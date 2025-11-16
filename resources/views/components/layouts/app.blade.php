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
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

     

<div class="flex min-h-screen">
   


    {{-- SIDEBAR --}}
    <aside id="sidebar"
           class="sidebar sidebar-gradient w-72 text-white shadow-2xl flex flex-col transition-all duration-300">

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

    {{-- Tombol close sidebar untuk mobile --}}
    <button id="closeSidebar" class="md:hidden text-white text-2xl font-bold mt-3">&times;</button>
</div>

{{-- {{dd($module_permissions)}} --}}
        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-2">
            @foreach ($menus as $menu)
                @php
                    $hasAccessibleChildren = $menu->children->count() > 0;
                    $isActiveParent = request()->routeIs($menu->route) || 
                                     $menu->children->contains(function($child) {
                                         return request()->routeIs($child->route);
                                     });
                @endphp
                
                <div>
                    <a href="{{ $menu->route ? route($menu->route) : '#' }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-200 hover:bg-white/10
                       {{ $isActiveParent ? 'active-link' : '' }}
                       {{ !$menu->route && !$hasAccessibleChildren ? 'cursor-not-allowed opacity-50' : '' }}">
                        @if($menu->icon)
                            <i data-lucide="{{ $menu->icon }}" class="w-5 h-5"></i>
                        @endif
                        <span class="menu-text">{{ $menu->name }}</span>
                        
                        {{-- Indicator untuk menu dengan children --}}
                        @if($hasAccessibleChildren)
                            <i data-lucide="chevron-down" class="w-4 h-4 ml-auto menu-text"></i>
                        @endif
                    </a>

                    @if ($hasAccessibleChildren)
                        <div class="ml-6 mt-1 border-l border-white/20 pl-3 space-y-1">
                            @foreach ($menu->children as $child)
                                <a href="{{ $child->route ? route($child->route) : '#' }}"
                                   class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-md transition hover:bg-white/10
                                   {{ request()->routeIs($child->route) ? 'active-link' : '' }}">
                                    <i data-lucide="circle" class="w-3 h-3"></i>
                                    <span class="menu-text">{{ $child->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            
            {{-- Fallback jika tidak ada menu yang accessible --}}
            @if($menus->count() == 0)
                <div class="text-center text-white/60 py-8">
                    <i data-lucide="lock" class="w-8 h-8 mx-auto mb-2"></i>
                    <p class="text-sm">No accessible menus</p>
                </div>
            @endif
        </nav>

        {{-- Footer --}}
        <div class="text-center text-sm text-white border-t border-white/20 py-3">
            © {{ date('Y') }} {{env("APP_NAME")}}
        </div>
    </aside>

    {{-- Overlay for mobile --}}
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 hidden md:hidden z-30"></div>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        {{-- Header --}}
        <header class="header-sticky sticky bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3">
                    <button id="sidebarToggle" class="md:hidden p-2 rounded hover:bg-gray-100 text-gray-700 text-xl">
                        ☰
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="font-medium text-gray-700">{{ auth()->user()->name ?? 'Guest' }}</span>
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="
                                    inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <span class="whitespace-nowrap
                        ">
                    <span class="whitespace-nowrap">Logout</span>
                        <i class="fas fa-sign-out ml-1.5 text-xs"></i>
                    </button>
                        </form>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="content-area">
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<div x-data="notificationHandler()" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50 max-w-sm w-full"
         style="display: none;"
         @notify.window="showNotification($event.detail)">
        
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
                }" class="mt-0.5 mr-3"></i>
                <div class="flex-1">
                    <p x-text="message" class="text-sm"></p>
                </div>
                <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    
@livewireScripts
<script>
     window.AppConfig = {
        routes: {
            languageChange: '{{ route('language.change') }}'
        },
        csrfToken: '{{ csrf_token() }}',
     };
</script>
    <script src="{{ asset('assets/js/admin.js') }}"></script>

    <script>
    function notificationHandler() {
        return {
            show: false,
            type: '',
            message: '',
            timer: null,
            
            showNotification(detail) {
                console.log('Detail received:', detail); // Debug log
                
                // Handle both object and array formats
                let notificationData;
                if (Array.isArray(detail)) {
                    // Jika detail adalah array, ambil element pertama
                    notificationData = detail[0];
                } else {
                    // Jika detail adalah object langsung
                    notificationData = detail;
                }
                
                console.log('Processed data:', notificationData); // Debug log
                
                this.type = notificationData.type;
                this.message = notificationData.message;
                this.show = true;
                
                // Clear existing timer
                if (this.timer) {
                    clearTimeout(this.timer);
                }
                
                // Auto hide after 5 seconds
                this.timer = setTimeout(() => {
                    this.show = false;
                }, 5000);
            }
        }
    }

    // Handle session flash messages
    document.addEventListener('DOMContentLoaded', function() {
        // Check for existing flash messages in session
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
    
{{-- Di layout utama atau component view --}}
@if(env('CHAT_REALTIME', false))
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.dispatch('initialize-echo');
    });

    // Atau menggunakan Alpine.js
    document.addEventListener('alpine:init', () => {
        Alpine.data('chat', () => ({
            init() {
                if (@json(env('CHAT_REALTIME', false))) {
                    this.initializeEcho();
                }
            },
            
            initializeEcho() {
                // Initialize Pusher
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: @json(env('PUSHER_APP_KEY')),
                    cluster: @json(env('PUSHER_APP_CLUSTER')),
                    encrypted: true
                });
            }
        }));
    });
</script>
@endif

</body>
</html>



    {{-- https://code.jquery.com/jquery-3.7.1.js
    https://cdn.tailwindcss.com
    https://cdn.datatables.net/2.3.4/js/dataTables.js
    https://cdn.datatables.net/2.3.4/js/dataTables.tailwindcss.js
    https://cdn.tailwindcss.com --}}