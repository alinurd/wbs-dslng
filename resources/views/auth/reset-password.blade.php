<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            {{-- <x-authentication-card-logo /> --}}
        </x-slot>

         <div class="sm:mx-auto sm:w-full sm:max-w-md mb-4">
            <div class="bg-gradient-to-r from-blue-800 to-blue-600 py-8 px-4 shadow-lg rounded-lg">
                <h2 class="text-center text-3xl font-bold text-white" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                    {{ __('auth.reset.title') }}
                </h2>
              
            </div>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" value="{{ __('auth.reset.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4 relative">
                <x-label for="password" value="{{ __('auth.reset.password') }}" />
                 <div class="relative">
                <x-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                        <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                    </button>
            </div>
            </div>
            
            <div class="mt-4 relative">
                <x-label for="password_confirmation" value="{{ __('auth.reset.confirm_password') }}" />
                 <div class="relative">
                <x-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" required autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                    </button>
            </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit"
            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition">

            <span wire:loading.remove wire:target="forgot">
                                    {{ __('auth.reset.submit') }}

            </span>

            <span wire:loading wire:target="forgot">
               <i class="fas fa-spinner fa-spin"></i>
            </span>
        </button>
 
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye text-gray-400 hover:text-gray-600';
        }
    }
</script>