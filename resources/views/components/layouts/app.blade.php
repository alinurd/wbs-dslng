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
    {{-- Lucide Icons --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Style untuk sidebar */
        #sidebar {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        
        #sidebar.open {
            transform: translateX(0);
        }
        
        @media (min-width: 768px) {
            #sidebar {
                transform: translateX(0) !important;
                position: relative !important;
            }
        }
        
        /* Overlay untuk mobile */
        #sidebarOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 35;
        }
        
        #sidebarOverlay.active {
            display: block;
        }
        
        /* Pastikan main content tidak overflow di mobile */
        body.sidebar-open {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside id="sidebar"
            class="sidebar-gradient w-72 text-white shadow-2xl flex flex-col fixed md:relative left-0 top-0 h-full z-40">
            
            {{-- Logo & Title --}}
            <div class="sidebar-logo flex flex-col items-center px-5 py-4 border-b border-white/20">
                <div class="flex flex-col items-center gap-2">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                        class="max-w-[90%] p-1 filter brightness-0 invert">

                    {{-- Nama user dan role --}}
                    <div class="text-center mt-2">
                        <p class="text-white text-base font-semibold">{{ $user->name }}</p>

                        {{-- Roles --}}
                        @php
                            $roles = $user->getRoleNames()->implode(', ');
                        @endphp
                        <p class="text-xs text-white/70 italic">{{ $roles ?: 'No Role' }}</p>
                    </div>
                </div>

                <button id="closeSidebarBtn" class="md:hidden text-white text-2xl font-bold mt-3">&times;</button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-2" id="sidebarNav">
                @foreach ($menus as $menu)
                    @php
                        $hasAccessibleChildren = $menu->children->count() > 0;
                        $isActiveParent = request()->routeIs($menu->route) || 
                                         $menu->children->contains(function($child) {
                                             return request()->routeIs($child->route);
                                         });
                        $menuId = 'menu-' . Str::slug($menu->name);
                    @endphp

                    <div class="menu-item">
                        <a href="{{ $menu->route ? route($menu->route) : '#' }}"
                           @if($hasAccessibleChildren)
                               onclick="event.preventDefault(); toggleSubmenu('{{ $menuId }}')"
                           @endif
                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-200 hover:bg-white/10
                           {{ $isActiveParent ? 'active-link' : '' }}
                           {{ !$menu->route && !$hasAccessibleChildren ? 'cursor-not-allowed opacity-50' : '' }}">
                            
                            @if($menu->icon)
                                <i class="{{ $menu->icon }} w-5 h-5 text-center"></i>
                            @else
                                <i class="w-5 h-5 text-center text-white/60"></i>
                            @endif
                            
                            <span class="menu-text flex-1">{{ $menu->name }}</span>
                            
                            {{-- Indicator untuk menu dengan children --}}
                            @if($hasAccessibleChildren)
                                <i id="chevron-{{ $menuId }}" 
                                    class="fas fa-chevron-down w-4 h-4 transition-transform duration-200 ml-auto"></i>
                            @endif
                        </a>

                        @if ($hasAccessibleChildren)
                            <div id="submenu-{{ $menuId }}" 
                                 class="submenu ml-6 mt-1 border-l border-white/20 pl-3 space-y-1 hidden">
                                @foreach ($menu->children as $child)
                                    <a href="{{ $child->route ? route($child->route) : '#' }}"
                                       class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-md transition hover:bg-white/10
                                       {{ request()->routeIs($child->route) ? 'active-link' : '' }}">
                                        <i class="fas fa-circle text-xs text-white/70"></i>
                                        <span class="menu-text">{{ $child->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Fallback jika tidak ada menu yang accessible --}}
                @if ($menus->count() == 0)
                    <div class="text-center text-white/60 py-8">
                        <i class="fas fa-lock text-2xl mb-2"></i>
                        <p class="text-sm">No accessible menus</p>
                    </div>
                @endif
            </nav>

            {{-- Footer --}}
            <div class="text-center text-sm text-white border-t border-white/20 py-3">
                © {{ date('Y') }} {{ env('APP_NAME') }}
            </div>
        </aside>

        {{-- Overlay for mobile --}}
        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        {{-- MAIN CONTENT --}}
        <div class="main-content flex-1 w-full min-h-screen">
            {{-- Header --}}
            <header class="header-sticky sticky top-0 bg-white shadow-sm border-b z-30">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle"
                            class="p-2 rounded hover:bg-gray-100 text-gray-700 text-xl md:hidden">
                            ☰
                        </button>
                        <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Notification Bell --}}
                        {{-- <livewire:notification-bell /> --}}

                        {{-- User Info & Logout --}}
                        <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
                            <span class="font-medium text-gray-700">{{ auth()->user()->name ?? 'Guest' }}</span>
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
            <main class="content-area p-4">
                <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Notification Component --}}
    <div id="notification" class="fixed top-4 right-4 z-50 max-w-sm w-full hidden">
        <div id="notificationContent" class="p-4 border rounded-lg shadow-lg">
            <div class="flex items-start">
                <i id="notificationIcon" class="mt-0.5 mr-3"></i>
                <div class="flex-1">
                    <p id="notificationMessage" class="text-sm"></p>
                </div>
                <button onclick="hideNotification()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @livewireScripts
    
    <script>
        // Simple sidebar toggle function
        function setupSidebar() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn'); // Ganti nama variabel
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            console.log('Setting up sidebar...'); // Debug log
            
            // Fungsi untuk menutup sidebar
            function closeSidebar() {
                console.log('Closing sidebar'); // Debug log
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
            
            // Toggle sidebar function
            function toggleSidebar() {
                console.log('Toggle sidebar clicked'); // Debug log
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    console.log('Opening sidebar'); // Debug log
                    sidebar.classList.add('open');
                    sidebarOverlay.classList.add('active');
                    document.body.classList.add('sidebar-open');
                }
            }
            
            // Event listeners
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
                console.log('Sidebar toggle button found'); // Debug log
            } else {
                console.log('Sidebar toggle button NOT found'); // Debug log
            }
            
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', closeSidebar);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }
            
            // Close sidebar on mobile when clicking a link
            document.querySelectorAll('#sidebarNav a[href]').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeSidebar();
                    }
                });
            });
            
            // Initialize active menus
            initializeActiveMenus();
        }

        // Fungsi untuk toggle submenu
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById('submenu-' + menuId);
            const chevron = document.getElementById('chevron-' + menuId);
            
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                submenu.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Inisialisasi menu yang aktif
        function initializeActiveMenus() {
            @foreach ($menus as $menu)
                @php
                    $hasAccessibleChildren = $menu->children->count() > 0;
                    $isActiveParent = request()->routeIs($menu->route) || 
                                     $menu->children->contains(function($child) {
                                         return request()->routeIs($child->route);
                                     });
                    $menuId = 'menu-' . Str::slug($menu->name);
                @endphp
                @if($hasAccessibleChildren && $isActiveParent)
                    // Buka menu yang aktif
                    const submenu = document.getElementById('submenu-{{ $menuId }}');
                    const chevron = document.getElementById('chevron-{{ $menuId }}');
                    if (submenu && chevron) {
                        submenu.classList.remove('hidden');
                        chevron.classList.add('rotate-180');
                    }
                @endif
            @endforeach
        }

        // Fungsi untuk menampilkan notifikasi
        function showNotification(type, message) {
            const notification = document.getElementById('notification');
            const notificationContent = document.getElementById('notificationContent');
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationMessage = document.getElementById('notificationMessage');
            
            // Set kelas berdasarkan type
            const classes = {
                'success': 'bg-green-50 border-green-200 text-green-700',
                'error': 'bg-red-50 border-red-200 text-red-700',
                'info': 'bg-blue-50 border-blue-200 text-blue-700',
                'warning': 'bg-yellow-50 border-yellow-200 text-yellow-700'
            };
            
            const icons = {
                'success': 'fas fa-check-circle text-green-500',
                'error': 'fas fa-exclamation-triangle text-red-500',
                'info': 'fas fa-info-circle text-blue-500',
                'warning': 'fas fa-exclamation-circle text-yellow-500'
            };
            
            // Reset classes
            notificationContent.className = 'p-4 border rounded-lg shadow-lg';
            notificationIcon.className = 'mt-0.5 mr-3';
            
            // Add new classes
            notificationContent.classList.add(...classes[type].split(' '));
            notificationIcon.classList.add(...icons[type].split(' '));
            
            // Set message
            notificationMessage.textContent = message;
            
            // Show notification
            notification.classList.remove('hidden');
            
            // Auto hide after 5 seconds
            setTimeout(hideNotification, 5000);
        }

        function hideNotification() {
            document.getElementById('notification').classList.add('hidden');
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded'); // Debug log
            setupSidebar();
            
            // Handle Livewire events untuk notifikasi
            if (window.Livewire) {
                window.Livewire.on('notify', (data) => {
                    if (Array.isArray(data)) {
                        showNotification(data[0].type, data[0].message);
                    } else {
                        showNotification(data.type, data.message);
                    }
                });
            }
            
            // Check for existing flash messages in session
            @if (session()->has('success'))
                showNotification('success', '{{ session('success') }}');
            @endif

            @if (session()->has('error'))
                showNotification('error', '{{ session('error') }}');
            @endif

            @if (session()->has('warning'))
                showNotification('warning', '{{ session('warning') }}');
            @endif

            @if (session()->has('info'))
                showNotification('info', '{{ session('info') }}');
            @endif
        });

        // Juga initialize ketika Livewire selesai load
        document.addEventListener('livewire:load', function() {
            console.log('Livewire loaded'); // Debug log
            setupSidebar();
        });
    </script>

</body>

</html>