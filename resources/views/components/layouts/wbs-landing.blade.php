<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Whistleblowing System - PT DONGGI-SENORO LNG' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <style>
        :root {
            --color-dslng-blue: #0033A0;
            --color-dslng-light-blue: #0072CE;
            --color-dslng-red: #E4002B;
            --color-dslng-gray: #53565A;
        }

        .hero-bg {
            background: linear-gradient(135deg, var(--color-dslng-blue), var(--color-dslng-light-blue));
        }

        .news-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .required-info-item {
            border-left: 4px solid var(--color-dslng-red);
            transition: all 0.3s ease;
        }

        .required-info-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        /* Sticky Header */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sticky-header.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        /* Desktop Navigation */
        .nav-link {
            position: relative;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--color-dslng-blue);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Language Dropdown */
        .language-dropdown {
            position: relative;
        }

       

        .dropdown-menu { 
            transform: translateY(-10px);
            transition: all 0.3s ease;
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 50;
        }

        /* Mobile Menu */
        @media (max-width: 767px) {
            .sticky-header {
                z-index: 1001;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <header id="mainHeader" class="sticky-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                {{-- Logo --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        DSLNG
                    </div>
                    <div class="border-l border-gray-300 h-8"></div>
                    <div>
                        <div class="font-bold text-gray-900 text-lg tracking-tight">DONGGI SENORO</div>
                        <div class="text-gray-600 text-sm">Liquefied Natural Gas</div>
                    </div>
                </div>
 
                {{-- Desktop Navigation --}}
<nav class="hidden md:flex items-center gap-8">
    <a href="#" class="nav-link text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
        {{ __('wbs.header.home') }}
    </a>
    <a href="#" class="nav-link text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
        {{ __('wbs.header.register') }}
    </a>
    <a href="#" class="nav-link text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
        {{ __('wbs.header.login') }}
    </a>
    
    {{-- Language Dropdown dengan Alpine --}}
    <div x-data="{ open: false }" class="language-dropdown relative">
        <button @click="open = !open" 
                class="nav-link flex items-center gap-2 text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
            {{ __('wbs.header.language') }}
            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </button>
        
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             @click.away="open = false"
             class="dropdown-menu mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
            <button wire:click="changeLanguage('en')" 
                    @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2 {{ $currentLocale === 'en' ? 'bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-language text-gray-400"></i>
                English
                @if($currentLocale === 'en')
                    <i class="fas fa-check ml-auto text-blue-600"></i>
                @endif
            </button>
            <button wire:click="changeLanguage('id')" 
                    @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2 {{ $currentLocale === 'id' ? 'bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-language text-gray-400"></i>
                Bahasa Indonesia
                @if($currentLocale === 'id')
                    <i class="fas fa-check ml-auto text-blue-600"></i>
                @endif
            </button>
        </div>
    </div>
</nav>

                {{-- Mobile Menu Button --}}
                <button id="mobileMenuButton" class="md:hidden text-gray-700 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        {{-- Mobile Menu --}}
<div id="mobileMenu" class="md:hidden bg-white border-t border-gray-200 px-4 py-4 hidden">
    <div class="space-y-3">
        <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
            {{ __('wbs.header.home') }}
        </a>
        <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
            {{ __('wbs.header.register') }}
        </a>
        <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
            {{ __('wbs.header.login') }}
        </a>
        <div x-data="{ open: false }" class="pt-2">
            <button @click="open = !open" 
                    class="w-full text-left flex items-center justify-between text-gray-700 font-medium py-2">
                <span>{{ __('wbs.header.language') }}</span>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" 
                 x-transition
                 class="pl-4 space-y-2 mt-2">
                <button wire:click="changeLanguage('en')" 
                        @click="open = false; document.getElementById('mobileMenu').classList.add('hidden'); document.getElementById('mobileMenuButton').innerHTML = '<i class=\"fas fa-bars text-xl\"></i>';"
                        class="w-full text-left block text-gray-600 hover:text-blue-600 text-sm py-1 flex items-center gap-2 {{ $currentLocale === 'en' ? 'text-blue-600 font-semibold' : '' }}">
                    English
                    @if($currentLocale === 'en')
                        <i class="fas fa-check ml-auto"></i>
                    @endif
                </button>
                <button wire:click="changeLanguage('id')" 
                        @click="open = false; document.getElementById('mobileMenu').classList.add('hidden'); document.getElementById('mobileMenuButton').innerHTML = '<i class=\"fas fa-bars text-xl\"></i>';"
                        class="w-full text-left block text-gray-600 hover:text-blue-600 text-sm py-1 flex items-center gap-2 {{ $currentLocale === 'id' ? 'text-blue-600 font-semibold' : '' }}">
                    Bahasa Indonesia
                    @if($currentLocale === 'id')
                        <i class="fas fa-check ml-auto"></i>
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>
    </header>

    {{ $slot }}

    @livewireScripts

    <script>
        // Mobile Menu Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.getElementById('mainHeader');
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');

            // Sticky header on scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Mobile menu toggle
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = mobileMenu.classList.contains('hidden');
                    
                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        this.innerHTML = '<i class="fas fa-times text-xl"></i>';
                        this.classList.add('text-blue-600');
                    } else {
                        mobileMenu.classList.add('hidden');
                        this.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                        this.classList.remove('text-blue-600');
                    }
                });

                // Close mobile menu when clicking on links
                mobileMenu.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                        mobileMenuButton.classList.remove('text-blue-600');
                    }
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!header.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                        if (mobileMenuButton) {
                            mobileMenuButton.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                            mobileMenuButton.classList.remove('text-blue-600');
                        }
                    }
                });
            }

            // Initialize header state
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            }
        });
    </script>
</body>
</html>