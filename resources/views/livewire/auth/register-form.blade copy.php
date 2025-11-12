<div>
  <div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ __('auth.register.title') }}
            </h1>
            <p class="text-sm text-gray-600">
                {{ __('auth.register.note') }}
            </p>
        </div>

        <!-- Form Container -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <form wire:submit.prevent="register" class="space-y-4">
                <!-- Statement Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('auth.register.statement') }}
                    </h3>
                    
                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1 required">
                            {{ __('auth.register.password') }}
                        </label>
                        <input wire:model="password" id="password" name="password" type="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                            @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ __($message, ['min' => 8]) }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1 required">
                            {{ __('auth.register.password_confirmation') }}
                        </label>
                        <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 required">
                            {{ __('auth.register.email') }}
                        </label>
                        <input wire:model="email" id="email" name="email" type="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                            @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ __($message) }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('auth.register.contact_details') }}
                    </h3>
                    
                    <!-- Security Question & Answer -->
                    <div class="mb-4">
                        <label for="security_question" class="block text-sm font-medium text-gray-700 mb-1 required">
                            {{ __('auth.register.security_question') }}
                        </label>
                        <select wire:model="security_question" id="security_question" name="security_question" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 mb-2
                            @error('security_question') border-red-500 @enderror">
                            <option value="">{{ __('auth.register.choose_question') }}</option>
                            @foreach (__('auth.register.security_questions') as $key => $question)
                                <option value="{{ $key }}">{{ $question }}</option>
                            @endforeach
                        </select>
                        
                        <label for="answer" class="block text-sm font-medium text-gray-700 mb-1 required">
                            {{ __('auth.register.answer') }}
                        </label>
                        <input wire:model="answer" id="answer" name="answer" type="text" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                            @error('answer') border-red-500 @enderror">
                        @error('answer')
                            <p class="mt-1 text-sm text-red-600">{{ __($message, ['min' => 2]) }}</p>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div class="mb-4">
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('auth.register.full_name') }}
                        </label>
                        <input wire:model="full_name" id="full_name" name="full_name" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- ID Number -->
                    <div class="mb-4">
                        <label for="id_number" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('auth.register.id_number') }}
                        </label>
                        <input wire:model="id_number" id="id_number" name="id_number" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('auth.register.phone') }}
                        </label>
                        <input wire:model="phone" id="phone" name="phone" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Type of Reporter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 required">
                        {{ __('auth.register.type_of_reporter') }}
                    </label>
                    <div class="flex space-x-6">
                        <div class="flex items-center">
                            <input wire:model="reporter_type" id="employee" name="reporter_type" type="radio" value="employee" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label for="employee" class="ml-2 block text-sm text-gray-700">
                                {{ __('auth.register.employee') }}
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input wire:model="reporter_type" id="non_employee" name="reporter_type" type="radio" value="non_employee"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label for="non_employee" class="ml-2 block text-sm text-gray-700">
                                {{ __('auth.register.non_employee') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Verification Code -->
                <div class="mb-6">
                    <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-1 required">
                        {{ __('auth.register.verification_code') }}
                    </label>
                    <input wire:model="verification_code" id="verification_code" name="verification_code" type="text" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                        @error('verification_code') border-red-500 @enderror">
                    @error('verification_code')
                        <p class="mt-1 text-sm text-red-600">{{ __($message) }}</p>
                    @enderror
                </div>

                <!-- Horizontal Line -->
                <div class="border-t border-gray-300 my-6"></div>

                <!-- Confirmation Checkbox -->
                <div class="mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5 mt-0.5">
                            <input wire:model="confirmation" id="confirmation" name="confirmation" type="checkbox" required
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded
                                @error('confirmation') border-red-500 @enderror">
                        </div>
                        <label for="confirmation" class="ml-3 block text-sm text-gray-700">
                            {{ __('auth.register.confirmation_text') }}
                        </label>
                    </div>
                    @error('confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ __($message) }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('auth.register.submit') }}
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600">
                        {{ __('auth.register.already_registered') }}
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
        color: #ef4444;
    }
</style>
</div>