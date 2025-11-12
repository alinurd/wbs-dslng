<header id="mainHeader" class="sticky-header bg-white shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            {{-- Logo --}}
            <div class="flex items-center gap-4">
                 <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width: 80%">                  
            </div>

            {{-- Desktop Navigation --}}
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

                {{-- Language Dropdown (Desktop) --}}
                <div x-data="{ open: false }" class="language-dropdown relative">
                    <button @click="open = !open"
                        class="nav-link flex items-center gap-2 text-gray-700 hover:text-blue-600 font-medium text-sm uppercase tracking-wide">
                        {{ __('wbs.header.language') }}
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        @click.away="open = false"
                        class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">

                        {{-- Indonesia --}}
                        <button onclick="changeLanguage('id')"
                            @click="open = false"
                            class="w-full text-left px-4 py-2 flex items-center gap-2 text-sm rounded-md
                                {{ $currentLocale === 'id' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                            🇮🇩 Indonesia
                            @if ($currentLocale === 'id')
                                <i class="fas fa-check ml-auto text-blue-600"></i>
                            @endif
                        </button>

                        {{-- English --}}
                        <button onclick="changeLanguage('en')"
                            @click="open = false"
                            class="w-full text-left px-4 py-2 flex items-center gap-2 text-sm rounded-md
                                {{ $currentLocale === 'en' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                            🇺🇸 English
                            @if ($currentLocale === 'en')
                                <i class="fas fa-check ml-auto text-blue-600"></i>
                            @endif
                        </button>
                        

                       
        </button>

                    </div>
                </div>
            </nav>

            {{-- Mobile Menu Button --}}
            <button id="mobileMenuButton" class="md:hidden text-gray-700 hover:text-blue-600 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="md:hidden bg-white border-t border-gray-200 px-4 py-4 hidden">
        <div class="space-y-3">
            <a href="{{ route('landing.index') }}"
                class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
                {{ __('wbs.header.home') }}
            </a>
            <a href="{{ route('register') }}"
                class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
                {{ __('wbs.header.register') }}
            </a>
            <a href="{{ route('login') }}"
                class="block text-gray-700 hover:text-blue-600 font-medium py-2 border-b border-gray-100">
                {{ __('wbs.header.login') }}
            </a>

            {{-- Language Dropdown (Mobile) --}}
            <div x-data="{ open: false }" class="pt-2">
                <button @click="open = !open"
                    class="w-full text-left flex items-center justify-between text-gray-700 font-medium py-2">
                    <span>{{ __('wbs.header.language') }}</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                        :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" x-transition class="pl-4 space-y-2 mt-2">
                    {{-- English --}}
                    <button onclick="changeLanguage('en')"
                        @click="open = false; document.getElementById('mobileMenu').classList.add('hidden'); document.getElementById('mobileMenuButton').innerHTML = '<i class=&quot;fas fa-bars text-xl&quot;></i>';"
                        class="w-full text-left block text-sm py-2 flex items-center gap-2 rounded-lg
                            {{ $currentLocale === 'en' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                        🇺🇸 English
                        @if ($currentLocale === 'en')
                            <i class="fas fa-check ml-auto text-blue-600"></i>
                        @endif
                    </button>

                    {{-- Indonesia --}}
                    <button onclick="changeLanguage('id')"
                        @click="open = false; document.getElementById('mobileMenu').classList.add('hidden'); document.getElementById('mobileMenuButton').innerHTML = '<i class=&quot;fas fa-bars text-xl&quot;></i>';"
                        class="w-full text-left block text-sm py-2 flex items-center gap-2 rounded-lg
                            {{ $currentLocale === 'id' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                        🇮🇩 Bahasa Indonesia
                        @if ($currentLocale === 'id')
                            <i class="fas fa-check ml-auto text-blue-600"></i>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
