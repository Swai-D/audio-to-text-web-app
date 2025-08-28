<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
        <!-- Header Section -->
        <div class="bg-white/80 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="p-2 bg-white/50 rounded-xl hover:bg-white/70 transition-all duration-300">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                {{ $transcript->title ?? 'Sermon Transcript' }}
                            </h1>
                            <p class="text-gray-600 text-sm">Created {{ $transcript->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('transcribe.edit', $transcript) }}" 
                           class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit</span>
                        </a>
                        
                        <form action="{{ route('transcribe.destroy', $transcript) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this transcript? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-300 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Success/Error Messages -->
            @if(session('ok'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('ok') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-red-800 font-medium mb-2">Please fix the following errors:</p>
                            <ul class="text-red-700 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Transcript Details -->
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Full Transcript -->
                    <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Full Transcript</h2>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('transcribe.pdf', $transcript) }}" 
                                   class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 text-sm flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>PDF</span>
                                </a>
                                <a href="{{ route('transcribe.docx', $transcript) }}" 
                                   class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 text-sm flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Word</span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-white/50 rounded-xl p-6 max-h-96 overflow-y-auto">
                            <div class="prose prose-sm max-w-none">
                                {!! nl2br(e($transcript->text ?? 'No transcript available.')) !!}
                            </div>
                        </div>
                    </div>

                    <!-- AI Summary -->
                    @if($transcript->summary)
                    <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800">AI Summary</h2>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">AI Generated</span>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-xl p-6 border border-green-200/50">
                            <div class="prose prose-sm max-w-none">
                                {!! nl2br(e($transcript->summary)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Transcript Info -->
                    <div class="bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/20 shadow-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Transcript Info</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Language</label>
                                <p class="text-gray-800">{{ $transcript->language === 'auto' ? 'Auto-detected' : ucfirst($transcript->language) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">File</label>
                                <p class="text-gray-800 text-sm">{{ basename($transcript->audio_path) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Created</label>
                                <p class="text-gray-800">{{ $transcript->created_at->format('F j, Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Updated</label>
                                <p class="text-gray-800">{{ $transcript->updated_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/20 shadow-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                        
                        <div class="space-y-3">
                            @if(!$transcript->summary)
                            <form action="{{ route('transcribe.summarize', $transcript) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                                    Generate AI Summary
                                </button>
                            </form>
                            @endif
                            
                            <a href="{{ route('transcribe.edit', $transcript) }}" 
                               class="block w-full px-4 py-3 bg-blue-500 text-white font-semibold rounded-xl hover:bg-blue-600 transition-all duration-300 text-center">
                                Edit Transcript
                            </a>
                            
                            <form action="{{ route('transcribe.destroy', $transcript) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this transcript? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-4 py-3 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition-all duration-300">
                                    Delete Transcript
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
