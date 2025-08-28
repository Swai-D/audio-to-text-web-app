@props(['name' => 'audio', 'accept' => 'audio/*,video/mp4', 'required' => true])

<div class="upload-area rounded-2xl p-8 text-center transition-all duration-300" 
     x-data="recordingFunctions"
     x-init="
         isDragOver = false;
         fileName = null;
         fileSize = null;
         isUploading = false;
         isRecording = false;
         recordingTime = 0;
         recordingInterval = null;
         mediaRecorder = null;
         recordedChunks = [];
         audioBlob = null;
         audioUrl = null;
     "
     @dragover.prevent="isDragOver = true"
     @dragleave.prevent="isDragOver = false"
     @drop.prevent="
         isDragOver = false;
         const files = $event.dataTransfer.files;
         if (files.length > 0) {
             $refs.fileInput.files = files;
             fileName = files[0].name;
             fileSize = (files[0].size / 1024 / 1024).toFixed(2);
             $refs.fileInput.dispatchEvent(new Event('change'));
         }
     "
     :class="isDragOver ? 'border-indigo-400 bg-indigo-50 scale-105' : 'border-dashed border-gray-300'">
    
    <!-- Default State -->
    <div x-show="!fileName && !audioUrl" class="space-y-6">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-gray-400 transition-colors duration-300" 
                 :class="isDragOver ? 'text-indigo-500' : 'text-gray-400'"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        </div>
        
        <div class="space-y-4">
            <!-- Upload Option -->
            <div class="space-y-2">
                <label class="cursor-pointer">
                    <span class="text-lg font-medium text-gray-700 transition-colors duration-300"
                          :class="isDragOver ? 'text-indigo-700' : 'text-gray-700'">
                        Choose audio file or drag & drop
                    </span>
                    <input x-ref="fileInput" 
                           type="file" 
                           name="{{ $name }}" 
                           accept="{{ $accept }}" 
                           class="hidden" 
                           {{ $required ? 'required' : '' }}
                           @change="
                               if ($event.target.files.length > 0) {
                                   fileName = $event.target.files[0].name;
                                   fileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2);
                               }
                           ">
                </label>
                <p class="text-sm text-gray-500">MP3, WAV, M4A, MP4 up to 50MB</p>
            </div>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">au</span>
                </div>
            </div>

            <!-- Record Option -->
            <div class="space-y-3">
                <p class="text-lg font-medium text-gray-700">Record audio directly</p>
                
                <!-- Recording Button -->
                <button type="button"
                        @click="
                            if (!isRecording) {
                                startRecording();
                            } else {
                                stopRecording();
                            }
                        "
                        x-data
                        :class="isRecording ? 
                            'bg-red-500 hover:bg-red-600 text-white' : 
                            'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white'"
                        class="inline-flex items-center px-6 py-3 rounded-xl font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    
                    <!-- Recording Icon -->
                    <svg x-show="!isRecording" class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                    
                    <!-- Stop Icon -->
                    <svg x-show="isRecording" class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                    </svg>
                    
                    <span x-text="isRecording ? 'Stop Recording' : 'Start Recording'"></span>
                </button>

                <!-- Recording Timer -->
                <div x-show="isRecording" x-transition class="text-center">
                    <div class="text-2xl font-mono font-bold text-red-600" 
                         x-text="formatTime(recordingTime)"></div>
                    <div class="text-sm text-gray-600 mt-1">Recording in progress...</div>
                </div>

                <!-- Recording Status -->
                <div x-show="isRecording" x-transition class="flex items-center justify-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-red-600 font-medium">Recording</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- File Selected State -->
    <div x-show="fileName && !audioUrl" x-transition class="space-y-4">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <div class="space-y-2">
            <div class="text-lg font-medium text-gray-700" x-text="fileName"></div>
            <div class="text-sm text-gray-500" x-text="`${fileSize} MB`"></div>
            <p class="text-sm text-green-600 font-medium">Ready to transcribe</p>
        </div>
        
        <!-- Change File Button -->
        <button type="button" 
                @click="
                    fileName = null;
                    fileSize = null;
                    $refs.fileInput.value = '';
                "
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Change File
        </button>
    </div>

    <!-- Recording Preview State -->
    <div x-show="audioUrl" x-transition class="space-y-4">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
            </svg>
        </div>
        
        <div class="space-y-2">
            <div class="text-lg font-medium text-gray-700">Recording Complete</div>
            <div class="text-sm text-gray-500" x-text="`Duration: ${formatTime(recordingTime)}`"></div>
            <p class="text-sm text-blue-600 font-medium">Ready to transcribe</p>
        </div>

        <!-- Audio Preview -->
        <div class="bg-gray-50 rounded-lg p-4">
            <audio x-ref="audioPlayer" controls class="w-full">
                <source :src="audioUrl" type="audio/wav">
                Your browser does not support the audio element.
            </audio>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <button type="button" 
                    @click="
                        audioUrl = null;
                        audioBlob = null;
                        recordedChunks = [];
                        recordingTime = 0;
                        $refs.fileInput.value = '';
                    "
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Record Again
            </button>
            
            <button type="button" 
                    @click="downloadRecording()"
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-200">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download
            </button>
        </div>
    </div>
    
    <!-- Upload Progress (Optional) -->
    <div x-show="isUploading" x-transition class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-300" 
                 style="width: 0%"
                 x-data="{ progress: 0 }"
                 x-init="
                     const interval = setInterval(() => {
                         progress += Math.random() * 15;
                         if (progress >= 100) {
                             progress = 100;
                             clearInterval(interval);
                             setTimeout(() => isUploading = false, 500);
                         }
                         $el.style.width = progress + '%';
                     }, 200);
                 "></div>
        </div>
        <p class="text-sm text-gray-600 mt-2">Processing audio...</p>
    </div>
</div>

<!-- Audio Preview (if video file) -->
<div x-show="fileName && fileName.toLowerCase().includes('.mp4')" 
     x-transition 
     class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
    <div class="flex items-center space-x-2">
        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
        </svg>
        <span class="text-sm text-blue-800 font-medium">Video file detected - audio will be extracted</span>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('recordingFunctions', () => ({
        startRecording() {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    this.mediaRecorder = new MediaRecorder(stream);
                    this.recordedChunks = [];
                    
                    this.mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            this.recordedChunks.push(event.data);
                        }
                    };
                    
                    this.mediaRecorder.onstop = () => {
                        this.audioBlob = new Blob(this.recordedChunks, { type: 'audio/wav' });
                        this.audioUrl = URL.createObjectURL(this.audioBlob);
                        
                        // Create a file input with the recorded audio
                        const file = new File([this.audioBlob], `recording-${Date.now()}.wav`, { type: 'audio/wav' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.fileInput.files = dataTransfer.files;
                        this.fileName = file.name;
                        this.fileSize = (file.size / 1024 / 1024).toFixed(2);
                        
                        // Stop all tracks
                        stream.getTracks().forEach(track => track.stop());
                    };
                    
                    this.mediaRecorder.start();
                    this.isRecording = true;
                    
                    // Start timer
                    this.recordingTime = 0;
                    this.recordingInterval = setInterval(() => {
                        this.recordingTime++;
                    }, 1000);
                })
                .catch(error => {
                    console.error('Error accessing microphone:', error);
                    alert('Unable to access microphone. Please check permissions.');
                });
        },

        stopRecording() {
            if (this.mediaRecorder && this.isRecording) {
                this.mediaRecorder.stop();
                this.isRecording = false;
                
                if (this.recordingInterval) {
                    clearInterval(this.recordingInterval);
                    this.recordingInterval = null;
                }
            }
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },

        downloadRecording() {
            if (this.audioBlob) {
                const url = URL.createObjectURL(this.audioBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `recording-${Date.now()}.wav`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
        }
    }));
});
</script>
