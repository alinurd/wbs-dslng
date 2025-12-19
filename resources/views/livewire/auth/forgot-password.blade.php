<div class="log">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gradient-to-r from-blue-800 to-blue-600 py-8 px-4 shadow-lg rounded-lg">
                <h2 class="text-center text-3xl font-bold text-white" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                    {{ __('auth.forgot.title') }}
                </h2>
              
            </div>
        </div>

        <!-- Form Container -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form wire:submit="forgot">
                    <!-- Username -->
                    <div>
                        {{ __('auth.forgot.dsc') }}
                        <div class="mt-1">
                            <input wire:model="email" id="email" name="email" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Innovative reCAPTCHA -->
                    <div class="mt-4 mb-4">
                        @error('captcha')
                            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-md">
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            </div>
                        @enderror

                        <livewire:innovative-captcha />
                    </div>


                    <!-- Submit Button -->

        <!-- Submit Button -->
@if ($captchaVerified)
    <div class="mb-4">
        <button type="submit"
            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition">

            <span wire:loading.remove wire:target="forgot">
                {{ __('Submit') }}
            </span>

            <span wire:loading wire:target="forgot">
               <i class="fas fa-spinner fa-spin"></i>
            </span>
        </button>
    </div>
@endif


                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                {{ __('auth.login.register_here') }}
                            </a>  | 
                            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                {{ __('auth.register.login_here') }}
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
