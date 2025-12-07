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
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-9 max-h-96 overflow-y-auto">
        
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50 sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Notifications</h3>
                <div class="flex items-center gap-2">
                    <button wire:click="loadNotifications" 
                            wire:loading.attr="disabled"
                            class="p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                            title="Refresh notifications">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </button>
                    @if($unreadCount > 0)
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ $unreadCount }} unread
                        </span>
                    @endif
                </div>
            </div>

            {{-- Filter Tabs --}}
            <div class="mt-3 flex space-x-1 overflow-x-auto pb-1">
                @php
                    $counts = $this->notificationCounts;
                @endphp
                <button wire:click="filterNotifications('all')"
                        class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'all' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    All ({{ $counts['all'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('unread')"
                        class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'unread' ? 'bg-red-100 text-red-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Unread ({{ $counts['unread'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('chat')"
                        class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'chat' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Chat ({{ $counts['chat'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('approval')"
                        class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'approval' ? 'bg-green-100 text-green-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Approval ({{ $counts['approval'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('other')"
                        class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'other' ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Other ({{ $counts['other'] ?? 0 }})
                </button>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="divide-y divide-gray-100">
            @php
                $filteredNotifications = $this->filteredNotifications;
            @endphp
            
            @forelse($filteredNotifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors cursor-pointer {{ !$notification['read'] ? 'border-l-2 border-blue-500 bg-blue-50/30' : 'opacity-80' }}"
                     wire:click="markAsRead({{ $notification['id'] }})">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="@if($notification['badge_color'] === 'blue') bg-blue-100 text-blue-600
                                       @elseif($notification['badge_color'] === 'green') bg-green-100 text-green-600
                                       @elseif($notification['badge_color'] === 'gray') bg-gray-100 text-gray-600
                                       @else bg-gray-100 text-gray-600 @endif p-2 rounded-full">
                                <i class="{{ $notification['icon'] ?? 'fas fa-bell' }} text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            {{-- Badge Type --}}
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $notification['type_class'] }}">
                                    @if($notification['type'] == 'chat')
                                        <i class="fas fa-comment-alt mr-1 text-xs"></i> Chat
                                    @elseif($notification['type'] == 'approval')
                                        <i class="fas fa-clipboard-check mr-1 text-xs"></i> Approval
                                    @else
                                        <i class="fas fa-bell mr-1 text-xs"></i> Other
                                    @endif
                                </span>
                                
                                {{-- @if(!$notification['read'])
                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                @endif --}}
                            </div>
                            
                            <p class="text-sm font-semibold text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs @if(!$notification['read']) text-blue-600 font-medium @else text-gray-400 @endif mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($notification['time'])->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            @if(!$notification['read'])
                                <button wire:click.stop="markAsRead({{ $notification['id'] }})"
                                        class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded"
                                        title="Mark as read">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            @endif
                            <button wire:click.stop="deleteNotification({{ $notification['id'] }})"
                                    onclick="return confirm('Delete this notification?')"
                                    class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded"
                                    title="Delete notification">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                    <p class="text-sm">No notifications</p>
                    <p class="text-xs text-gray-400 mt-1">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($filteredNotifications) > 0 && $unreadCount > 0)
            <div class="p-3 border-t border-gray-200 bg-gray-50 sticky bottom-0">
                <button wire:click="markAllAsRead" 
                        class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-2 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark all as read
                </button>
            </div>
        @endif
        
        {{-- Polling Indicator --}}
        <div class="px-3 py-1 border-t border-gray-100 text-center">
            <span class="text-xs text-gray-400">
                <i class="fas fa-sync-alt fa-spin text-xs mr-1"></i>
                Auto-refresh every 30s
            </span>
        </div>
    </div>
    
<!-- Polling Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let pollingInterval;
        let fastPollingInterval;
        
        function startPolling(interval = 30000) {
            if (pollingInterval) clearInterval(pollingInterval);
            
            pollingInterval = setInterval(() => {
                if (window.Livewire && window.Livewire.find('notification-bell')) {
                    window.Livewire.find('notification-bell').call('loadNotifications');
                }
            }, interval);
        }
        
        function startFastPolling() {
            if (fastPollingInterval) clearInterval(fastPollingInterval);
            
            fastPollingInterval = setInterval(() => {
                if (window.Livewire && window.Livewire.find('notification-bell')) {
                    window.Livewire.find('notification-bell').call('loadNotifications');
                }
            }, 5000); // Fast polling every 5s when dropdown is open
        }
        
        function stopFastPolling() {
            if (fastPollingInterval) {
                clearInterval(fastPollingInterval);
                fastPollingInterval = null;
            }
        }
        
        // Start normal polling
        startPolling(30000);
        
        // Listen for Alpine.js state changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'x-show') {
                    const element = mutation.target;
                    const isOpen = element.getAttribute('x-show') === 'true' || 
                                  element.style.display !== 'none';
                    
                    if (isOpen) {
                        // Start fast polling when dropdown opens
                        startFastPolling();
                        clearInterval(pollingInterval); // Stop normal polling
                    } else {
                        // Resume normal polling when dropdown closes
                        stopFastPolling();
                        startPolling(30000);
                    }
                }
            });
        });
        
        // Observe the dropdown element
        const dropdown = document.querySelector('[x-show="isOpen"]');
        if (dropdown) {
            observer.observe(dropdown, { 
                attributes: true, 
                attributeFilter: ['x-show', 'style'] 
            });
        }
        
        // Cleanup
        window.addEventListener('beforeunload', function() {
            if (pollingInterval) clearInterval(pollingInterval);
            if (fastPollingInterval) clearInterval(fastPollingInterval);
        });
        
        // Livewire event listeners
        document.addEventListener('livewire:init', function() {
            startPolling(30000);
        });
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
    
    .fa-spin {
        animation: fa-spin 2s infinite linear;
    }
    
    @keyframes fa-spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
</div>
