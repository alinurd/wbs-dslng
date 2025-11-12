<div>
    <section class="new-bg-page text-white py-20">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                {{ __('auth.register.title') }}
            </h1>
            <p class="text-gray-50 m-3 text-lg max-w-2xl mx-auto">
                {{ __('auth.register.dsc') }}
            </p>
        </div>
    </section>
    <div>
        <div class="min-h-screen bg-gray-50 py-8 px-4">
            <div class="max-w-7xl mx-auto">
                <!-- Form Container -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="mb-8 p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                        <p class="text-sm text-red-800 font-medium italic">
                            {{ __('auth.register.note') }}
                        </p>
                    </div>
                    <form wire:submit.prevent="register" class="space-y-6">

                        <div class="grid  grid-cols-1 lg:grid-cols-2">
                            <div class="grid grid-cols-1 p-4">
                                <!-- Username -->
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.username') }}
                                    </label>
                                    <input wire:model="username" id="username" name="username" type="text" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror">
                                    @error('username')
                                        <p class="mt-1 text-sm text-red-600">{{ __($message, ['min' => 3]) }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.password') }}
                                    </label>
                                    <input wire:model="password" id="password" name="password" type="password" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                            @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ __($message, ['min' => 8]) }}</p>
                                    @enderror
                                </div>

                                <!-- Password Confirmation -->
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.password_confirmation') }}
                                    </label>
                                    <input wire:model="password_confirmation" id="password_confirmation"
                                        name="password_confirmation" type="password" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.email') }}
                                    </label>
                                    <input wire:model="email" id="email" name="email" type="email" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                            @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ __($message) }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="detail" class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.detail') }}
                                    </label>
                                    <textarea wire:model="detail" id="detail" name="detail" type="detail" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                            @error('detail') border-red-500 @enderror">
</textarea>
                                    @error('detail')
                                        <p class="mt-1 text-sm text-red-600">{{ __($message) }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 p-4">

                                <!-- Security Question -->
                                <div class="mb-4">
                                    <label for="security_question"
                                        class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.security_question') }}
                                    </label>
                                    <select wire:model="security_question" id="security_question"
                                        name="security_question" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 mb-3
                                @error('security_question') border-red-500 @enderror">
                                        <option value="">{{ __('auth.register.choose_question') }}</option>
                                        @foreach (__('auth.register.security_questions') as $key => $question)
                                            <option value="{{ $key }}">{{ $question }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Answer -->
                                <div class="mb-4">
                                    <label for="answer" class="block text-sm font-medium text-gray-700 mb-2 required">
                                        {{ __('auth.register.answer') }}
                                    </label>
                                    <input wire:model="answer" id="answer" name="answer" type="text" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                                @error('answer') border-red-500 @enderror">
                                    @error('answer')
                                        <p class="mt-1 text-sm text-red-600">{{ __($message, ['min' => 2]) }}</p>
                                    @enderror
                                </div>

                                <!-- Full Name -->
                                <div class="mb-4">
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('auth.register.full_name') }}
                                    </label>
                                    <input wire:model="full_name" id="full_name" name="full_name" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- ID Number -->
                                <div class="mb-4">
                                    <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('auth.register.id_number') }}
                                    </label>
                                    <input wire:model="id_number" id="id_number" name="id_number" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-4">
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('auth.register.phone') }}
                                    </label>
                                    <input wire:model="phone" id="phone" name="phone" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                            </div>

                            <div>
                                <div class="flex items-start space-x-4">
                                    <!-- Gambar di samping kiri -->
                                    <div class="flex-shrink-0 mt-7">
                                        <img src="{{ asset('assets/images/key.jpg') }}" alt="Logo"
                                            class="max-w-[90%] h-auto">
                                    </div>

                                    <!-- Input verification code -->
                                    <div class="flex-1 pr-5">
                                        <label for="verification_code"
                                            class="block text-sm font-medium text-gray-700 mb-2 required">
                                            Verification Code(*)
                                        </label>
                                        <input wire:model="verification_code" id="verification_code"
                                            name="verification_code" type="text" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                                            @error('verification_code') border-red-500 @enderror">
                                        @error('verification_code')
                                            <p class="mt-1 text-sm text-red-600">{{ __($message) }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div> 
                                <div class="pl-5">
                                <label class="block text-sm font-medium text-gray-700 mb-3 required">
                                    {{ __('auth.register.type_of_reporter') }}
                                </label>
                                <div class="flex space-x-6">
                                    <div class="flex items-center">
                                        <input wire:model="reporter_type" id="employee" name="reporter_type"
                                            type="radio" value="employee"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="employee" class="ml-2 block text-sm text-gray-700">
                                            {{ __('auth.register.employee') }}
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input wire:model="reporter_type" id="non_employee" name="reporter_type"
                                            type="radio" value="non_employee"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            {{ __('auth.register.non_employee') }}
                                        </label>
                                    </div>
                                </div>
                            </div> 

                        </div>
                        <div class="border-t border-gray-200 pt-8">
                            <div class="flex items-start space-x-4 bg-blue-50 p-6 rounded-2xl">
                                <div class="flex items-center h-5 mt-1">
                                    <input wire:model="confirmation" id="confirmation" name="confirmation"
                                        type="checkbox" required
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded
                                        @error('confirmation') border-red-500 ring-1 ring-red-500 @enderror">
                                </div>
                                <div class="flex-1">
                                    <label for="confirmation"
                                        class="block text-sm font-medium text-gray-800 leading-6">
                                        {{ __('auth.register.confirmation_text') }}
                                    </label>
                                    @error('confirmation')
                                        <p class="mt-2 text-sm text-red-600">{{ __($message) }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[rgb(0,95,160)] hover:bg-[rgb(0,110,160)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('auth.register.submit') }}
                        </button>


                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                {{ __('auth.register.already_registered') }}
                                <a href="{{ route('login.form') }}"
                                    class="font-medium text-blue-600 hover:text-blue-500">
                                    {{ __('auth.register.login_here') }}
                                </a>
                            </p>
                        </div>
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
</div>
