<div class="relative" x-data="notificationBell()" x-init="init()">
    <button @click="toggleDropdown()"
        class="relative p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors duration-200">
        <i class="fas fa-bell text-xl"></i>
        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Notification Dropdown --}}
    <div x-show="isOpen" x-cloak @click.away="closeDropdown()" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">

        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50 sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Notifications</h3>
                <div class="flex items-center gap-2">
                    <button wire:click="loadNotifications" wire:loading.attr="disabled" @click.stop
                        class="p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                        title="Refresh notifications">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </button>
                    @if ($unreadCount > 0)
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
                <button wire:click="filterNotifications('all')" @click.stop
                    class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'all' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    All ({{ $counts['all'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('unread')" @click.stop
                    class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'unread' ? 'bg-red-100 text-red-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Unread ({{ $counts['unread'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('chat')" @click.stop
                    class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'chat' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Chat ({{ $counts['chat'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('approval')" @click.stop
                    class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'approval' ? 'bg-green-100 text-green-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Approval ({{ $counts['approval'] ?? 0 }})
                </button>
                <button wire:click="filterNotifications('other')" @click.stop
                    class="px-3 py-1.5 text-xs rounded-full transition-colors whitespace-nowrap {{ $activeFilter == 'other' ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                    Other ({{ $counts['other'] ?? 0 }})
                </button>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="divide-y divide-gray-100" id="notification-list" @click.stop>
            @php
                $filteredNotifications = $this->filteredNotifications;
            @endphp

            @forelse($filteredNotifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors cursor-pointer {{ !$notification['read'] ? 'border-l-2 border-blue-500 bg-blue-50/30' : 'opacity-80' }} notification-item"
                    data-id="{{ $notification['id'] }}" @click="markAsReadHandler({{ $notification['id'] }})"
                    @click.stop>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div
                                class="@if ($notification['badge_color'] === 'blue') bg-blue-100 text-blue-600
                                       @elseif($notification['badge_color'] === 'green') bg-green-100 text-green-600
                                       @elseif($notification['badge_color'] === 'gray') bg-gray-100 text-gray-600
                                       @else bg-gray-100 text-gray-600 @endif p-2 rounded-full">
                                <i class="{{ $notification['icon'] ?? 'fas fa-bell' }} text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            {{-- Badge Type --}}
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-full {{ $notification['type_class'] }}">
                                    @if ($notification['type'] == 'chat')
                                        <i class="fas fa-comment-alt mr-1 text-xs"></i> Chat
                                    @elseif($notification['type'] == 'approval')
                                        <i class="fas fa-clipboard-check mr-1 text-xs"></i> Approval
                                    @else
                                        <i class="fas fa-bell mr-1 text-xs"></i> Other
                                    @endif
                                </span>
                                @if (!$notification['read'])
                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                @endif
                            </div>

                            <p class="text-sm font-semibold text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p
                                class="text-xs @if (!$notification['read']) text-blue-600 font-medium @else text-gray-400 @endif mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($notification['time'])->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            @if (!$notification['read'])
                                <button @click.stop="markAsReadHandler({{ $notification['id'] }})"
                                    class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded mark-read-btn"
                                    title="Mark as read" data-id="{{ $notification['id'] }}">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            @endif
                            <button wire:click.stop="deleteNotification({{ $notification['id'] }})"
                                onclick="return confirm('Delete this notification?')" @click.stop
                                class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded"
                                title="Delete notification">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="p-8 text-center text-gray-500" @click.stop>
                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                    <p class="text-sm">No notifications</p>
                    <p class="text-xs text-gray-400 mt-1">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        {{-- Polling Indicator --}}
        <div class="px-3 py-1 border-t border-gray-100 text-center" @click.stop>
            <span class="text-xs text-gray-400">
                <i class="fas fa-sync-alt fa-spin text-xs mr-1"></i>
                Auto-refresh every <span x-text="pollingTimeDisplay"></span>s
            </span>
        </div>

        {{-- Script dan Style MASIH dalam root div --}}
        <script>
            function notificationBell() {
                return {
                    isOpen: @json($isOpen),
                    pollingInterval: null,
                    fastPollingInterval: null,
                    pollingTimeDisplay: 10,

                    init() {
                        console.log('Notification bell initialized');
                        this.startPolling(10000);

                        this.setupEventListeners();
                        this.setupTabVisibility();

                        // Prevent dropdown close when clicking inside
                        this.preventDropdownClose();
                    },

                    setupEventListeners() {
                        Livewire.on('notification-marked-read', (data) => {
                            if (data && data.id) {
                                this.fadeOutNotification(data.id);
                            }
                        });

                        Livewire.on('notifications-updated', () => {
                            console.log('Notifications updated event received');
                        });
                    },

                    setupTabVisibility() {
                        document.addEventListener('visibilitychange', () => {
                            if (!document.hidden) {
                                console.log('Tab became visible - refreshing notifications');
                                @this.loadNotifications();
                            }
                        });
                    },

                    preventDropdownClose() {
                        const dropdownContent = this.$el.querySelector('[x-show]');
                        if (dropdownContent) {
                            dropdownContent.addEventListener('click', (e) => {
                                e.stopPropagation();
                            });
                        }
                    },

                    toggleDropdown() {
                        this.isOpen = !this.isOpen;
                        if (this.isOpen) {
                            @this.loadNotifications();
                        }

                        this.updatePollingBasedOnState();
                    },

                    closeDropdown() {
                        this.isOpen = false;
                        this.updatePollingBasedOnState();
                    },

                    updatePollingBasedOnState() {
                        console.log('Dropdown state:', this.isOpen ? 'OPEN' : 'CLOSED');

                        if (this.isOpen) {
                            this.stopAllPolling();
                            this.startFastPolling();
                        } else {
                            this.stopAllPolling();
                            this.startPolling(10000);
                        }
                    },

                    startPolling(interval = 10000) {
                        if (this.pollingInterval) {
                            clearInterval(this.pollingInterval);
                        }

                        this.pollingTimeDisplay = interval / 1000;
                        console.log('Starting normal polling:', interval + 'ms');

                        this.pollingInterval = setInterval(() => {
                            console.log('Polling (normal) - Loading notifications...');
                            @this.loadNotifications();
                        }, interval);
                    },

                    startFastPolling() {
                        if (this.fastPollingInterval) {
                            clearInterval(this.fastPollingInterval);
                        }

                        this.pollingTimeDisplay = 5;
                        console.log('Starting fast polling: 5000ms');

                        this.fastPollingInterval = setInterval(() => {
                            console.log('Polling (fast) - Loading notifications...');
                            @this.loadNotifications();
                        }, 5000);
                    },

                    stopAllPolling() {
                        if (this.pollingInterval) {
                            clearInterval(this.pollingInterval);
                            this.pollingInterval = null;
                        }
                        if (this.fastPollingInterval) {
                            clearInterval(this.fastPollingInterval);
                            this.fastPollingInterval = null;
                        }
                        console.log('All polling stopped');
                    },

                    fadeOutNotification(notificationId) {
                        const notificationElement = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                        if (notificationElement) {
                            notificationElement.classList.add('fading');

                            setTimeout(() => {
                                if (notificationElement.parentNode) {
                                    notificationElement.remove();

                                    const list = document.getElementById('notification-list');
                                    if (list && list.children.length === 0) {
                                        list.innerHTML = `
                                            <div class="p-8 text-center text-gray-500" @click.stop>
                                                <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                                <p class="text-sm">No notifications</p>
                                                <p class="text-xs text-gray-400 mt-1">You're all caught up!</p>
                                            </div>
                                        `;
                                    }
                                }
                            }, 300);

                            console.log('Notification faded out:', notificationId);
                        }
                    },

                    markAsReadHandler(notificationId) {
                        console.log('Mark as read handler called:', notificationId);
                        @this.markAsRead(notificationId);
                        this.fadeOutNotification(notificationId);
                    }
                };
            }

            // Initialize Alpine
            document.addEventListener('alpine:init', () => {
                Alpine.data('notificationBell', notificationBell);
            });

            // Fallback polling
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const alpineComponent = Alpine.$data(document.querySelector(
                        '[x-data="notificationBell()"]'));
                    if (!alpineComponent || !alpineComponent.pollingInterval) {
                        console.log('Starting fallback polling...');

                        let pollingInterval = setInterval(() => {
                            if (window.Livewire && window.Livewire.find('notification-bell')) {
                                window.Livewire.find('notification-bell').call('loadNotifications');
                            }
                        }, 10000);

                        window.addEventListener('beforeunload', () => {
                            clearInterval(pollingInterval);
                        });
                    }
                }, 1000);
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

            /* Animasi fade out */
            .notification-item.fading {
                animation: fadeOut 0.3s ease-out forwards;
                pointer-events: none;
            }

            @keyframes fadeOut {
                0% {
                    opacity: 1;
                    transform: translateX(0);
                    max-height: 200px;
                }

                50% {
                    opacity: 0.5;
                }

                100% {
                    opacity: 0;
                    transform: translateX(-20px);
                    max-height: 0;
                    padding-top: 0;
                    padding-bottom: 0;
                    margin-top: 0;
                    margin-bottom: 0;
                    border: 0;
                }
            }

            /* Smooth cursor */
            .notification-item {
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .notification-item:hover {
                background-color: rgba(243, 244, 246, 0.8);
            }

            /* Pulse animation */
            .animate-pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }
        </style>
    </div>
</div>