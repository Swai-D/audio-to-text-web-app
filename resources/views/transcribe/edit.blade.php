<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
        <!-- Header Section -->
        <div class="bg-white/80 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('transcribe.show', $transcript) }}" class="p-2 bg-white/50 rounded-xl hover:bg-white/70 transition-all duration-300">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                Edit Transcript
                            </h1>
                            <p class="text-gray-600 text-sm">{{ $transcript->title ?? 'Sermon Transcript' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('transcribe.show', $transcript) }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300">
                            Cancel
                        </a>
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

            <!-- Edit Form -->
            <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-xl">
                <form action="{{ route('transcribe.update', $transcript) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Transcript Title</label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title', $transcript->title) }}"
                                   class="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Enter transcript title">
                        </div>

                        <!-- Full Transcript -->
                        <div>
                            <label for="text" class="block text-sm font-medium text-gray-700 mb-2">Full Transcript</label>
                            <textarea name="text" 
                                      id="text" 
                                      rows="12"
                                      class="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                      placeholder="Enter or edit the full transcript text">{{ old('text', $transcript->text) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">You can edit the transcript text here. Changes will be saved when you submit the form.</p>
                        </div>

                        <!-- AI Summary -->
                        @if($transcript->summary)
                        <div>
                            <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">AI Summary</label>
                            <textarea name="summary" 
                                      id="summary" 
                                      rows="8"
                                      class="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                      placeholder="Edit the AI summary">{{ old('summary', $transcript->summary) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">You can edit the AI-generated summary here.</p>
                        </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <p>Last updated: {{ $transcript->updated_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('transcribe.show', $transcript) }}" 
                                   class="px-6 py-3 bg-gray-500 text-white font-semibold rounded-xl hover:bg-gray-600 transition-all duration-300">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Transcript Info -->
            <div class="mt-8 bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/20 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Transcript Information</h3>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Original File</label>
                        <p class="text-gray-800">{{ basename($transcript->audio_path) }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Language</label>
                        <p class="text-gray-800">{{ $transcript->language === 'auto' ? 'Auto-detected' : ucfirst($transcript->language) }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Created</label>
                        <p class="text-gray-800">{{ $transcript->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Last Updated</label>
                        <p class="text-gray-800">{{ $transcript->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-resize textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
</x-app-layout>
