<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary: #004A8F;
            --primary-light: #1E90D1;
            --primary-lighter: #66C6F0;
        }

        .sidebar-link {
            @apply flex items-center px-4 py-2 rounded-lg transition-all duration-200;
        }

        .sidebar-link:hover {
            background-color: var(--primary-light);
            color: white;
        }

        .sidebar-link.active {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }

        .submenu-link {
            @apply block px-4 py-1.5 text-sm rounded-md transition-all duration-200;
        }

        .submenu-link:hover {
            background-color: var(--primary-lighter);
            color: white;
        }

        .submenu-link.active {
            background-color: var(--primary-light);
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

@php
use App\Models\Menu;
$menus = Menu::whereNull('parent_id')
    ->where('is_active', true)
    ->orderBy('order')
    ->with(['children' => function ($query) {
        $query->where('is_active', true)->orderBy('order');
    }])
    ->get();
@endphp

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white shadow-lg border-r hidden md:flex flex-col">
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8">
                <h1 class="text-lg font-bold text-[var(--primary)]">RBAC System</h1>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
            @foreach ($menus as $menu)
                <div>
                    <a href="{{ $menu->route ? route($menu->route) : '#' }}"
                       class="sidebar-link {{ request()->routeIs($menu->route) ? 'active' : 'text-gray-700 hover:text-white' }}">
                        @if($menu->icon)
                            <i class="{{ $menu->icon }} mr-2"></i>
                        @endif
                        {{ $menu->name }}
                    </a>

                    {{-- Submenu --}}
                    @if ($menu->children->count())
                        <div class="ml-6 mt-1 border-l-2 border-gray-200 pl-3 space-y-1">
                            @foreach ($menu->children as $child)
                                <a href="{{ $child->route ? route($child->route) : '#' }}"
                                   class="submenu-link {{ request()->routeIs($child->route) ? 'active' : 'text-gray-600 hover:text-white' }}">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col">

        {{-- HEADER / NAVBAR --}}
        <header class="bg-white shadow-sm border-b sticky top-0 z-20">
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3">
                    {{-- tombol menu mobile --}}
                    <button id="sidebarToggle" class="md:hidden p-2 rounded hover:bg-gray-100">
                        ☰
                    </button>
                    <h2 class="text-lg font-semibold text-[var(--primary)]">{{ $title ?? 'Dashboard' }}</h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="font-medium text-gray-700">{{ auth()->user()->name ?? 'Guest' }}</span>
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded bg-red-100 text-red-600 hover:bg-red-200 transition">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="p-6">
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

{{-- Script toggle sidebar mobile --}}
<script>
    const btn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('aside');
    btn?.addEventListener('click', () => sidebar.classList.toggle('hidden'));
</script>

@livewireScripts
</body>
</html>
