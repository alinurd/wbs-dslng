<div class="relative" wire:poll.10s="checkNewNotifications">
    <button wire:click="toggleNotifications" 
            class="relative p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors duration-200">
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Notification Dropdown --}}
    <div x-show="isOpen" 
         x-cloak
         @click.away="isOpen = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
        
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Notifikasi Komentar</h3>
                @if($unreadCount > 0)
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                        {{ $unreadCount }} baru
                    </span>
                @endif
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="p-4 hover:bg-blue-50 transition-colors cursor-pointer border-l-2 border-blue-500 bg-blue-50/30"
                     wire:click="goToPengaduan('{{ $notification['pengaduan_id'] }}', '{{ $notification['id'] }}')">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="bg-blue-100 text-blue-600 p-2 rounded-full">
                                <i class="fas fa-comment text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $notification['comment_user_name'] }} berkomentar:
                            </p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 italic">
                                "{{ $notification['comment_message'] }}"
                            </p>
                            <p class="text-xs text-blue-600 font-medium mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($notification['time'])->diffForHumans() }}
                            </p>
                        </div>
                        <button wire:click.stop="deleteNotification({{ $notification['id'] }})"
                                class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                    <p class="text-sm">Tidak ada notifikasi</p>
                    <p class="text-xs text-gray-400 mt-1">Tidak ada komentar baru</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($unreadCount > 0)
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                <button wire:click="markAllAsRead" 
                        class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-2 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Tandai semua sudah dibaca
                </button>
            </div>
        @endif
    </div>
</div>