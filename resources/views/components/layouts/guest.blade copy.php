<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RBAC System' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --color-primary: #003B73;
            --color-secondary: #0077C8;
            --color-accent: #6EC1E4;
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary), var(--color-accent));
        }

        .btn-primary {
            background: var(--color-primary);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--color-secondary);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50">
    {{-- Navigation --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full bg-gray-100 p-1">
                    <span class="text-xl font-bold text-gray-800">RBAC System</span>
                </div>

                {{-- Navigation Links --}}
                <div class="flex items-center gap-6">
                    <a href="#features" class="text-gray-600 hover:text-gray-900 transition">Features</a>
                    <a href="#about" class="text-gray-600 hover:text-gray-900 transition">About</a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Dashboard
                        </a>
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" 
                               class="text-gray-600 hover:text-gray-900 transition">
                                Login
                            </a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Get Started
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold mb-4">RBAC System</h3>
                <p class="text-gray-400 mb-6">Powerful Role-Based Access Control System</p>
                <div class="flex justify-center gap-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Contact</a>
                </div>
                <div class="mt-6 text-gray-400">
                    © {{ date('Y') }} RBAC System. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        lucide.createIcons();
    </script>

    @livewireScripts
</body>
</html>