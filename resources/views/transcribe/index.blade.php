<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
        <!-- Header Section -->
        <div class="bg-white/80 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                            Sermon Transcriber
                        </h1>
                        <p class="text-gray-600 mt-1">Transform your audio sermons into text with AI</p>
                    </div>
                                         <div class="flex items-center space-x-4">
                         <!-- Storage Info -->
                         <div class="flex items-center space-x-3 bg-white/50 rounded-xl px-4 py-2">
                             <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                             </svg>
                             <div class="text-xs">
                                 <div class="text-gray-700 font-medium">{{ $storageUsage['formatted_size'] }}</div>
                                 <div class="text-gray-500">{{ $storageUsage['file_count'] }} files</div>
                                 @php
                                     $usagePercent = min(100, ($storageUsage['total_size'] / (300 * 1024 * 1024)) * 100); // 300MB limit
                                     $colorClass = $usagePercent > 80 ? 'bg-red-500' : ($usagePercent > 60 ? 'bg-yellow-500' : 'bg-green-500');
                                 @endphp
                                 <div class="w-16 bg-gray-200 rounded-full h-1 mt-1">
                                     <div class="{{ $colorClass }} h-1 rounded-full" style="width: {{ $usagePercent }}%"></div>
                                 </div>
                             </div>
                         </div>
                         
                         <span class="text-sm text-gray-500">Welcome, {{ Auth::user()->name }}</span>
                         <a href="{{ route('profile.edit') }}" class="p-2 bg-white/50 rounded-xl hover:bg-white/70 transition-all duration-300">
                             <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                             </svg>
                         </a>
                         <a href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="p-2 bg-red-500/10 rounded-xl hover:bg-red-500/20 transition-all duration-300">
                             <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                             </svg>
                         </a>
                     </div>
                    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
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

        <!-- Upload Section -->
            <div class="mb-12">
                <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-xl">
            <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Upload Your Sermon</h2>
                        <p class="text-gray-600">Choose to upload an audio file or record directly</p>
            </div>

                    <!-- Upload Options -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- File Upload -->
                        <div class="bg-gradient-to-br from-purple-500/10 to-blue-500/10 rounded-2xl p-6 border border-purple-200/50 hover:border-purple-300/50 transition-all duration-300">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Upload Audio File</h3>
                                <p class="text-gray-600 mb-4">Drag and drop or click to select audio files</p>
                                
                                <form action="{{ route('transcribe.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="upload-form">
                @csrf
                                    <div class="relative">
                                        <input type="file" 
                                               name="audio" 
                                               id="audio-file"
                                               accept=".mp3,.wav,.m4a,.mp4,.webm"
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                               required>
                                        <div class="border-2 border-dashed border-purple-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors duration-300" id="drop-zone">
                                            <svg class="w-8 h-8 text-purple-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                                            <p class="text-gray-600" id="file-text">Click to select or drag files here</p>
                                                                                         <p class="text-xs text-gray-500 mt-1">MP3, WAV, M4A, MP4, WEBM (max 300MB)</p>
                </div>
            </div>
                                    
                                    <div>
                                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                        <select name="language" id="language" class="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="auto">Auto-detect (Recommended)</option>
                                            <option value="en">English</option>
                                            <option value="sw">Swahili</option>
                        </select>
            </div>

                                    <button type="submit" 
                                            id="submit-btn"
                                            class="w-full py-3 px-6 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span id="submit-text">Upload & Transcribe</span>
                                        <span id="loading-text" class="hidden">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Processing...
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Record Audio -->
                        <div class="bg-gradient-to-br from-green-500/10 to-teal-500/10 rounded-2xl p-6 border border-green-200/50 hover:border-green-300/50 transition-all duration-300">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Record Audio</h3>
                                <p class="text-gray-600 mb-4">Record your sermon directly in the browser</p>
                                
                                <div class="space-y-4">
                                    <div class="border-2 border-dashed border-green-300 rounded-xl p-6 text-center">
                                        <div id="recording-status" class="text-gray-600 mb-4">
                                            <p>Click to start recording</p>
                                        </div>
                                        <div id="recording-timer" class="text-2xl font-bold text-green-600 mb-4 hidden">00:00</div>
                                        
                                        <!-- Audio Preview -->
                                        <div id="audio-preview" class="mb-4 hidden">
                                            <audio id="recorded-audio" controls class="w-full">
                                                Your browser does not support the audio element.
                                            </audio>
                    </div>
                    
                                        <button id="record-btn" 
                                                class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center text-white hover:from-green-600 hover:to-teal-600 transition-all duration-300 transform hover:scale-110 shadow-lg">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                                        </button>
                        </div>
                                    
                                    <!-- Recording Controls -->
                                    <div class="flex space-x-2">
                                        <button id="download-recording" 
                                                class="flex-1 py-2 px-4 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-all duration-300 hidden">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </button>
                                        
                                        <button id="save-recording" 
                                                class="flex-1 py-2 px-4 bg-gradient-to-r from-green-500 to-teal-500 text-white font-semibold rounded-lg hover:from-green-600 hover:to-teal-600 transition-all duration-300 hidden">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            Transcribe
                    </button>
                </div>
                                    
                                    <!-- Recording Form -->
                                    <form id="recording-form" action="{{ route('transcribe.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
                                        @csrf
                                        <input type="hidden" name="language" value="auto">
                                        <input type="file" name="audio" id="recording-file" accept="audio/*" class="hidden">
            </form>
        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transcripts Section -->
            <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-xl">
                                 <div class="flex items-center justify-between mb-8">
                     <div>
                         <h2 class="text-2xl font-bold text-gray-800">Your Transcripts</h2>
                         <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                             @if($showMode === 'one')
                                 <span>{{ $items ? '1' : '0' }} transcript{{ $items ? '' : 's' }} showing</span>
                             @else
                                 <span>{{ $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : $items->count() }} transcript{{ ($items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : $items->count()) !== 1 ? 's' : '' }} total</span>
                                 @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                     <span>•</span>
                                     <span>{{ $items->count() }} showing</span>
                                 @endif
                             @endif
                             <span>•</span>
                             <span>{{ $storageUsage['file_count'] }} audio files</span>
                             <span>•</span>
                             <span>{{ $storageUsage['formatted_size'] }} used</span>
                         </div>
                     </div>
                     
                                           <!-- Display Options -->
                      <div class="flex items-center space-x-2">
                          <a href="{{ request()->fullUrlWithQuery(['show' => 'one']) }}" 
                             class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors {{ !request('show') || request('show') === 'one' ? 'bg-green-200' : '' }}">
                              One at a Time
                          </a>
                          <a href="{{ request()->fullUrlWithQuery(['show' => 'paginated']) }}" 
                             class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors {{ request('show') === 'paginated' ? 'bg-blue-200' : '' }}">
                              Paginated
                          </a>
                          <a href="{{ request()->fullUrlWithQuery(['show' => 'all']) }}" 
                             class="px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-full hover:bg-purple-200 transition-colors {{ request('show') === 'all' ? 'bg-purple-200' : '' }}">
                              Show All
                          </a>
                      </div>
                 </div>

                                 @if($showMode === 'one' && $items)
                     <!-- Single Transcript Display -->
                     <div class="bg-white/80 backdrop-blur-md rounded-2xl p-6 border border-white/40 hover:border-purple-200/60 transition-all duration-300 hover:shadow-lg">
                         <div class="flex items-start justify-between">
                             <div class="flex-1">
                                 <div class="flex items-center space-x-3 mb-3">
                                     <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                                         <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                         </svg>
                                     </div>
                                     <div>
                                         <h3 class="text-lg font-semibold text-gray-800">{{ $items->title }}</h3>
                                         <p class="text-sm text-gray-500">{{ $items->created_at->format('M j, Y \a\t g:i A') }}</p>
                                         <p class="text-xs text-gray-400 mt-1">
                                             <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                             </svg>
                                             {{ str_replace('users/' . auth()->id() . '/audio/', '', $items->audio_path) }}
                                         </p>
                                     </div>
                                 </div>
                                 
                                 <div class="mb-4">
                                     <p class="text-gray-700 line-clamp-3">{{ Str::limit($items->text, 200) }}</p>
                                 </div>
                                 
                                 <div class="flex items-center space-x-2 mb-4">
                                     <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                         {{ ucfirst($items->language) }}
                                     </span>
                                     @if($items->summary)
                                         <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                             Summarized
                                         </span>
                                     @endif
                                 </div>
                             </div>
                             
                             <div class="flex flex-col space-y-2 ml-4">
                                 @if(!$items->summary)
                                     <form action="{{ route('transcribe.summarize', $items) }}" method="POST" class="inline">
                                         @csrf
                                         <button type="submit" 
                                                 class="p-2 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl hover:from-green-600 hover:to-teal-600 transition-all duration-300 transform hover:scale-105">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                             </svg>
                                         </button>
            </form>
                                 @endif
                                 
                                 <a href="{{ route('transcribe.pdf', $items) }}" 
                                    class="p-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                     </svg>
                                 </a>
                                 
                                                                   <a href="{{ route('transcribe.docx', $items) }}" 
                                     class="p-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 transform hover:scale-105"
                                     title="{{ !extension_loaded('zip') ? 'Will download as TXT (ZIP extension not available)' : 'Download as DOCX' }}">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                      </svg>
                                  </a>
                                 
                                 <a href="{{ route('transcribe.edit', $items) }}" 
                                    class="p-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl hover:from-yellow-600 hover:to-orange-600 transition-all duration-300 transform hover:scale-105"
                                    title="Edit transcript">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                     </svg>
                                 </a>
                                 
                                 <button type="button" 
                                         class="p-2 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-xl hover:from-red-600 hover:to-rose-600 transition-all duration-300 transform hover:scale-105 delete-btn"
                                         data-transcript-id="{{ $items->id }}"
                                         data-transcript-title="{{ $items->title ?? 'Untitled Transcript' }}"
                                         title="Delete transcript">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                     </svg>
                                 </button>
                             </div>
                         </div>
                         
                         @if($items->summary)
                             <div class="mt-4 p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm">
                                 <div class="flex items-center mb-4">
                                     <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                         <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                         </svg>
                                     </div>
                                     <h4 class="font-bold text-green-800 text-lg">AI Summary</h4>
                                 </div>
                                 <div class="prose prose-sm max-w-none text-green-700 leading-relaxed summary-content">
                                     {!! nl2br(e($items->summary)) !!}
                                 </div>
                             </div>
                         @endif
                     </div>
                 @elseif($items->count() > 0)
                     <div class="grid gap-6">
                         @foreach($items as $transcript)
                            <div class="bg-white/80 backdrop-blur-md rounded-2xl p-6 border border-white/40 hover:border-purple-200/60 transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                                                                         <div>
                                                 <h3 class="text-lg font-semibold text-gray-800">{{ $transcript->title }}</h3>
                                                 <p class="text-sm text-gray-500">{{ $transcript->created_at->format('M j, Y \a\t g:i A') }}</p>
                                                 <p class="text-xs text-gray-400 mt-1">
                                                     <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                     </svg>
                                                     {{ str_replace('users/' . auth()->id() . '/audio/', '', $transcript->audio_path) }}
                                                 </p>
                                             </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <p class="text-gray-700 line-clamp-3">{{ Str::limit($transcript->text, 200) }}</p>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2 mb-4">
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                                {{ ucfirst($transcript->language) }}
                                            </span>
                                            @if($transcript->summary)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                    Summarized
                                                </span>
                                            @endif
                                        </div>
        </div>

                                    <div class="flex flex-col space-y-2 ml-4">
                                        @if(!$transcript->summary)
                                            <form action="{{ route('transcribe.summarize', $transcript) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-2 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl hover:from-green-600 hover:to-teal-600 transition-all duration-300 transform hover:scale-105">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('transcribe.pdf', $transcript) }}" 
                                           class="p-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        
                                                                                 <a href="{{ route('transcribe.docx', $transcript) }}" 
                                            class="p-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 transform hover:scale-105"
                                            title="{{ !extension_loaded('zip') ? 'Will download as TXT (ZIP extension not available)' : 'Download as DOCX' }}">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                             </svg>
                                         </a>
                                         
                                         <a href="{{ route('transcribe.edit', $transcript) }}" 
                                            class="p-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl hover:from-yellow-600 hover:to-orange-600 transition-all duration-300 transform hover:scale-105"
                                            title="Edit transcript">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                             </svg>
                                         </a>
                                         
                                         <button type="button" 
                                                 class="p-2 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-xl hover:from-red-600 hover:to-rose-600 transition-all duration-300 transform hover:scale-105 delete-btn"
                                                 data-transcript-id="{{ $transcript->id }}"
                                                 data-transcript-title="{{ $transcript->title ?? 'Untitled Transcript' }}"
                                                 title="Delete transcript">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                             </svg>
                                         </button>
                                    </div>
                </div>

                                @if($transcript->summary)
                                    <div class="mt-4 p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm">
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-green-800 text-lg">AI Summary</h4>
                                        </div>
                                        <div class="prose prose-sm max-w-none text-green-700 leading-relaxed summary-content">
                                            {!! nl2br(e($transcript->summary)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                    @endforeach
                </div>
                     
                     <!-- Pagination -->
                     @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
                         <div class="mt-8 flex justify-center">
                             <div class="bg-white/60 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                 {{ $items->links() }}
                             </div>
                         </div>
                     @endif
                 @elseif($showMode === 'one' && !$items)
                     <div class="text-center py-12">
                         <div class="w-24 h-24 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                             <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                             </svg>
                         </div>
                         <h3 class="text-xl font-semibold text-gray-800 mb-2">No transcripts yet</h3>
                         <p class="text-gray-600">Upload your first sermon to get started!</p>
            </div>
                 @elseif($items->count() === 0)
                     <div class="text-center py-12">
                         <div class="w-24 h-24 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                             <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No transcripts yet</h3>
                         <p class="text-gray-600">Upload your first sermon to get started!</p>
            </div>
        @endif
    </div>
        </div>
    </div>

    <!-- Custom CSS for Summary Formatting -->
    <style>
        .summary-content {
            line-height: 1.4;
        }
        .summary-content h1, .summary-content h2, .summary-content h3 {
            color: #059669;
            font-weight: 600;
            margin-top: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .summary-content ul, .summary-content ol {
            margin-left: 1.5rem;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .summary-content li {
            margin-bottom: 0.1rem;
        }
        .summary-content strong, .summary-content b {
            color: #047857;
            font-weight: 600;
        }
        .summary-content p {
            margin-bottom: 0.5rem;
        }
    </style>

    <!-- Custom Notification System -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- File Upload & Recording JavaScript -->
    <script>
        // Custom Notification System
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            
            const colors = {
                success: 'bg-green-50 border-green-200 text-green-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            };
            
            const icons = {
                success: `<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`,
                error: `<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`,
                warning: `<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>`,
                info: `<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`
            };
            
            notification.className = `flex items-center p-4 rounded-xl border backdrop-blur-md shadow-lg transform transition-all duration-300 ${colors[type]} opacity-0 translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    ${icons[type]}
                    <p class="font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('opacity-0', 'translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }

        // File Upload Enhancement
        const audioFile = document.getElementById('audio-file');
        const dropZone = document.getElementById('drop-zone');
        const fileText = document.getElementById('file-text');
        const uploadForm = document.getElementById('upload-form');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const loadingText = document.getElementById('loading-text');

                 // File selection handling
         audioFile.addEventListener('change', function(e) {
             const file = e.target.files[0];
             if (file) {
                 // Debug file info
                 console.log('File selected:', {
                     name: file.name,
                     type: file.type,
                     size: file.size,
                     extension: file.name.split('.').pop().toLowerCase()
                 });
                 
                 // Validate file size (300MB = 300 * 1024 * 1024 bytes)
                 if (file.size > 300 * 1024 * 1024) {
                     showNotification('File size must be less than 300MB', 'error');
                     this.value = '';
                     return;
                 }

                 // Validate file type - check both MIME type and extension
                 const allowedTypes = ['audio/mp3', 'audio/wav', 'audio/m4a', 'audio/mp4', 'video/mp4', 'audio/mpeg', 'audio/webm'];
                 const allowedExtensions = ['mp3', 'wav', 'm4a', 'mp4'];
                 const fileExtension = file.name.split('.').pop().toLowerCase();
                 
                 if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                     showNotification(`File type not supported. Got: ${file.type} (${fileExtension}). Please select MP3, WAV, M4A, or MP4 files.`, 'error');
                     this.value = '';
                     return;
                 }

                                 // Update UI
                 fileText.textContent = `Selected: ${file.name}`;
                 dropZone.classList.add('border-green-400', 'bg-green-50');
                 dropZone.classList.remove('border-purple-300');
                 
                 // Show success notification
                 showNotification(`File "${file.name}" selected successfully!`, 'success');
            }
        });

        // Drag and drop functionality
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-400', 'bg-purple-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-400', 'bg-purple-50');
        });

                 dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
             this.classList.remove('border-purple-400', 'bg-purple-50');
             
             const files = e.dataTransfer.files;
             if (files.length > 0) {
                 audioFile.files = files;
                 audioFile.dispatchEvent(new Event('change'));
                 showNotification('File dropped successfully!', 'success');
             }
         });

                 // Form submission handling
         uploadForm.addEventListener('submit', function(e) {
             if (!audioFile.files[0]) {
                e.preventDefault();
                 showNotification('Please select an audio file', 'warning');
                 return;
             }

            // Show loading state
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loadingText.classList.remove('hidden');
        });

        // Recording functionality
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;
        let recordingTimer;
        let recordedBlob = null;

        const recordBtn = document.getElementById('record-btn');
        const recordingStatus = document.getElementById('recording-status');
        const recordingTimerEl = document.getElementById('recording-timer');
        const saveRecordingBtn = document.getElementById('save-recording');
        const audioPreview = document.getElementById('audio-preview');
        const recordedAudio = document.getElementById('recorded-audio');
        const recordingForm = document.getElementById('recording-form');
        const recordingFileInput = document.getElementById('recording-file');
        const downloadRecordingBtn = document.getElementById('download-recording');

        recordBtn.addEventListener('click', async () => {
            if (!isRecording) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        audio: {
                            echoCancellation: true,
                            noiseSuppression: true,
                            sampleRate: 44100
                        } 
                    });
                    
                    mediaRecorder = new MediaRecorder(stream, {
                        mimeType: 'audio/webm;codecs=opus'
                    });
                    audioChunks = [];

                    mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onstop = () => {
                        recordedBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        recordedAudio.src = URL.createObjectURL(recordedBlob);
                        audioPreview.classList.remove('hidden');
                        saveRecordingBtn.classList.remove('hidden');
                        downloadRecordingBtn.classList.remove('hidden');
                        
                        // Stop all tracks
                        stream.getTracks().forEach(track => track.stop());
                        
                        showNotification('Recording completed! You can now transcribe or download.', 'success');
                    };

                    mediaRecorder.start(1000); // Collect data every second
                    isRecording = true;
                    
                    // Change button to stop icon
                    recordBtn.innerHTML = `
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        </svg>
                    `;
                    recordBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                    recordBtn.classList.remove('bg-gradient-to-r', 'from-green-500', 'to-teal-500', 'hover:from-green-600', 'hover:to-teal-600');
                    
                    recordingStatus.innerHTML = '<p class="text-red-600 font-medium">Recording... Click to stop</p>';
                    recordingTimerEl.classList.remove('hidden');
                    
                    showNotification('Recording started! Click the stop button when finished.', 'info');
                    
                    // Start timer
                    let seconds = 0;
                    recordingTimer = setInterval(() => {
                        seconds++;
                        const mins = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        recordingTimerEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    }, 1000);
                    
                } catch (err) {
                    console.error('Error accessing microphone:', err);
                    showNotification('Unable to access microphone. Please check browser permissions and try again.', 'error');
                }
            } else {
                // Stop recording
                mediaRecorder.stop();
                isRecording = false;
                clearInterval(recordingTimer);
                
                // Reset button to record icon
                recordBtn.innerHTML = `
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                `;
                recordBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                recordBtn.classList.add('bg-gradient-to-r', 'from-green-500', 'to-teal-500', 'hover:from-green-600', 'hover:to-teal-600');
                
                recordingStatus.innerHTML = '<p class="text-green-600 font-medium">Recording saved!</p>';
            }
        });

        // Transcribe recording
        saveRecordingBtn.addEventListener('click', () => {
            if (recordedBlob) {
                // Create a File object from the blob
                const file = new File([recordedBlob], 'recorded_sermon.webm', { 
                    type: 'audio/webm',
                    lastModified: Date.now()
                });
                
                // Create a new FileList-like object
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                recordingFileInput.files = dataTransfer.files;
                
                // Show loading state
                saveRecordingBtn.disabled = true;
                saveRecordingBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                
                showNotification('Uploading and transcribing your recording...', 'info');
                recordingForm.submit();
            } else {
                showNotification('No recording available. Please record audio first.', 'warning');
            }
        });

        // Download recording
        downloadRecordingBtn.addEventListener('click', () => {
            if (recordedBlob) {
                const url = URL.createObjectURL(recordedBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `sermon_recording_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.webm`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                showNotification('Recording downloaded successfully!', 'success');
            } else {
                showNotification('No recording available to download.', 'warning');
            }
        });



        // Custom Delete Modal
        const deleteModal = document.getElementById('delete-modal');
        const modalContent = document.getElementById('modal-content');
        const transcriptTitle = document.getElementById('transcript-title');
        const deleteForm = document.getElementById('delete-form');
        const cancelDelete = document.getElementById('cancel-delete');
        const confirmDelete = document.getElementById('confirm-delete');
        const deleteText = document.getElementById('delete-text');
        const deleteLoading = document.getElementById('delete-loading');

        // Show delete modal
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const transcriptId = this.getAttribute('data-transcript-id');
                const title = this.getAttribute('data-transcript-title');
                
                transcriptTitle.textContent = title;
                deleteForm.action = `/transcribe/${transcriptId}`;
                
                deleteModal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            });
        });

        // Hide modal
        function hideModal() {
            modalContent.classList.add('scale-95', 'opacity-0');
            modalContent.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                deleteModal.classList.add('hidden');
            }, 300);
        }

        // Cancel delete
        cancelDelete.addEventListener('click', hideModal);

        // Close modal on backdrop click
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                hideModal();
            }
        });

        // Handle delete form submission
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            confirmDelete.disabled = true;
            deleteText.classList.add('hidden');
            deleteLoading.classList.remove('hidden');
            
            // Submit form
            this.submit();
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                hideModal();
            }
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-content">
                <div class="text-center">
                    <!-- Warning Icon -->
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Transcript</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete "<span id="transcript-title" class="font-semibold text-gray-800"></span>"?</p>
                    
                    <!-- Warning Message -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="text-left">
                                <p class="text-red-800 font-medium text-sm">This action cannot be undone!</p>
                                <p class="text-red-700 text-xs mt-1">The transcript and associated audio file will be permanently deleted.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button id="cancel-delete" 
                                class="flex-1 px-6 py-3 bg-gray-500 text-white font-semibold rounded-xl hover:bg-gray-600 transition-all duration-300">
                            Cancel
                        </button>
                        <form id="delete-form" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    id="confirm-delete"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white font-semibold rounded-xl hover:from-red-600 hover:to-rose-600 transition-all duration-300 transform hover:scale-105">
                                <span id="delete-text">Delete</span>
                                <span id="delete-loading" class="hidden">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Deleting...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
