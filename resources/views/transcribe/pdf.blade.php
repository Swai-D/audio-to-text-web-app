<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $transcript->title }} - Transcript</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .metadata {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .metadata-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .metadata-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .metadata-value {
            color: #1f2937;
            font-weight: 500;
        }
        
        .content-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .transcript-text {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            line-height: 1.8;
            font-size: 1rem;
            color: #374151;
        }
        
        .summary-section {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .summary-title {
            font-weight: 600;
            color: #166534;
            margin-bottom: 0.5rem;
        }
        
        .summary-text {
            color: #15803d;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                font-size: 12pt;
            }
            
            .header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .metadata {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .summary-section {
                background: #f0fdf4 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sermon Transcript</h1>
        <p>{{ $transcript->title }}</p>
    </div>
    
    <div class="container">
        <!-- Metadata -->
        <div class="metadata">
            <div class="metadata-grid">
                <div class="metadata-item">
                    <span class="metadata-label">Title:</span>
                    <span class="metadata-value">{{ $transcript->title }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">Language:</span>
                    <span class="metadata-value">{{ ucfirst($transcript->language) }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">Date:</span>
                    <span class="metadata-value">{{ $transcript->created_at->format('F j, Y') }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">Time:</span>
                    <span class="metadata-value">{{ $transcript->created_at->format('g:i A') }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">Words:</span>
                    <span class="metadata-value">{{ str_word_count($transcript->text) }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">Characters:</span>
                    <span class="metadata-value">{{ strlen($transcript->text) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Transcript Content -->
        <div class="content-section">
            <h2 class="section-title">Full Transcript</h2>
            <div class="transcript-text">
                {!! nl2br(e($transcript->text)) !!}
            </div>
        </div>
        
        <!-- Summary (if available) -->
        @if($transcript->summary)
            <div class="content-section">
                <h2 class="section-title">AI Summary</h2>
                <div class="summary-section">
                    <div class="summary-title">Key Points & Summary</div>
                    <div class="summary-text">
                        {!! nl2br(e($transcript->summary)) !!}
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Generated by Sermon Transcriber - AI-Powered Transcription Service</p>
            <p>Document created on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>
