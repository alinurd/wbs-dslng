<div class="simple-captcha my-4" id="{{ $componentId }}">
    <div class="bg-white border rounded-lg p-4">

        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span class="text-sm font-medium">{{ __('captcha.header') }}</span>
            </div>
        </div>

        <!-- Error -->
        @if ($errorMessage)
            <div class="mb-3 p-2 bg-red-50 text-red-600 text-sm rounded">
                {{ __('captcha.error') }}
            </div>
        @endif

        <!-- Sudah Terverifikasi -->
        @if ($captchaVerified)
            <div class="text-center py-3">
                <div class="inline-flex items-center gap-2 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-medium">{{ __('captcha.verified') }}</span>
                </div>
            </div>
        @else

            <!-- CAPTCHA + Input -->
            <div class="flex items-center gap-3 mt-2">

                <!-- CAPTCHA BOX -->
                <div class="relative bg-white border rounded-md w-32 h-12 flex items-center justify-center select-none overflow-hidden">
                    <!-- Garis silang -->
                    <div class="absolute inset-0">
                        <div class="absolute w-full h-0.5 bg-gray-300 rotate-12 top-1/2" style="z-index: 999"></div>
                        <div class="absolute w-full h-0.5 bg-gray-300 -rotate-10 top-1/2" style="z-index: 999"></div>
                        <div class="absolute w-full h-0.5 bg-gray-300 -rotate-12 top-1/3"></div>
                        <div class="absolute w-full h-0.5 bg-gray-300 -rotate-9 top-1/4"></div>
                    </div>

                    <!-- Noise titik -->
                    <div class="absolute inset-0">
                        @for($i = 0; $i < 50; $i++)
                            <span class="absolute text-blue-500 opacity-70"
                                style="font-size:10px; top:{{ rand(10, 95) }}%; left:{{ rand(15, 95) }}%;">
                                •
                            </span>
                        @endfor
                    </div>

                    <!-- Angka -->
                    <span class="text-3xl font-extrabold text-gray-800 z-10"
      style="
          font-family: 'Courier New', monospace;
          letter-spacing: 5px;
       ">
    {{ $captchaText }}
</span>

                </div>

                <!-- INPUT + REFRESH -->
                <div class="flex items-center border rounded-md overflow-hidden h-12">

                    <input type="text"
                        wire:model.live="userInput"
                        wire:keydown.enter="verifyCaptcha"
                        class="px-3 w-40 text-center outline-none text-base"
                        placeholder="{{ __('captcha.placeholder') }}"
                        maxlength="6">

                    <button type="button"
                        wire:click="generateCaptcha"
                        class="px-3 h-full bg-gray-50 hover:bg-gray-100 border-l flex items-center justify-center">

                        <!-- Normal Refresh Icon -->
                        <i class="fa fa-refresh text-gray-700" wire:loading.remove wire:target="generateCaptcha"></i>

                        <!-- Loading Spinner -->
                        <span wire:loading wire:target="generateCaptcha">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Tombol Verifikasi -->
            <button type="button"
                wire:click="verifyCaptcha"
                class="w-full py-2 mt-4 bg-blue-600 text-white rounded-md text-center hover:bg-blue-700 relative font-medium">

                <span wire:loading.remove wire:target="verifyCaptcha">{{ __('captcha.verify') }}</span>

                <span wire:loading wire:target="verifyCaptcha" class="inline-flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    {{ __('captcha.processing') }}
                </span>
            </button>

        @endif
    </div>
</div>
