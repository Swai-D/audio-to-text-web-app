@props(['transcript'])

<div class="glass-card card-hover rounded-2xl p-6 border border-white/20 shadow-lg">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h4 class="font-semibold text-gray-800 text-lg mb-1">{{ $transcript->title ?? 'Audio File' }}</h4>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ $transcript->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div class="gradient-bg rounded-lg px-3 py-1 text-xs font-medium text-white">
            #{{ $transcript->id }}
        </div>
    </div>

    <!-- Preview -->
    <div class="mb-4">
        <p class="text-gray-600 text-sm leading-relaxed">
            {{ Str::limit($transcript->text, 150) }}
        </p>
    </div>

    <!-- Language Badge -->
    <div class="mb-4">
        @if($transcript->language === 'auto')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                🌍 Auto-detect
            </span>
        @elseif($transcript->language === 'sw')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                🇹🇿 Swahili
            </span>
        @elseif($transcript->language === 'en')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                🇺🇸 English
            </span>
        @endif
    </div>

    <!-- Actions -->
    <div class="space-y-3">
        <!-- Export Buttons -->
        <div class="flex space-x-2">
            <a href="{{ route('transcribe.download.pdf',$transcript) }}" 
               class="flex-1 bg-white/50 hover:bg-white/70 text-gray-700 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 flex items-center justify-center space-x-1 group">
                <svg class="h-4 w-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>PDF</span>
            </a>
            <a href="{{ route('transcribe.download.docx',$transcript) }}" 
               class="flex-1 bg-white/50 hover:bg-white/70 text-gray-700 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 flex items-center justify-center space-x-1 group">
                <svg class="h-4 w-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Word</span>
            </a>
        </div>

        <!-- Summarize Form -->
        <form action="{{ route('transcribe.summarize',$transcript) }}" method="post" class="space-y-2">
            @csrf
            <input name="style" 
                   class="w-full rounded-lg border-gray-300 bg-white/50 backdrop-blur-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                   placeholder="💡 e.g., bullet points, 5 key insights">
            <button type="submit" 
                    class="w-full gradient-button rounded-lg px-4 py-2 text-white font-medium text-sm transition-all duration-300 flex items-center justify-center space-x-2 hover:scale-105 transform">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <span>Summarize</span>
            </button>
        </form>

        <!-- Summary Display -->
        @if($transcript->summary)
            <div class="rounded-lg bg-gradient-to-r from-purple-50 to-blue-50 p-4 border border-purple-200">
                <div class="flex items-start space-x-2">
                    <svg class="h-5 w-5 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <div>
                        <h5 class="font-semibold text-purple-800 text-sm mb-1">AI Summary</h5>
                        <p class="text-purple-700 text-sm leading-relaxed">{{ Str::limit($transcript->summary, 200) }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
