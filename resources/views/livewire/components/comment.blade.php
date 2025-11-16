@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '', 
])
 

@if($show)
<div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-6xl transform transition-all duration-300 scale-95 animate-scale-in">
            <!-- Header -->
            <div class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-xl font-bold tracking-tight">
                                {{ $title }} {{$trackingId}}
                            </h5>
                            <p class="text-sm text-white/80 mt-1">Status: <span class="font-semibold">{{ $data['Status'] ?? 'Pending' }}</span></p>
                        </div>
                    </div>
                    <button type="button" wire:click="{{ $onClose }}"
                        class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/20 transition-all duration-300 hover:rotate-90">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-body p-0 max-h-[80vh] overflow-hidden flex">
                <!-- Detail Informasi -->
                <div class="w-1/3 border-r border-gray-200 bg-gray-50">
                    <div class="p-6">
                        <h6 class="font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            Informasi Pengaduan
                        </h6>
                        
                        <div class="space-y-3">
                            @foreach($data as $key => $value)
                                @if(!in_array($key, ['trackingId', 'Status']))
                                    <div class="border-b border-gray-100 pb-2">
                                        <p class="text-xs text-gray-500 font-medium">{{ $key }}</p>
                                        <p class="text-sm text-gray-800 font-semibold">{{ $value }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Section File Upload -->
                        <div class="mt-8">
                            <h6 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-paperclip mr-2 text-blue-500"></i>
                                Lampiran File
                            </h6>
                            
                            <!-- Upload Form -->
                            <div class="mb-4">
                                <form wire:submit.prevent="uploadFile" class="space-y-3">
                                    <div>
                                        <input type="file" 
                                               wire:model="fileUpload"
                                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                        @error('fileUpload')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <input type="text" 
                                               wire:model="fileDescription"
                                               placeholder="Deskripsi file (opsional)"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        
                                        <button type="submit"
                                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                                {{ !$fileUpload ? 'disabled' : '' }}>
                                            <i class="fas fa-upload"></i>
                                            <span>Upload</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Uploaded Files List -->
                            <div class="mt-4">
                                <h7 class="font-semibold text-gray-600 mb-3 text-sm">File Terlampir</h7>
                                
                                @if(isset($uploadedFiles) && count($uploadedFiles) > 0)
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        @foreach($uploadedFiles as $file)
                                            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                    <!-- File Icon -->
                                                    <div class="flex-shrink-0">
                                                        @if(str_contains($file['type'], 'image'))
                                                            <i class="fas fa-image text-blue-500"></i>
                                                        @elseif(str_contains($file['type'], 'pdf'))
                                                            <i class="fas fa-file-pdf text-red-500"></i>
                                                        @elseif(str_contains($file['type'], 'word') || str_contains($file['type'], 'document'))
                                                            <i class="fas fa-file-word text-blue-600"></i>
                                                        @elseif(str_contains($file['type'], 'excel') || str_contains($file['type'], 'spreadsheet'))
                                                            <i class="fas fa-file-excel text-green-500"></i>
                                                        @else
                                                            <i class="fas fa-file text-gray-500"></i>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- File Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-800 truncate">
                                                            {{ $file['name'] }}
                                                        </p>
                                                        @if($file['description'])
                                                            <p class="text-xs text-gray-500 truncate">
                                                                {{ $file['description'] }}
                                                            </p>
                                                        @endif
                                                        <p class="text-xs text-gray-400">
                                                            {{ $file['size'] }} • {{ $file['uploaded_at'] }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2 flex-shrink-0">
                                                    <button wire:click="downloadFile('{{ $file['id'] }}')"
                                                            class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                                            title="Download">
                                                        <i class="fas fa-download text-sm"></i>
                                                    </button>
                                                    <button wire:click="deleteFile('{{ $file['id'] }}')"
                                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                            title="Hapus">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500">
                                        <i class="fas fa-folder-open text-2xl mb-2 opacity-30"></i>
                                        <p class="text-sm">Belum ada file yang diupload</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Area Chat -->
                <div class="w-2/3 flex flex-col">
                    <!-- Header Chat -->
                    <div class="border-b border-gray-200 px-6 py-4 bg-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <h6 class="font-semibold text-gray-700">Diskusi Pengaduan</h6>
                            </div>
                            <span class="text-xs text-gray-500">Real-time</span>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div class="flex-1 p-6 overflow-y-auto bg-gray-50" 
                         id="chatMessages"
                         wire:poll.1s="loadMessages"
                         style="max-height: 400px;">
                         
                        @if(isset($messages) && count($messages) > 0)
                            <div class="space-y-4">
                                @foreach($messages as $message)
                                    <div class="flex {{ $message['is_own'] ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg 
                                            {{ $message['is_own'] 
                                                ? 'bg-blue-500 text-white rounded-br-none' 
                                                : 'bg-white border border-gray-200 rounded-bl-none' }}">
                                            
                                            @if(!$message['is_own'])
                                                <p class="text-xs font-semibold text-gray-600 mb-1">
                                                    {{ $message['sender'] }}
                                                </p>
                                            @endif
                                            
                                            <!-- Message Content -->
                                            <p class="text-sm">{{ $message['message'] }}</p>
                                            
                                            <!-- File Attachment in Message -->
                                            @if(isset($message['file']) && $message['file'])
                                                <div class="mt-2 p-2 bg-white/20 rounded border border-white/30">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-paperclip text-xs"></i>
                                                        <span class="text-xs truncate flex-1">{{ $message['file']['name'] }}</span>
                                                        <button wire:click="downloadMessageFile('{{ $message['file']['id'] }}')"
                                                                class="text-xs hover:underline">
                                                            Download
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <p class="text-xs mt-1 opacity-70 {{ $message['is_own'] ? 'text-blue-100' : 'text-gray-500' }}">
                                                {{ $message['time'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-comments text-4xl mb-3 opacity-30"></i>
                                <p>Belum ada pesan</p>
                                <p class="text-sm">Mulai percakapan...</p>
                            </div>
                        @endif
                    </div>

                    <!-- Typing Indicator -->
                    <div wire:loading wire:target="sendMessage" class="px-6 py-2">
                        <div class="flex items-center space-x-2 text-gray-500">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                            <span class="text-xs">Mengirim...</span>
                        </div>
                    </div>

                    <!-- Form Input Message -->
                    <div class="border-t border-gray-200 p-4 bg-white">
                        <form wire:submit.prevent="sendMessage" class="space-y-3">
                            <!-- File Attachment for Message -->
                            @if($attachFile)
                                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-paperclip text-blue-500"></i>
                                        <div>
                                            <p class="text-sm font-medium text-blue-800">{{ $attachFile->getClientOriginalName() }}</p>
                                            <p class="text-xs text-blue-600">File akan dilampirkan ke pesan</p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            wire:click="$set('attachFile', null)"
                                            class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif

                            <div class="flex space-x-3">
                                <!-- File Attachment Button -->
                                <label class="flex items-center justify-center w-12 h-12 border border-gray-300 text-gray-600 rounded-full hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                    <i class="fas fa-paperclip"></i>
                                    <input type="file" 
                                           wire:model="attachFile"
                                           class="hidden"
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                </label>

                                <!-- Message Input -->
                                <div class="flex-1">
                                    <input type="text" 
                                           wire:model="newMessage"
                                           placeholder="Ketik pesan Anda..."
                                           class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           {{ !$trackingId ? 'disabled' : '' }}>
                                </div>
                                
                                <!-- Send Button -->
                                <button type="submit"
                                        class="flex items-center justify-center w-12 h-12 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                        {{ !$trackingId || (!$newMessage && !$attachFile) ? 'disabled' : '' }}>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Error Messages -->
                        @error('newMessage')
                            <p class="text-red-500 text-xs mt-2 px-2">{{ $message }}</p>
                        @enderror
                        @error('attachFile')
                            <p class="text-red-500 text-xs mt-2 px-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end bg-gray-50">
                <button type="button" wire:click="{{ $onClose }}"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Script untuk auto scroll ke bawah -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function scrollToBottom() {
            const chatContainer = document.getElementById('chatMessages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }
        
        // Scroll saat modal terbuka
        scrollToBottom();
        
        // Scroll saat ada polling baru
        Livewire.hook('message.processed', () => {
            scrollToBottom();
        });
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .animate-scale-in {
        animation: scaleIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes scaleIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>