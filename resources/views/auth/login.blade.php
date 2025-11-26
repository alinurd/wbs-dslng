<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4">
                <div class="font-medium text-red-600">
                    {{ __('auth.validation_error') }}
                </div>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Status Message --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="login">
            <div>
                <x-label for="email" value="{{ __('auth.email') }}" />
                <x-input 
                    wire:model="email" 
                    id="email" 
                    class="block mt-1 w-full" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="email"
                    placeholder="{{ __('auth.enter_email') }}"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('auth.password') }}" />
                <x-input 
                    wire:model="password" 
                    id="password" 
                    class="block mt-1 w-full" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="{{ __('auth.enter_password') }}"
                />
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox 
                        wire:model="remember" 
                        id="remember_me" 
                        name="remember" 
                    />
                    <span class="ms-2 text-sm text-gray-600">{{ __('auth.remember_me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                       href="{{ route('password.request') }}">
                        {{ __('auth.forgot_password') }}
                    </a>
                @endif

                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="ms-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                >
                    <span wire:loading.remove wire:target="login">
                        {{ __('auth.login') }}
                    </span>
                    <span wire:loading wire:target="login">
                        {{ __('auth.logging_in') }}
                    </span>
                </button>
            </div>

            {{-- Language Switcher --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ __('auth.language') }}:</span>
                    <div class="flex space-x-2">
                        <button 
                            type="button"
                            wire:click="changeLanguage('en')"
                            class="px-3 py-1 text-xs rounded {{ $locale === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}"
                        >
                            EN
                        </button>
                        <button 
                            type="button"
                            wire:click="changeLanguage('id')"
                            class="px-3 py-1 text-xs rounded {{ $locale === 'id' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}"
                        >
                            ID
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>