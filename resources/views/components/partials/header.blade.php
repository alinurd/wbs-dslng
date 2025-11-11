<header id="mainHeader" class="sticky-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                {{-- Logo --}}
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        DSLNG
                    </div>
                    <div class="border-l border-gray-300 h-8"></div>
                    <div>
                        <div class="font-bold text-gray-900 text-lg tracking-tight">DONGGI SENORO</div>
                        <div class="text-gray-600 text-sm">Liquefied Natural Gas</div>
                    </div>
                </div>

                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('landing.index') }}"
                        class="nav-link text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
                        {{ __('wbs.header.home') }}
                    </a>
                    <a href="{{ route('register') }}"
                        class="nav-link text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
                        {{ __('wbs.header.register') }}
                    </a>
                    <a href="{{ route('login') }}"
                        class="nav-link text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
                        {{ __('wbs.header.login') }}
                    </a>
                    <div x-data="{ open: false }" class="language-dropdown relative">
                        <button @click="open = !open"
                            class="nav-link flex items-center gap-2 text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
                            {{ __('wbs.header.language') }}
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" @click.away="open = false"
                            class="dropdown-menu mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <button wire:click="changeLanguage('id')" class="btn btn-sm btn-primary">
                                🇮🇩 Indonesia
                            </button>
                            <button wire:click="changeLanguage('en')" class="btn btn-sm btn-secondary">
                                🇺🇸 English
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
        <div id="mobileMenu" class="md:hidden bg-white border-t border-gray-200 px-4 py-4 hidden">
            <div class="space-y-3">
                <a href="#"
                    class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
                    {{ __('wbs.header.home') }}
                </a>
                <a href="#"
                    class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
                    {{ __('wbs.header.register') }}
                </a>
                <a href="#"
                    class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
                    {{ __('wbs.header.login') }}
                </a>
                <div x-data="{ open: false }" class="pt-2">
                    <button @click="open = !open"
                        class="w-full text-left flex items-center justify-between text-gray-700 font-medium py-2">
                        <span>{{ __('wbs.header.language') }}</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="pl-4 space-y-2 mt-2">
                        <button wire:click="changeLanguage('en')"
                            @click="open = false; document.getElementById('mobileMenu').classList.add('hidden'); document.getElementById('mobileMenuButton').innerHTML = '<i class=\"fas fa-bars text-xl\"></i>';"
                            class="w-full text-left block text-gray-600 hover:text-blue-600 text-sm py-1 flex items-center gap-2 {{ $currentLocale === 'en' ? 'text-blue-600 font-semibold' : '' }}">
                            English
                            @if ($currentLocale === 'en')
                                <i class="fas fa-check ml-auto"></i>
                            @endif
                        </button>
                        <button wire:click="changeLanguage('id')"
                            @click="open = false; document.getElementById('mobileMenu').classList.add('hidden'); document.getElementById('mobileMenuButton').innerHTML = '<i class=\"fas fa-bars text-xl\"></i>';"
                            class="w-full text-left block text-gray-600 hover:text-blue-600 text-sm py-1 flex items-center gap-2 {{ $currentLocale === 'id' ? 'text-blue-600 font-semibold' : '' }}">
                            Bahasa Indonesia
                            @if ($currentLocale === 'id')
                                <i class="fas fa-check ml-auto"></i>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>