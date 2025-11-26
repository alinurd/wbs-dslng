<div>
    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}!</h1>
    <p class="text-blue-100 text-lg mb-4">
        Sistem pelaporan yang aman, rahasia, dan terpercaya untuk menciptakan budaya kerja transparan
        dan berintegritas
    </p>
    
    @if (!$isVerif)
        <!-- Alert Verifikasi -->
        <div class="bg-red-500/20 border border-red-300 rounded-lg p-6 mb-6">
            <div class="flex flex-col items-center justify-center space-y-3 text-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle text-red-300 text-xl"></i>
                    <span class="text-red-100 font-medium text-lg">Silahkan lakukan verifikasi email terlebih dahulu!</span>
                </div>
                <p class="text-red-100 text-sm">Kami telah mengirim kode verifikasi ke email Anda: <strong>{{ $user->email }}</strong></p>
            </div>
        </div>

        <!-- Form Verifikasi Email -->
        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 mb-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Verifikasi Email</h2>
                <p class="text-blue-100">Masukkan kode verifikasi yang telah dikirim ke email Anda</p>
            </div>

            <!-- Session Messages -->
            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-500/20 border border-green-300 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-green-300"></i>
                        <span class="text-green-100">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-500/20 border border-red-300 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-circle text-red-300"></i>
                        <span class="text-red-100">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form wire:submit="verifyEmail">
                <!-- Kode Verifikasi -->
                <div class="mb-6">
                    <div class="flex justify-center">
                        <input 
                            type="text" 
                            id="verification_code"
                            wire:model="verification_code"
                            maxlength="8"
                            class="w-full max-w-xs text-center px-4 py-3 bg-white/5 border {{ $errors->has('verification_code') ? 'border-red-400' : 'border-white/20' }} rounded-lg text-white text-lg font-mono tracking-widest focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 transition-all duration-200"
                            placeholder="Masukkan 8 digit kode"
                            required
                            autofocus
                        >
                    </div>
                    @error('verification_code')
                        <p class="text-red-300 text-sm mt-2 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
    <!-- Tombol Verifikasi -->
    <button 
        type="submit"
        class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center space-x-2"
        wire:loading.attr="disabled"
        wire:target="verifyEmail"
    >
        <i class="fas fa-check-circle"></i>
        <span wire:loading.remove wire:target="verifyEmail">
            Verifikasi Email
        </span>
        <span wire:loading wire:target="verifyEmail">
            <i class="fas fa-spinner fa-spin"></i>
            Memverifikasi...
        </span>
    </button>

    <!-- Tombol Kirim Ulang Kode -->
    <button 
        type="button"
        wire:click="resendVerificationCode"
        class="w-full sm:w-auto px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-medium rounded-lg transition-all duration-200 flex items-center justify-center space-x-2"
        wire:loading.attr="disabled"
        wire:target="resendVerificationCode"
        @if(!$canResend) disabled @endif
    >
        
        <span wire:loading.remove wire:target="resendVerificationCode">
            @if($canResend)
               <i class="fas fa-redo-alt 
            @if(!$canResend) opacity-50 
            @else wire:loading.remove wire:target="resendVerificationCode" 
            @endif">
        </i>  Kirim Ulang Kode
            @else
               <i class="fas fa-spinner fa-spin"></i>  Tunggu {{ $countdown }} detik
            @endif
        </span>
        
        <span wire:loading wire:target="resendVerificationCode">
            <i class="fas fa-spinner fa-spin"></i>
            Mengirim Kode...
        </span>
    </button>
</div>
            </form>

            <!-- Auto Countdown dengan wire:poll -->
            @if(!$canResend && $countdown > 0)
                <div wire:poll.1000ms="decreaseCountdown"></div>
            @endif

            <!-- Info Tambahan -->
            <div class="mt-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-yellow-300 mt-1"></i>
                    <div class="text-yellow-100 text-sm">
                        <p class="font-semibold">Perhatian:</p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                            {{-- <li>Kode verifikasi berlaku selama 30 menit</li> --}}
                            <li>Periksa folder spam jika email tidak ditemukan</li>
                            <li>Kode terdiri dari 8 karakter acak (huruf dan angka)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Tampilan Setelah Verifikasi Berhasil -->
        <div class="bg-green-500/20 border border-green-300 rounded-lg p-6">
            <div class="flex flex-col items-center justify-center space-y-3 text-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-green-300 text-xl"></i>
                    <span class="text-green-100 font-medium text-lg">Email Anda telah terverifikasi!</span>
                </div>
                <p class="text-green-100">Sekarang Anda dapat menggunakan semua fitur sistem dengan lengkap.</p>
            </div>
        </div>
    @endif
</div>