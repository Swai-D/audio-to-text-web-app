<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Transcript;
use Dompdf\Dompdf;
use PhpOffice\PhpWord\PhpWord;

class TranscriptionController extends Controller
{
    public function index(Request $request)
    {
        // Get user-specific transcripts, ordered by latest first
        $query = Transcript::where('user_id', auth()->id())->latest();
        
        // Check display mode
        $showMode = $request->get('show', 'one');
        
        if ($showMode === 'all') {
            $items = $query->get();
        } elseif ($showMode === 'paginated') {
            $items = $query->paginate(10);
        } else {
            // Show only one transcript at a time
            $items = $query->first();
        }
        
        // Get storage usage
        $storageUsage = $this->getStorageUsage();
        
        return view('transcribe.index', compact('items', 'storageUsage', 'showMode'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,mp4,mpeg,webm|max:307200', // 300MB for very long sermons
            'language' => 'nullable|in:auto,sw,en'
        ], [
            'audio.required' => 'Please select an audio file to transcribe.',
            'audio.file' => 'The uploaded file is not valid.',
            'audio.mimes' => 'Only MP3, WAV, M4A, MP4, and WEBM files are supported.',
            'audio.max' => 'File size must be less than 300MB.',
            'language.in' => 'Please select a valid language option.'
        ]);

        // Check if OpenAI API key is configured
        if (!config('services.openai.key')) {
            return back()->withErrors(['api' => 'OpenAI API key is not configured. Please contact administrator.']);
        }

        try {
            // Store the file with proper user folder structure
        $originalName = $request->file('audio')->getClientOriginalName();
        $extension = $request->file('audio')->getClientOriginalExtension();
            $mimeType = $request->file('audio')->getMimeType();
            $userId = auth()->id();
            
            // Create user-specific folder structure
            $userFolder = 'users/' . $userId . '/audio';
            $dateFolder = date('Y/m'); // Organize by year/month
            
            // Create clean filename (remove special characters)
            $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $fileName = date('Y-m-d_H-i-s') . '_' . $cleanName . '.' . $extension;
            
            // Full path: users/{user_id}/audio/{year}/{month}/{filename}
            $fullPath = $userFolder . '/' . $dateFolder . '/' . $fileName;
            
            // Log file details for debugging
            \Log::info('File upload details', [
                'user_id' => $userId,
                'original_name' => $originalName,
                'clean_name' => $cleanName,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'size' => $request->file('audio')->getSize(),
                'storage_path' => $fullPath
            ]);
            
            // Store file in user-specific folder
            $path = $request->file('audio')->storeAs($fullPath, $fileName, 'public');

            // Get file path for OpenAI API
        $file = Storage::disk('public')->path($path);
            $language = $request->input('language', 'auto');

            // Log the transcription attempt
            \Log::info('Starting transcription', [
                'user_id' => auth()->id(),
                'file_name' => $originalName,
                'file_size' => $request->file('audio')->getSize(),
                'language' => $language
            ]);

            // Call OpenAI Whisper API
            $response = Http::withToken(config('services.openai.key'))
                ->timeout(1800) // 30 minutes timeout for very long sermons
                ->asMultipart()
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    [
                        'name' => 'file',
                        'contents' => fopen($file, 'r'),
                        'filename' => basename($file),
                    ],
                    ['name' => 'model', 'contents' => 'whisper-1'],
                    ['name' => 'response_format', 'contents' => 'json'],
                    ['name' => 'temperature', 'contents' => '0'],
                ]);

            if (!$response->ok()) {
                $errorBody = $response->body();
                \Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $errorBody
                ]);
                
                // Handle specific API errors
                if ($response->status() === 401) {
                    throw new \Exception('OpenAI API key is invalid or expired.');
                } elseif ($response->status() === 413) {
                    throw new \Exception('File is too large for OpenAI API.');
                } elseif ($response->status() === 429) {
                    throw new \Exception('Rate limit exceeded. Please try again later.');
                } else {
                    throw new \Exception('OpenAI API error: ' . $errorBody);
                }
            }

            $json = $response->json();
            $text = $json['text'] ?? null;

            if (empty($text)) {
                throw new \Exception('No text was transcribed from the audio file.');
            }

            // Create transcript record
            $transcript = Transcript::create([
                'user_id'   => auth()->id(),
                'title'     => $originalName,
                'audio_path'=> $path,
                'language'  => $language,
                'text'      => $text,
                'meta'      => [
                    'provider' => 'openai',
                    'model' => 'whisper-1',
                    'file_size' => $request->file('audio')->getSize(),
                    'duration' => $json['duration'] ?? null,
                    'language_detected' => $json['language'] ?? null
                ],
            ]);

            // Log successful transcription
            \Log::info('Transcription completed successfully', [
                'transcript_id' => $transcript->id,
                'text_length' => strlen($text),
                'user_id' => auth()->id()
            ]);

            // Clean up old audio files (keep only last 10 files per user)
            $this->cleanupOldFiles($userId);

            return redirect()->route('home')
                ->with('ok', 'Transcription completed successfully! Your sermon has been transcribed.')
                ->with('t_id', $transcript->id);

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error('Transcription failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up uploaded file if it exists
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            // Return user-friendly error messages
            if (str_contains($e->getMessage(), 'OpenAI API key')) {
                return back()->withErrors(['api' => 'OpenAI API key is invalid or missing. Please check your configuration.']);
            } elseif (str_contains($e->getMessage(), 'too large')) {
                return back()->withErrors(['api' => 'File is too large. Maximum size is 300MB.']);
            } elseif (str_contains($e->getMessage(), 'Rate limit')) {
                return back()->withErrors(['api' => 'Rate limit exceeded. Please try again in a few minutes.']);
            } elseif (str_contains($e->getMessage(), 'No text was transcribed')) {
                return back()->withErrors(['api' => 'No speech was detected in the audio file. Please check your audio file.']);
            } elseif (str_contains($e->getMessage(), 'cURL') || str_contains($e->getMessage(), 'network')) {
                return back()->withErrors(['api' => 'Network error. Please check your internet connection and try again.']);
            } else {
                return back()->withErrors(['api' => 'Transcription failed: ' . $e->getMessage()]);
            }
        }
    }

    /**
     * Clean up old audio files for a user
     */
    private function cleanupOldFiles($userId)
    {
        try {
            $userFolder = 'users/' . $userId . '/audio';
            $disk = Storage::disk('public');
            
            // Get all audio files for this user
            $files = collect($disk->allFiles($userFolder))
                ->filter(function ($file) {
                    return in_array(pathinfo($file, PATHINFO_EXTENSION), ['mp3', 'wav', 'm4a', 'mp4']);
                })
                ->sortByDesc(function ($file) {
                    return $disk->lastModified($file);
                });
            
            // Keep only the last 10 files
            if ($files->count() > 10) {
                $filesToDelete = $files->slice(10);
                
                foreach ($filesToDelete as $file) {
                    $disk->delete($file);
                    \Log::info('Deleted old audio file', ['file' => $file, 'user_id' => $userId]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error cleaning up old files', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user's storage usage
     */
    public function getStorageUsage()
    {
        $userId = auth()->id();
        $userFolder = 'users/' . $userId . '/audio';
        $disk = Storage::disk('public');
        
        $totalSize = 0;
        $fileCount = 0;
        
        if ($disk->exists($userFolder)) {
            $files = $disk->allFiles($userFolder);
            $fileCount = count($files);
            
            foreach ($files as $file) {
                $totalSize += $disk->size($file);
            }
        }
        
        return [
            'total_size' => $totalSize,
            'file_count' => $fileCount,
            'formatted_size' => $this->formatBytes($totalSize)
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function summarize(Transcript $transcript, Request $request)
    {
        $request->validate([
            'style' => 'nullable|string|max:100' // e.g., "bullet points", "short sermon recap"
        ]);

        $style = $request->input('style', 'bullet points');

        try {
            $prompt = "Create a clean, well-structured summary of the following sermon transcript. 

IMPORTANT FORMATTING REQUIREMENTS:
- Use clear, readable formatting without bold text (**)
- Do NOT use section headings like 'Introduction', 'Main Theme', 'Conclusion'
- Just use simple bullet points for all content
- Keep line spacing compact (not too much space)
- Include scripture references if mentioned
- Make it concise and easy to read
- Use minimal spacing between points

LANGUAGE REQUIREMENT:
- If the transcript is primarily in Swahili, write the summary in Swahili
- If the transcript is primarily in English, write the summary in English
- If it's mixed, use the dominant language

SERMON TRANSCRIPT:
" . $transcript->text;

            $resp = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                           ['role' => 'system', 'content' => 'You are an expert sermon summarizer who creates clean, simple summaries. Use only bullet points, no section headings. Match the language of the transcript (Swahili for Swahili, English for English). Make summaries concise and easy to read.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.3,
                ]);

            if (!$resp->ok()) throw new \Exception($resp->body());

            $summary = $resp->json('choices.0.message.content');
            $transcript->update(['summary' => $summary]);

            return back()->with('ok', 'Summary generated successfully! Your sermon has been summarized.');
        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error('Summarization failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's an API key issue
            if (str_contains($e->getMessage(), '401') || str_contains($e->getMessage(), 'unauthorized')) {
                return back()->withErrors(['api' => 'OpenAI API key is invalid or missing. Please check your configuration.']);
            }
            
            // Check if it's a network issue
            if (str_contains($e->getMessage(), 'cURL') || str_contains($e->getMessage(), 'network')) {
                return back()->withErrors(['api' => 'Network error. Please check your internet connection and try again.']);
            }
            
            return back()->withErrors(['api' => 'Summarization failed: ' . $e->getMessage()]);
        }
    }

    public function downloadPdf(Transcript $transcript)
    {
        try {
        $html = view('transcribe.pdf', compact('transcript'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
            $filename = 'transcript-' . $transcript->id . '-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
        } catch (\Exception $e) {
            \Log::error('PDF export failed: ' . $e->getMessage(), [
                'transcript_id' => $transcript->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['export' => 'Failed to generate PDF file: ' . $e->getMessage()]);
        }
    }

    public function downloadDocx(Transcript $transcript)
    {
        try {
            // Check if zip extension is available
            if (!extension_loaded('zip')) {
                return back()->withErrors(['export' => 'ZIP extension is required for DOCX export. Please enable zip extension in php.ini file.']);
            }
            
            // Check if PhpWord class exists
            if (!class_exists('PhpOffice\PhpWord\PhpWord')) {
                return back()->withErrors(['export' => 'PhpWord library not found. Please install: composer require phpoffice/phpword']);
            }
            
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            
            // Set document properties
            $properties = $phpWord->getDocInfo();
            $properties->setCreator('Sermon Transcriber');
            $properties->setTitle($transcript->title ?? 'Sermon Transcript');
            $properties->setDescription('AI-generated sermon transcript');
            $properties->setCreated(time());
            
        $section = $phpWord->addSection();
        
            // Add title with better styling
            $titleStyle = [
                'name' => 'Arial',
                'size' => 18,
                'bold' => true,
                'color' => '2E5BBA'
            ];
            $section->addText($transcript->title ?? 'Sermon Transcript', $titleStyle);
            $section->addTextBreak(2);
            
            // Add metadata with styling
            $metaStyle = [
                'name' => 'Arial',
                'size' => 10,
                'color' => '666666'
            ];
            $section->addText('Generated on: ' . $transcript->created_at->format('F j, Y \a\t g:i A'), $metaStyle);
            $section->addText('Language: ' . ($transcript->language === 'auto' ? 'Auto-detected' : ucfirst($transcript->language)), $metaStyle);
            $section->addText('File: ' . basename($transcript->audio_path), $metaStyle);
            $section->addTextBreak(2);
            
            // Add transcript text with better formatting
            $sectionTitleStyle = [
                'name' => 'Arial',
                'size' => 14,
                'bold' => true,
                'color' => '2E5BBA'
            ];
            $section->addText('Full Transcript', $sectionTitleStyle);
        $section->addTextBreak(1);
        
            // Format transcript text with proper paragraphs
            $textStyle = [
                'name' => 'Arial',
                'size' => 11,
                'color' => '000000'
            ];
            
            $transcriptText = $transcript->text ?? 'No transcript available.';
            $paragraphs = explode("\n", $transcriptText);
            
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    $section->addText($paragraph, $textStyle);
        $section->addTextBreak(1);
                }
            }
        
        // Add summary if available
        if ($transcript->summary) {
                $section->addTextBreak(2);
                $section->addText('AI Summary', $sectionTitleStyle);
                $section->addTextBreak(1);
                
                $summaryStyle = [
                    'name' => 'Arial',
                    'size' => 11,
                    'color' => '059669'
                ];
                
                $summaryText = $transcript->summary;
                $summaryParagraphs = explode("\n", $summaryText);
                
                foreach ($summaryParagraphs as $paragraph) {
                    $paragraph = trim($paragraph);
                    if (!empty($paragraph)) {
                        $section->addText($paragraph, $summaryStyle);
            $section->addTextBreak(1);
                    }
                }
            }
            
            // Generate filename and save
            $filename = 'transcript-' . $transcript->id . '-' . date('Y-m-d-H-i-s') . '.docx';
            $path = storage_path('app/temp/' . $filename);
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $phpWord->save($path, 'Word2007');
            
            // Return file for download
            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('DOCX export failed: ' . $e->getMessage(), [
                'transcript_id' => $transcript->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['export' => 'Failed to generate DOCX file: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Download transcript as TXT file (fallback when DOCX is not available)
     */
    private function downloadTxt(Transcript $transcript)
    {
        try {
            $content = "SERMON TRANSCRIPT\n";
            $content .= "==================\n\n";
            $content .= "Title: " . ($transcript->title ?? 'Sermon Transcript') . "\n";
            $content .= "Generated on: " . $transcript->created_at->format('F j, Y \a\t g:i A') . "\n";
            $content .= "Language: " . ($transcript->language === 'auto' ? 'Auto-detected' : ucfirst($transcript->language)) . "\n";
            $content .= "File: " . basename($transcript->audio_path) . "\n\n";
            $content .= "FULL TRANSCRIPT\n";
            $content .= "===============\n\n";
            $content .= $transcript->text ?? 'No transcript available.' . "\n\n";
            
            if ($transcript->summary) {
                $content .= "AI SUMMARY\n";
                $content .= "==========\n\n";
                $content .= $transcript->summary . "\n\n";
            }
            
            $content .= "Generated by Sermon Transcriber - AI-Powered Transcription Service\n";
            $content .= "Document created on " . now()->format('F j, Y \a\t g:i A') . "\n";
            
            $filename = 'transcript-' . $transcript->id . '-' . date('Y-m-d-H-i-s') . '.txt';
            
            return response($content, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('TXT export failed: ' . $e->getMessage(), [
                'transcript_id' => $transcript->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['export' => 'Failed to generate export file: ' . $e->getMessage()]);
        }
    }
}
