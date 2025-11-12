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
        <div class="sidebar-logo flex items-center justify-between px-5 py-4 border-b border-white/20">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width: 90%; filter: brightness(0) invert(1);">

            </div>
            <button id="closeSidebar" class="md:hidden text-white text-2xl font-bold">&times;</button>
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
        <div class="text-center text-sm text-white/70 border-t border-white/20 py-3">
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
                                    class="text-red-500 hover:bg-red-50 px-3 py-1 rounded-md transition">Logout</button>
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

</body>
</html>



    {{-- https://code.jquery.com/jquery-3.7.1.js
    https://cdn.tailwindcss.com
    https://cdn.datatables.net/2.3.4/js/dataTables.js
    https://cdn.datatables.net/2.3.4/js/dataTables.tailwindcss.js
    https://cdn.tailwindcss.com --}}