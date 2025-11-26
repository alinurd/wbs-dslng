<div class="log">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gradient-to-r from-blue-800 to-blue-600 py-8 px-4 shadow-lg rounded-lg">
                <h2 class="text-center text-3xl font-bold text-white" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                    {{ __('auth.login.title') }}
                </h2>
                <p class="mt-2 text-center text-sm text-blue-100">
                    {{ __('auth.login.dsc') }}
                </p>
            </div>
        </div>

        <!-- Form Container -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <ul class="text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit="login">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 required">
                            {{ __('auth.username') }}
                        </label>
                        <div class="mt-1">
                            <input wire:model="email" id="email" name="email" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 required">
                            {{ __('auth.password') }}
                        </label>
                        <div class="mt-1">
                            <input wire:model="password" id="password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mt-4 mb-4">
                        <div class="flex items-center">
                            <input wire:model="remember" id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-900">
                                {{ __('auth.login.remember_me') }}
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('password.request') }}"
                                class="font-medium text-blue-600 hover:text-blue-500">
                                {{ __('auth.login.forgot_password') }}
                            </a>
                        </div>
                    </div>

                    <!-- Innovative reCAPTCHA -->
                    <!-- Innovative reCAPTCHA -->
                    <div class="mt-4 mb-4">
                        @error('captcha')
                            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-md">
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            </div>
                        @enderror

                        <livewire:innovative-captcha :patternLength="4" :gridSize="9" />
                    </div>

                   
                    <!-- Submit Button -->
                    <div class="mb-4">
                       <button type="submit" wire:loading.attr="disabled"
        class="verif-btn w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition">
    <span wire:loading.remove wire:target="login">
        {{ __('auth.login.submit') }}
    </span>
    <span wire:loading wire:target="login">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        Memproses...
    </span>
</button>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            {{ __('auth.login.no_account') }}
                            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                {{ __('auth.login.register_here') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .btn-enabled {
            background-color: #2563eb !important;
            cursor: pointer !important;
        }

        .btn-enabled:hover {
            background-color: #1d4ed8 !important;
        }

        .btn-disabled {
            background-color: #9ca3af !important;
            cursor: not-allowed !important;
        }
    </style>
</div>
