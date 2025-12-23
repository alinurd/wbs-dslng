<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PT DONGGI-SENORO LNG' }}</title>
    <link rel="Shortcut Icon" href="{{ asset('assets/images/logo_donggi.ico') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.tailwindcss.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .truncated-text {
            display: none;
        }
        .sidebar-collapsed nav a:hover .truncated-text {
            display: block;
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            margin-left: 8px;
            z-index: 1000;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <div class="flex min-h-screen">
        {{-- SIDEBAR --}}
        <aside id="sidebar"
            class="sidebar-gradient text-white shadow-2xl flex flex-col h-screen ">
            
            {{-- Logo & Title --}}
            <div class="sidebar-logo flex flex-col items-center px-5 py-4 border-b border-white/20">
                <div class="flex flex-col items-center gap-2 w-full">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                        class="max-w-[90%] p-1 filter brightness-0 invert transition-all duration-300 sidebar-logo-img">
                    
                    {{-- Nama user dan role --}}
                    <div class="text-center mt-2 w-full transition-all duration-300">
                        <p class="text-white text-base font-semibold">{{ $user->name }}</p>
                        @php
                            $roles = $user->getRoleNames()->implode(', ');
                        @endphp
                        <p class="text-xs text-white/70 italic">{{ $roles ?: 'No Role' }}</p>
                    </div>
                </div>

                {{-- Close button for mobile --}}
                <button id="closeSidebar" 
                        class="md:hidden text-white text-2xl font-bold hover:bg-white/10 w-8 h-8 flex items-center justify-center rounded-full transition-colors mt-2">
                    &times;
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-2 sticky top-0" x-data="sidebarNavigation()">
                @foreach ($menus as $menu)
                    @php
                        $hasAccessibleChildren = $menu->children->count() > 0;
                        $isActiveParent =
                            request()->routeIs($menu->route) ||
                            $menu->children->contains(function ($child) {
                                return request()->routeIs($child->route);
                            });
                        $menuId = 'menu-' . Str::slug($menu->name);
                        // Ambil 3 huruf pertama untuk mode collapsed
                        $truncatedName = substr($menu->name, 0, 3);
                    @endphp

                    <div class="menu-item">
                        <a href="{{ $menu->route ? route($menu->route) : '#' }}"
                            @if ($hasAccessibleChildren) @click.prevent="toggleMenu('{{ $menuId }}')" @endif
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 hover:bg-white/10
                                   {{ $isActiveParent ? 'active-link bg-white/20' : '' }}
                                   {{ !$menu->route && !$hasAccessibleChildren ? 'cursor-not-allowed opacity-50' : '' }}">
                            
                            @if ($menu->icon)
                                <i class="{{ $menu->icon }} w-5 h-5 text-center"></i>
                            @else
                                <i class="w-5 h-5 text-center text-white/60"></i>
                            @endif

                            <span class="menu-text flex-1 transition-all duration-300">{{ $menu->name }}</span>
                            
                            {{-- Truncated text for collapsed mode --}}
                            <span class="truncated-text">{{ $truncatedName }} </span>

                            @if ($hasAccessibleChildren)
                                <i :class="isOpen('{{ $menuId }}') ? 'rotate-180 transform' : ''"
                                    class="fas fa-chevron-down w-4 h-4 transition-transform duration-200 ml-auto menu-text"></i>
                            @endif
                        </a>

                        @if ($hasAccessibleChildren)
                            <div x-show="isOpen('{{ $menuId }}')" x-collapse
                                class="ml-6 mt-1 border-l border-white/20 pl-3 space-y-1 menu-text">
                                @foreach ($menu->children as $child)
                                    <a href="{{ $child->route ? route($child->route) : '#' }}"
                                        class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-md transition hover:bg-white/10
                                               {{ request()->routeIs($child->route) ? 'active-link bg-white/15' : '' }}"
                                        @click="closeOnMobile()">
                                        <i class="fas fa-circle text-xs text-white/70"></i>
                                        <span class="menu-text">{{ $child->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                @if ($menus->count() == 0)
                    <div class="text-center text-white/60 py-8 menu-text">
                        <i class="fas fa-lock text-2xl mb-2"></i>
                        <p class="text-sm">No accessible menus</p>
                    </div>
                @endif
            </nav>

            {{-- Footer --}}
            <div class="text-center text-sm text-white border-t border-white/20 py-3 px-3 menu-text">
                © {{ date('Y') }} PT Donggi Senoro LNG
            </div>
        </aside>

        {{-- Overlay for mobile --}}
        <div id="sidebarBackdrop" 
             class="hidden fixed inset-0 bg-black/50 z-35 transition-opacity duration-300">
        </div>

        {{-- MAIN CONTENT --}}
        <div class="main-content flex-1 min-h-screen flex flex-col transition-all duration-300">
            {{-- Header --}}
            <header class="header-sticky sticky top-0 bg-white shadow-sm border-b z-20">
                <div class="flex items-center justify-between px-4 md:px-6 py-3">
                    <div class="flex items-center gap-3">
                        {{-- Hamburger menu for mobile --}}
                        <button id="sidebarToggle"
                            class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-colors"
                            aria-label="Toggle sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        {{-- Desktop toggle button --}}
                        <button id="desktopToggle" 
                                class="desktop-toggle-btn items-center justify-center p-2 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors hidden"
                                title="Toggle Sidebar">
                            <i class="fas fa-chevron-left" id="toggleIcon"></i>
                        </button>
                        
                        <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $title ?? 'Dashboard' }}</h2>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Notification Bell --}}
                        <livewire:notification-bell />

                        {{-- User Info & Logout --}}
                        <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
                            <span class="font-medium text-gray-700 hidden sm:inline">{{ auth()->user()->name ?? 'Guest' }}</span>
                            @auth
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                                        <span class="whitespace-nowrap">Logout</span>
                                        <i class="fas fa-sign-out ml-1.5 text-xs"></i>
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="content-area flex-1 p-4 md:p-6">
                <div class="bg-white shadow-sm rounded-xl p-4 md:p-6 border border-gray-100 h-full">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Notification Component --}}
    <div x-data="notificationHandler()" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed top-4 right-4 z-50 max-w-sm w-full"
        style="display: none;" @notify.window="showNotification($event.detail)">

        <div x-bind:class="{
            'bg-green-50 border-green-200 text-green-700': type === 'success',
            'bg-red-50 border-red-200 text-red-700': type === 'error',
            'bg-blue-50 border-blue-200 text-blue-700': type === 'info',
            'bg-yellow-50 border-yellow-200 text-yellow-700': type === 'warning'
        }"
            class="p-4 border rounded-lg shadow-lg">
            <div class="flex items-start">
                <i x-bind:class="{
                    'fas fa-check-circle text-green-500': type === 'success',
                    'fas fa-exclamation-triangle text-red-500': type === 'error',
                    'fas fa-info-circle text-blue-500': type === 'info',
                    'fas fa-exclamation-circle text-yellow-500': type === 'warning'
                }"
                    class="mt-0.5 mr-3"></i>
                <div class="flex-1">
                    <p x-text="message" class="text-sm"></p>
                </div>
                <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @livewireScripts
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const mobileToggle = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('closeSidebar');
            const desktopToggle = document.getElementById('desktopToggle');
            const toggleIcon = document.getElementById('toggleIcon');
            const mainContent = document.querySelector('.main-content');
            const sidebarLogoImg = document.querySelector('.sidebar-logo-img');
            
            // Load saved state from localStorage
            const savedCollapsed = localStorage.getItem('sidebarCollapsed');
            if (savedCollapsed === 'true' && window.innerWidth >= 768) {
                sidebar.classList.add('sidebar-collapsed');
                updateToggleIcon(true);
            } else {
                updateToggleIcon(false);
            }
            
            // Update logo based on sidebar state
            function updateLogo(isCollapsed) {
                if (sidebarLogoImg) {
                    if (isCollapsed) {
                        // Use favicon when collapsed
                        sidebarLogoImg.src = "{{ asset('assets/images/logo_donggi.ico') }}";
                        sidebarLogoImg.style.maxWidth = '40px';
                    } else {
                        // Use normal logo when expanded
                        sidebarLogoImg.src = "{{ asset('assets/images/logo.png') }}";
                        sidebarLogoImg.style.maxWidth = '90%';
                    }
                }
            }
            
            // Update toggle icon
            function updateToggleIcon(isCollapsed) {
                if (toggleIcon) {
                    if (isCollapsed) {
                        toggleIcon.classList.remove('fa-chevron-left');
                        toggleIcon.classList.add('fa-chevron-right');
                    } else {
                        toggleIcon.classList.remove('fa-chevron-right');
                        toggleIcon.classList.add('fa-chevron-left');
                    }
                }
            }
            
            // Mobile sidebar functions
            function showMobileSidebar() {
                sidebar.classList.add('open');
                sidebarBackdrop.classList.remove('hidden');
                sidebarBackdrop.classList.add('show');
                document.body.classList.add('sidebar-open');
            }
            
            function hideMobileSidebar() {
                sidebar.classList.remove('open');
                sidebarBackdrop.classList.remove('show');
                sidebarBackdrop.classList.add('hidden');
                document.body.classList.remove('sidebar-open');
            }
            
            // Desktop toggle function
            function toggleDesktopSidebar() {
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                
                if (isCollapsed) {
                    // Expand sidebar
                    sidebar.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'false');
                    updateToggleIcon(false);
                    updateLogo(false);
                } else {
                    // Collapse sidebar
                    sidebar.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'true');
                    updateToggleIcon(true);
                    updateLogo(true);
                }
            }
            
            // Event listeners
            if (mobileToggle) {
                mobileToggle.addEventListener('click', showMobileSidebar);
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', hideMobileSidebar);
            }
            
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', hideMobileSidebar);
            }
            
            if (desktopToggle) {
                desktopToggle.addEventListener('click', toggleDesktopSidebar);
            }
            
            // Handle window resize
            function handleResize() {
                if (window.innerWidth >= 768) {
                    // On desktop/tablet, ensure sidebar is visible
                    hideMobileSidebar();
                } else {
                    // On mobile, ensure sidebar is hidden by default
                    if (!sidebar.classList.contains('open')) {
                        sidebar.classList.remove('open');
                    }
                    // Remove collapsed state on mobile
                    sidebar.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'false');
                    updateToggleIcon(false);
                    updateLogo(false);
                }
            }
            
            window.addEventListener('resize', handleResize);
            handleResize(); // Initial check
            
            // Close sidebar when clicking on mobile links
            const mobileLinks = document.querySelectorAll('#sidebar a[href]');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth < 768) {
                        hideMobileSidebar();
                    }
                });
            });
            
            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth < 768) {
                    hideMobileSidebar();
                }
            });
            
            // Initial logo setup
            updateLogo(sidebar.classList.contains('sidebar-collapsed'));
        });

        // Sidebar Navigation Alpine Component
        function sidebarNavigation() {
            return {
                openMenus: {},
                isMobile: window.innerWidth < 768,

                init() {
                    // Cek ukuran layar
                    this.checkScreenSize();
                    window.addEventListener('resize', () => this.checkScreenSize());
                },
                
                checkScreenSize() {
                    this.isMobile = window.innerWidth < 768;
                },

                toggleMenu(menuId) {
                    this.openMenus[menuId] = !this.openMenus[menuId];
                },

                isOpen(menuId) {
                    return this.openMenus[menuId] || false;
                },
                
                closeOnMobile() {
                    if (this.isMobile) {
                        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
                        const sidebar = document.getElementById('sidebar');
                        if (sidebarBackdrop && sidebar) {
                            sidebarBackdrop.classList.remove('show');
                            sidebarBackdrop.classList.add('hidden');
                            sidebar.classList.remove('open');
                            document.body.classList.remove('sidebar-open');
                        }
                    }
                }
            }
        }
        
        // Notification Handler
        function notificationHandler() {
            return {
                show: false,
                type: '',
                message: '',
                timer: null,
                
                showNotification(detail) {
                    let notificationData = Array.isArray(detail) ? detail[0] : detail;
                    this.type = notificationData.type;
                    this.message = notificationData.message;
                    this.show = true;
                    
                    if (this.timer) clearTimeout(this.timer);
                    this.timer = setTimeout(() => this.show = false, 5000);
                }
            }
        }

        // Handle session flash messages
        document.addEventListener('DOMContentLoaded', function() {
            @if (session()->has('success'))
                Livewire.dispatch('notify', {
                    type: 'success',
                    message: '{{ session('success') }}'
                });
            @endif

            @if (session()->has('error'))
                Livewire.dispatch('notify', {
                    type: 'error', 
                    message: '{{ session('error') }}'
                });
            @endif

            @if (session()->has('warning'))
                Livewire.dispatch('notify', {
                    type: 'warning',
                    message: '{{ session('warning') }}'
                });
            @endif

            @if (session()->has('info'))
                Livewire.dispatch('notify', {
                    type: 'info',
                    message: '{{ session('info') }}'
                });
            @endif
        });
    </script>

    @if (env('CHAT_REALTIME', false))
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.dispatch('initialize-echo');
            });
        </script>
    @endif

</body>
</html>