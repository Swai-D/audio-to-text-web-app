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
    public function index()
    {
        // Get user-specific transcripts, ordered by latest first
        $items = Transcript::where('user_id', auth()->id())
                          ->latest()
                          ->paginate(10);
        
        return view('transcribe.index', compact('items'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,mp4,mpeg|max:307200', // 300MB for very long sermons
            'language' => 'nullable|in:auto,sw,en'
        ], [
            'audio.required' => 'Please select an audio file to transcribe.',
            'audio.file' => 'The uploaded file is not valid.',
            'audio.mimes' => 'Only MP3, WAV, M4A, and MP4 files are supported.',
            'audio.max' => 'File size must be less than 300MB.',
            'language.in' => 'Please select a valid language option.'
        ]);

        // Check if OpenAI API key is configured
        if (!config('services.openai.key')) {
            return back()->withErrors(['api' => 'OpenAI API key is not configured. Please contact administrator.']);
        }

        try {
            // Store the file with a unique name
            $originalName = $request->file('audio')->getClientOriginalName();
            $extension = $request->file('audio')->getClientOriginalExtension();
            $mimeType = $request->file('audio')->getMimeType();
            
            // Log file details for debugging
            \Log::info('File upload details', [
                'original_name' => $originalName,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'size' => $request->file('audio')->getSize()
            ]);
            
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $path = $request->file('audio')->storeAs('audio', $fileName, 'public');

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

            // Clean up the audio file after successful transcription (optional)
            // Storage::disk('public')->delete($path);

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

    public function summarize(Transcript $transcript, Request $request)
    {
        $request->validate([
            'style' => 'nullable|string|max:100' // e.g., "bullet points", "short sermon recap"
        ]);

        $style = $request->input('style', 'bullet points');

        try {
            $prompt = "Summarize the following sermon transcript into {$style}. " .
                      "The transcript may contain Swahili and English mixed. " .
                      "Keep key points, scripture references, and calls-to-action.\n\n---\n" . $transcript->text;

            $resp = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini', // or any available
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert sermon summarizer for mixed Swahili/English content.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.2,
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
        $html = view('transcribe.pdf', compact('transcript'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'transcript-' . $transcript->id . '-' . date('Y-m-d') . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function downloadDocx(Transcript $transcript)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        // Add title
        $section->addTitle($transcript->title ?? 'Sermon Transcript', 1);
        $section->addTextBreak(1);
        
        // Add metadata
        $section->addText('Generated on: ' . $transcript->created_at->format('F j, Y \a\t g:i A'), ['size' => 10, 'color' => '666666']);
        $section->addText('Language: ' . ($transcript->language === 'auto' ? 'Auto-detected' : ucfirst($transcript->language)), ['size' => 10, 'color' => '666666']);
        $section->addTextBreak(1);
        
        // Add transcript text
        $section->addTitle('Full Transcript', 2);
        $section->addText($transcript->text ?? 'No transcript available.');
        
        // Add summary if available
        if ($transcript->summary) {
            $section->addTextBreak(1);
            $section->addTitle('AI Summary', 2);
            $section->addText($transcript->summary);
        }
        
        $filename = 'transcript-' . $transcript->id . '-' . date('Y-m-d') . '.docx';
        $path = storage_path('app/public/' . $filename);
        $phpWord->save($path, 'Word2007', true);
        
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
