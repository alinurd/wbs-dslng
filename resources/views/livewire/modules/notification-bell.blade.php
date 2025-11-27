<div class="relative" x-data="{ isOpen: @entangle('isOpen') }">
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
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
        
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50 sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Notifications</h3>
                <div class="flex items-center gap-2">
                    @if($unreadCount > 0)
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ $unreadCount }} unread
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="divide-y divide-gray-100">
            {{-- Unread Notifications --}}
            @forelse($this->unreadNotifications as $notification)
                <div class="p-4 hover:bg-blue-50 transition-colors cursor-pointer border-l-2 border-blue-500 bg-blue-50/30"
                     wire:click="markAsRead({{ $notification['id'] }})">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="@if($notification['type'] === 'info') bg-blue-100 text-blue-600
                                       @elseif($notification['type'] === 'success') bg-green-100 text-green-600
                                       @elseif($notification['type'] === 'warning') bg-yellow-100 text-yellow-600
                                       @elseif($notification['type'] === 'error') bg-red-100 text-red-600
                                       @else bg-gray-100 text-gray-600 @endif p-2 rounded-full">
                                <i class="{{ $notification['icon'] ?? 'fas fa-bell' }} text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-blue-600 font-medium mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($notification['time'])->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <button wire:click.stop="deleteNotification({{ $notification['id'] }})"
                                    class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                {{-- No unread notifications --}}
            @endforelse

            {{-- Read Notifications --}}
            @forelse($this->readNotifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors cursor-pointer opacity-75"
                     wire:click="markAsRead({{ $notification['id'] }})">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="@if($notification['type'] === 'info') bg-blue-100 text-blue-600
                                       @elseif($notification['type'] === 'success') bg-green-100 text-green-600
                                       @elseif($notification['type'] === 'warning') bg-yellow-100 text-yellow-600
                                       @elseif($notification['type'] === 'error') bg-red-100 text-red-600
                                       @else bg-gray-100 text-gray-600 @endif p-2 rounded-full">
                                <i class="{{ $notification['icon'] ?? 'fas fa-bell' }} text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-2">
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
                {{-- No read notifications --}}
            @endforelse

            {{-- Empty State --}}
            @if(count($notifications) === 0)
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                    <p class="text-sm">No notifications</p>
                    <p class="text-xs text-gray-400 mt-1">You're all caught up!</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        @if(count($notifications) > 0 && $unreadCount > 0)
            <div class="p-3 border-t border-gray-200 bg-gray-50 sticky bottom-0">
                <button wire:click="markAllAsRead" 
                        class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-2 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark all as read
                </button>
            </div>
        @endif
    </div>
</div>