@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full max-w-6xl transform transition-all duration-300 scale-95 ">
                <!-- Header -->
                <div
                    class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-xl font-bold tracking-tight">
                                    {{ $title }}
                                </h5>

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
                                @foreach ($data as $key => $value)
                                    {{-- @if ($value !== 'status_ex') --}}
                                    @if (!in_array($key, ['trackingId', 'Status']))
                                        <div class="border-b border-gray-100 pb-2">
                                            <p class="text-xs text-gray-500 font-medium">{{ $key }}</p>
                                            <p class="text-sm text-gray-800 font-semibold">{{ $value }}</p>
                                        </div>
                                        {{-- @endif --}}
                                    @endif
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <!-- Area Chat -->
                    <div class="w-2/3 flex flex-col relative"> <!-- Tambahkan class relative di sini -->
                        <!-- Header Chat -->
                        <div class="border-b border-gray-200 px-6 py-4 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                    <h6 class="font-semibold text-gray-700">Diskusi Pengaduan</h6>
                                </div>
                                <div class="flex items-center space-x-3">
                                    {{-- <div
                                            class="flex items-center space-x-2 px-3 py-1.5 bg-{{ $data['status_ex']['color'] }}-50 rounded-lg border border-{{ $data['status_ex']['color'] }}-200">
                                            <div
                                                class="w-2 h-2 bg-{{ $data['status_ex']['color'] }}-500 rounded-full animate-pulse">
                                            </div>

                                            @if ($data['sts_fwd']['id'] === 1 && $data['user']['role']['id'] === 6)
                                                <span
                                                    class="text-sm font-medium text-{{ $data['sts_fwd']['data']['color'] }}-700">
                                                    {{ $data['sts_fwd']['data']['text'] }}
                                                </span>
                                            @else
                                                <span
                                                    class="text-sm font-medium text-{{ $data['status_ex']['color'] }}-700">
                                                    {{ $data['status_ex']['name'] }}
                                                </span>
                                                </span>
                                            @endif
                                        </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <!-- Ganti bagian chat messages dengan kode berikut: -->
<div class="flex-1 p-6 overflow-y-auto bg-gray-50" id="chatMessages"
    @if (!$showMentionDropdown) wire:poll.1s="loadMessages" @endif
                            style="max-height: 400px;">
    
    @if (isset($messages) && count($messages) > 0)
        <div class="space-y-4">
            @foreach ($messages as $message)
                <div class="flex {{ $message['is_own'] ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg
                        {{ $message['is_own'] 
                            ? 'bg-sky-950 text-white rounded-br-none' 
                            : 'bg-white border border-gray-200 rounded-bl-none' }}">
                        
                        @if (!$message['is_own'])
                            <p class="text-xs font-semibold text-gray-600 mb-1">
                                #{{ $message['sender'] }}
                            </p>
                        @endif

                        <p class="text-sm text-wrap break-words ">
                            @php
                                $messageText = $message['message'];
                                // Highlight mentions
                                $messageText = preg_replace_callback(
                                    '/@([a-zA-Z0-9_]+)/',
                                    function ($matches) {
                                        return '<span class="text-blue-600 font-mediumpx-1 py-0.5 rounded">@' .
                                            $matches[1] .
                                            '</span>';
                                    },
                                    htmlspecialchars($messageText),
                                );
                            @endphp
                            {!! nl2br($messageText) !!}
                        </p>

                        <!-- File Attachment in Message -->
                        @if (isset($message['file']) && $message['file'])
                            <div class="mt-2 p-2 bg-gray-100 rounded border border-sky-950">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-paperclip text-sky-950 text-xs"></i>
                                    <span class="text-xs text-sky-950  truncate flex-1">
                                        {{ $message['file']['original_name'] ?? 'Unknown File' }}
                                    </span>
                                    <button
                                        wire:click="downloadMessageFile('{{ $message['id'] }}')"
                                        class="text-xs text-sky-900 hover:underline hover:text-sky-800">
                                        Download
                                    </button>
                                </div>
                                @if (isset($message['file']['formatted_size']))
                                    <p class="text-xs text-sky-950 mt-1">
                                        {{ $message['file']['formatted_size'] }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        <p class="text-xs mt-1 {{ $message['is_own'] ? 'text-blue-200' : 'text-gray-500' }}">
                            {{ $message['time'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center h-full text-gray-400">
            <div class="mb-4">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
            </div>
            <p class="text-lg font-medium text-gray-500">Belum ada pesan</p>
            <p class="text-sm text-gray-400 mt-1">Mulai percakapan...</p>
        </div>
    @endif
</div>

                        <!-- Mention Dropdown -->
                        @if ($showMentionDropdown && count($mentionUsers) > 0)
                            <div class="mention-dropdown">
                                <div class="sticky top-0 p-2 border-b border-gray-200 bg-gray-50">
                                    <p class="text-xs font-medium text-gray-700">Mention user:</p>
                                    <p class="text-xs text-gray-500 mt-1">Tekan ↑↓ untuk navigasi, Enter untuk memilih
                                    </p>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    @foreach ($mentionUsers as $index => $user)
                                        <button type="button" wire:click="selectMentionUser({{ $index }})"
                                            class="mention-item flex items-center w-full px-3 py-2.5 text-left hover:bg-blue-50 transition-colors {{ $loop->first ? 'rounded-t-lg' : '' }} {{ $loop->last ? 'rounded-b-lg' : '' }}"
                                            data-index="{{ $index }}">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    {{ substr($user['name'], 0, 2) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $user['name'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 truncate">
                                                    {{ $user['email'] }}
                                                </p>
                                            </div>
                                            <div class="ml-2">
                                                <i class="fas fa-at text-gray-400 text-xs"></i>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                                @if (count($mentionUsers) === 0)
                                    <div class="p-4 text-center text-gray-500">
                                        <i class="fas fa-search mb-2"></i>
                                        <p class="text-sm">Tidak ada user ditemukan</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Typing Indicator -->
                        <div wire:loading wire:target="sendMessage" class="px-6 py-2">
                            <div class="flex items-center space-x-2 text-gray-500">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                        style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                        style="animation-delay: 0.2s"></div>
                                </div>
                                <span class="text-xs">Mengirim...</span>
                            </div>
                        </div>

                        <!-- Form Input Message -->
                        <div class="border-t border-gray-200 p-4 bg-white">
                            <form wire:submit.prevent="sendMessage" class="space-y-3">
                                <!-- File Attachment for Message -->
                                @if ($attachFile)
                                    <div
                                        class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-paperclip text-blue-500"></i>
                                            <div>
                                                <p class="text-sm font-medium text-blue-800">
                                                    {{ $attachFile->getClientOriginalName() }}</p>
                                                <p class="text-xs text-blue-600">File akan dilampirkan ke pesan</p>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="$set('attachFile', null)"
                                            class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif

                                <div class="flex space-x-3">
                                    <!-- File Attachment Button -->
                                    <label
                                        class="flex items-center justify-center w-12 h-12 border border-gray-300 text-gray-600 rounded-full hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                        <i class="fas fa-paperclip"></i>
                                        <input type="file" wire:model="attachFile" class="hidden"
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                    </label>

                                    <!-- Message Input Container - VERSI SEDERHANA -->
                                    <div class="flex-1 relative">
                                        <textarea wire:model="newMessage" wire:keydown.escape="resetMentionDropdown" x-data="{
                                            init() {
                                                    this.$refs.textarea.focus();
                                        
                                                    // Track mention state
                                                    this.mentionActive = false;
                                        
                                                    // Auto resize textarea
                                                    this.autoResize = () => {
                                                        this.$refs.textarea.style.height = 'auto';
                                                        this.$refs.textarea.style.height = (this.$refs.textarea.scrollHeight) + 'px';
                                                    };
                                        
                                                    // Simple @ detection
                                                    this.$refs.textarea.addEventListener('input', (e) => {
                                                        const cursorPos = e.target.selectionStart;
                                                        const text = e.target.value;
                                        
                                                        // Auto resize
                                                        this.autoResize();
                                        
                                                        // Send cursor position to Livewire
                                                        this.$wire.set('mentionDropdownPosition', cursorPos);
                                        
                                                        // Simple @ detection - jika ada @, langsung tampilkan dropdown
                                                        if (text.includes('@')) {
                                                            const lastAtPos = text.lastIndexOf('@', cursorPos - 1);
                                                            if (lastAtPos !== -1) {
                                                                // Cek karakter sebelum @
                                                                const charBefore = lastAtPos > 0 ? text[lastAtPos - 1] : ' ';
                                                                if (charBefore === ' ' || lastAtPos === 0) {
                                                                    // Ambil teks setelah @
                                                                    const afterAt = text.substring(lastAtPos + 1, cursorPos);
                                                                    if (!afterAt.includes(' ')) {
                                                                        // Jika belum ada karakter setelah @, show semua user
                                                                        if (afterAt.length === 0) {
                                                                            this.$wire.call('loadAllMentionableUsers');
                                                                        } else {
                                                                            this.$wire.call('loadMentionUsers', afterAt);
                                                                        }
                                                                        this.mentionActive = true;
                                                                        return;
                                                                    }
                                                                }
                                                            }
                                                        }
                                        
                                                        // Jika tidak ada @ yang valid, reset dropdown
                                                        if (this.mentionActive) {
                                                            this.$wire.call('resetMentionDropdown');
                                                            this.mentionActive = false;
                                                        }
                                                    });
                                        
                                                    // Handle arrow keys for navigation
                                                    this.$refs.textarea.addEventListener('keydown', (e) => {
                                                        const hasMentionDropdown = this.$wire.get('showMentionDropdown');
                                        
                                                        if (hasMentionDropdown) {
                                                            if (e.key === 'ArrowDown') {
                                                                e.preventDefault();
                                                                this.navigateMention('down');
                                                            } else if (e.key === 'ArrowUp') {
                                                                e.preventDefault();
                                                                this.navigateMention('up');
                                                            } else if (e.key === 'Enter' && !e.shiftKey) {
                                                                // Prevent new line jika ada dropdown
                                                                e.preventDefault();
                                                                this.selectMention();
                                                            } else if (e.key === 'Escape') {
                                                                this.$wire.call('resetMentionDropdown');
                                                            } else if (e.key === 'Tab') {
                                                                e.preventDefault();
                                                                this.selectMention();
                                                            } else if (e.key === ' ') {
                                                                // Jika spasi ditekan, close dropdown
                                                                this.$wire.call('resetMentionDropdown');
                                                            }
                                                        } else if (e.key === 'Enter' && !e.shiftKey) {
                                                            // Submit form jika Enter tanpa shift dan tidak ada mention dropdown
                                                            e.preventDefault();
                                                            document.querySelector('form[wire\\:submit]').requestSubmit();
                                                        }
                                                    });
                                        
                                                    // Handle click untuk update cursor
                                                    this.$refs.textarea.addEventListener('click', () => {
                                                        setTimeout(() => {
                                                            const cursorPos = this.$refs.textarea.selectionStart;
                                                            this.$wire.set('mentionDropdownPosition', cursorPos);
                                                            this.$wire.call('checkForMentionTrigger');
                                                        }, 10);
                                                    });
                                        
                                                    // Handle focus untuk check mention
                                                    this.$refs.textarea.addEventListener('focus', () => {
                                                        this.$wire.call('checkForMentionTrigger');
                                                    });
                                        
                                                    // Handle set cursor position dari Livewire
                                                    Livewire.on('set-cursor-position', (data) => {
                                                        setTimeout(() => {
                                                            this.$refs.textarea.focus();
                                                            this.$refs.textarea.setSelectionRange(data.position, data.position);
                                                        }, 10);
                                                    });
                                                },
                                        
                                                navigateMention(direction) {
                                                    // Gunakan selector sederhana
                                                    const mentionItems = document.querySelectorAll('.mention-item');
                                                    if (mentionItems.length === 0) return;
                                        
                                                    let currentIndex = -1;
                                                    mentionItems.forEach((item, index) => {
                                                        if (item.classList.contains('bg-blue-100')) {
                                                            currentIndex = index;
                                                        }
                                                    });
                                        
                                                    let newIndex;
                                                    if (direction === 'down') {
                                                        newIndex = currentIndex >= mentionItems.length - 1 ? 0 : currentIndex + 1;
                                                    } else {
                                                        newIndex = currentIndex <= 0 ? mentionItems.length - 1 : currentIndex - 1;
                                                    }
                                        
                                                    // Update UI
                                                    mentionItems.forEach((item, index) => {
                                                        item.classList.remove('bg-blue-100', 'border-l-2', 'border-blue-500');
                                                        if (index === newIndex) {
                                                            item.classList.add('bg-blue-100', 'border-l-2', 'border-blue-500');
                                                            item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                                                        }
                                                    });
                                                },
                                        
                                                selectMention() {
                                                    const mentionItems = document.querySelectorAll('.mention-item');
                                                    mentionItems.forEach((item) => {
                                                        if (item.classList.contains('bg-blue-100')) {
                                                            item.click();
                                                            return;
                                                        }
                                                    });
                                        
                                                    if (mentionItems.length > 0) {
                                                        mentionItems[0].click();
                                                    }
                                                }
                                        }" x-ref="textarea"
                                            placeholder="Ketik pesan Anda... Gunakan @ untuk mention user (email akan ditampilkan)"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm resize-none overflow-hidden"
                                            id="messageInput" autocomplete="off" rows="4" style="min-height: 48px; max-height: 250px;"
                                            {{ !$trackingId ? 'disabled' : '' }}></textarea>
                                    </div>

                                    <!-- Send Button -->
                                    <button type="submit"
                                        class="flex items-center justify-center w-12 h-12 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                        {{ !$trackingId || (!$newMessage && !$attachFile) ? 'disabled' : '' }}>
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>

                            <!-- Info tentang mention -->
                            <!-- Info tentang mention -->
                            <div class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Gunakan <span class="font-medium">@email</span> untuk mention user. User akan
                                mendapatkan notifikasi.
                            </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // console.log('✅ Chat script loaded');

        // Auto scroll ke bottom chat
        function scrollToBottom() {
            const chatContainer = document.getElementById('chatMessages');
            if (chatContainer) {
                setTimeout(() => {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }, 100);
            }
        }

        // Scroll saat modal terbuka
        scrollToBottom();

        // Track polling state
        let pollingPaused = false;

        // Function untuk pause polling
        function pausePolling() {
            const chatContainer = document.getElementById('chatMessages');
            if (chatContainer && chatContainer.hasAttribute('wire:poll') && !pollingPaused) {
                chatContainer.removeAttribute('wire:poll');
                pollingPaused = true;
                // console.log('⏸️ Polling paused for mention');
            }
        }

        // Function untuk resume polling
        function resumePolling() {
            const chatContainer = document.getElementById('chatMessages');
            if (chatContainer && pollingPaused && !chatContainer.hasAttribute('wire:poll')) {
                chatContainer.setAttribute('wire:poll.1s', 'loadMessages');
                pollingPaused = false;
                // console.log('▶️ Polling resumed');
            }
        }

        // Monitor mention dropdown visibility
        function checkMentionDropdown() {
            const mentionDropdown = document.querySelector('.mention-dropdown');
            if (mentionDropdown) {
                const isVisible = window.getComputedStyle(mentionDropdown).display !== 'none';

                if (isVisible && !pollingPaused) {
                    pausePolling();
                } else if (!isVisible && pollingPaused) {
                    // Delay sebelum resume untuk memastikan dropdown benar-benar hilang
                    setTimeout(() => {
                        resumePolling();
                    }, 200);
                }
            } else if (pollingPaused) {
                // Jika tidak ada dropdown sama sekali, resume polling
                setTimeout(() => {
                    resumePolling();
                }, 200);
            }
        }

        // Hook untuk Livewire - monitor state changes
        Livewire.hook('message.processed', (message) => {
            // Scroll ke bottom saat ada message baru
            scrollToBottom();

            // Check mention dropdown visibility
            checkMentionDropdown();
        });

        // Event untuk mention dropdown updated
        Livewire.on('mention-dropdown-updated', () => {
            // console.log('📋 Mention dropdown updated');
            pausePolling();
        });

        // Event untuk mention dropdown reset
        Livewire.on('mention-dropdown-reset', () => {
            // console.log('🗑️ Mention dropdown reset');
            // Delay sebelum resume polling
            setTimeout(() => {
                resumePolling();
            }, 150);
        });

        // Event untuk set cursor position
        Livewire.on('set-cursor-position', (data) => {
            // console.log('📍 Setting cursor position:', data.position);
            const textarea = document.getElementById('messageInput');
            if (textarea) {
                setTimeout(() => {
                    textarea.focus();
                    textarea.setSelectionRange(data.position, data.position);
                }, 10);
            }
        });

        // Event untuk focus message input
        Livewire.on('focus-message-input', () => {
            const textarea = document.getElementById('messageInput');
            if (textarea) {
                setTimeout(() => {
                    textarea.focus();
                }, 10);
            }
        });

        // Focus ke textarea saat modal terbuka
        Livewire.hook('element.updated', (el, component) => {
            // Cek jika ini modal content yang visible
            if (el.classList && el.classList.contains('modal-content') &&
                window.getComputedStyle(el).display !== 'none') {

                setTimeout(() => {
                    const messageInput = document.getElementById('messageInput');
                    if (messageInput && !messageInput.disabled) {
                        messageInput.focus();
                    }
                }, 300);
            }
        });

        // Handle click outside untuk close mention dropdown
        document.addEventListener('click', function(e) {
            const mentionDropdown = document.querySelector('.mention-dropdown');
            const messageInput = document.getElementById('messageInput');

            if (mentionDropdown && messageInput && window.getComputedStyle(mentionDropdown).display !==
                'none') {
                const isClickInDropdown = mentionDropdown.contains(e.target);
                const isClickInInput = messageInput.contains(e.target);

                if (!isClickInDropdown && !isClickInInput) {
                    // Dispatch event untuk reset mention dropdown
                    // console.log('👆 Click outside, resetting mention');
                    Livewire.dispatch('reset-mention-dropdown');
                }
            }
        });

        // Handle keydown untuk Escape secara global
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const mentionDropdown = document.querySelector('.mention-dropdown');
                if (mentionDropdown && window.getComputedStyle(mentionDropdown).display !== 'none') {
                    // console.log('⎋ Escape pressed, resetting mention');
                    Livewire.dispatch('reset-mention-dropdown');

                    // Focus ke input
                    const messageInput = document.getElementById('messageInput');
                    if (messageInput) {
                        messageInput.focus();
                    }
                }
            }
        });

        // Setup initial polling
        function setupInitialPolling() {
            const chatContainer = document.getElementById('chatMessages');
            if (chatContainer && !chatContainer.hasAttribute('wire:poll')) {
                // Cek jika ada mention dropdown visible
                const mentionDropdown = document.querySelector('.mention-dropdown');
                const hasVisibleMentionDropdown = mentionDropdown &&
                    window.getComputedStyle(mentionDropdown).display !== 'none';

                if (!hasVisibleMentionDropdown) {
                    chatContainer.setAttribute('wire:poll.1s', 'loadMessages');
                }
            }
        }

        // Initial setup
        setupInitialPolling();

        // Check ulang setelah modal fully loaded
        setTimeout(setupInitialPolling, 500);

        // Auto resize textarea saat halaman load
        setTimeout(() => {
            const textarea = document.getElementById('messageInput');
            if (textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            }
        }, 100);

        // Add mutation observer untuk detect changes in mention dropdown
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' || mutation.type === 'childList') {
                    // Check untuk mention dropdown changes
                    const target = mutation.target;
                    if (target.classList && target.classList.contains('mention-dropdown')) {
                        checkMentionDropdown();
                    }

                    // Check jika ada element mention dropdown baru
                    if (mutation.addedNodes) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.classList && node.classList.contains(
                                    'mention-dropdown')) {
                                // console.log('📋 Mention dropdown added');
                                pausePolling();
                            }
                        });
                    }

                    // Check jika mention dropdown dihapus
                    if (mutation.removedNodes) {
                        mutation.removedNodes.forEach(function(node) {
                            if (node.classList && node.classList.contains(
                                    'mention-dropdown')) {
                                // console.log('🗑️ Mention dropdown removed');
                                setTimeout(() => {
                                    resumePolling();
                                }, 200);
                            }
                        });
                    }
                }
            });
        });

        // Observe body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class']
        });

        // Also observe the chat area specifically
        const chatArea = document.querySelector('.w-2\\/3.flex-col.relative');
        if (chatArea) {
            observer.observe(chatArea, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }
    });

    // Helper untuk dispatch event ke Livewire
    window.dispatchLivewireEvent = (eventName, data) => {
        if (window.Livewire && window.Livewire.dispatch) {
            window.Livewire.dispatch(eventName, data);
        }
    };
</script>

<style>
    /* Mention dropdown styling */
    .mention-dropdown {
        position: fixed;
        bottom: 140px;
        /* Sesuaikan karena textarea lebih tinggi */
        left: 50%;
        transform: translateX(-50%);
        width: calc(66.666% - 100px);
        max-height: 240px;
        overflow-y: auto;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        z-index: 99999;
        animation: slideUp 0.15s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    /* Mention item styling */
    .mention-item {
        transition: background-color 0.15s ease;
    }

    .mention-item:hover {
        background-color: #eff6ff !important;
    }

    .mention-item.bg-blue-100 {
        background-color: #dbeafe !important;
        border-left: 2px solid #3b82f6;
    }

    /* Style untuk mention dalam chat - MIRING BIRU */
    .mention {
        font-style: italic;
        color: #ffffff;
        font-weight: 500;
        background-color: #2563eb;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        display: inline-block;
        margin: 0 0.125rem;
    }

    /* Textarea styling */
    #messageInput {
        min-height: 48px;
        max-height: 300px;
        resize: none;
        overflow-y: auto;
        line-height: 1.5;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    #messageInput:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }

    /* Scrollbar untuk dropdown */
    .mention-dropdown::-webkit-scrollbar {
        width: 8px;
    }

    .mention-dropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .mention-dropdown::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .mention-dropdown::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Scrollbar untuk textarea */
    #messageInput::-webkit-scrollbar {
        width: 6px;
    }

    #messageInput::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    #messageInput::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    #messageInput::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Animasi untuk modal */
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    .animate-scale-in {
        animation: scaleIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.95);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
