<div class="innovative-captcha my-4" id="{{ $componentId }}">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    {{-- <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded flex items-center justify-center">
                        <span class="text-white text-xs font-bold"></span>
                    </div> --}}
                    <span class="text-sm font-medium text-gray-700">{{ __('captcha.security_verification') }}</span>
                </div>
                
                <button type="button" 
        wire:click="generateNewChallenge" 
        wire:loading.attr="disabled"
        class="flex items-center justify-center p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors duration-500 disabled:opacity-50 disabled:cursor-not-allowed"
        title="Refresh Security Token"
        id="refresh-btn-{{ $componentId }}">
    
    <!-- Icon saat tidak loading -->
    <div wire:loading.remove wire:target="generateNewChallenge">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
    </div>
    
    <!-- Icon saat loading -->
    <div wire:loading wire:target="generateNewChallenge">
        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
    </div>
    
    <!-- Label teks untuk kejelasan -->
    <span class="ml-2 text-sm font-medium">Refresh Token</span>
</button>
            </div>
        </div>

        <div class="p-4">
            @if($errorMessage)
                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-600">{{ $errorMessage }}</p>
                </div>
            @endif

            @if($captchaVerified)
                <div class="text-center py-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-green-600 font-medium">{{ __('captcha.verification_success') }}</p>
                </div>
            @else
                @if($isRefreshing)
                <div class="text-center py-8">
                    <div class="flex justify-center mb-4">
                        <svg class="w-8 h-8 animate-spin text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('captcha.loading_new_pattern') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('captcha.please_wait') }}</p>
                </div>
                @else
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-3">{{ __('captcha.remember_and_repeat') }}</p>
                    
                    <div class="pattern-grid grid grid-cols-3 gap-2 max-w-xs mx-auto mb-4"
                         id="pattern-grid-{{ $componentId }}"
                         wire:ignore>
                        @for($i = 0; $i < $patternGridSize; $i++)
                            <div class="pattern-cell w-12 h-12 border-2 border-gray-300 rounded-lg flex items-center justify-center text-sm font-medium bg-white cursor-pointer transition-colors hover:bg-gray-50"
                                 data-index="{{ $i }}">
                                {{ $i + 1 }}
                            </div>
                        @endfor
                    </div>
                    
                    <div class="sequence-indicators flex justify-center space-x-2 mb-3" id="sequence-{{ $componentId }}">
                        @if(isset($challengeData['pattern']))
                            @foreach($challengeData['pattern'] as $index => $step)
                                <div class="w-4 h-4 bg-gray-300 rounded-full sequence-dot flex items-center justify-center text-xs text-white" 
                                     data-index="{{ $index }}">
                                    {{ $index + 1 }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <div class="mt-2">
                        <p class="text-sm text-gray-600" id="pattern-status-{{ $componentId }}">
                            {!! __('captcha.select_step', ['current' => '<span class="font-bold text-blue-600" id="current-step-' . $componentId . '">1</span>', 'total' => $patternLength]) !!}
                        </p>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function initializePatternChallenge(componentId) {
    const patternGrid = document.getElementById(`pattern-grid-${componentId}`);
    const sequenceIndicators = document.getElementById(`sequence-${componentId}`);
    const patternStatus = document.getElementById(`pattern-status-${componentId}`);
    const currentStep = document.getElementById(`current-step-${componentId}`);
    
    if (!patternGrid) {
        // console.log('Pattern grid not found for:', componentId);
        return;
    }
    
    let userPattern = [];
    const expectedPattern = @json($challengeData['pattern'] ?? []);
    const totalSteps = expectedPattern.length;
    
    // console.log('Initializing pattern challenge:', { expectedPattern, totalSteps });

    function resetState() {
        userPattern = [];
        updateSequenceIndicators();
        updatePatternStatus();
        
        const cells = patternGrid.querySelectorAll('.pattern-cell');
        cells.forEach(cell => {
            cell.classList.remove('bg-blue-500', 'text-white', 'border-blue-500', 'bg-red-500', 'border-red-500', 'bg-green-500', 'border-green-500');
            cell.classList.add('bg-white', 'border-gray-300');
        });
        
        patternGrid.style.pointerEvents = 'auto';
    }
    
    function updateSequenceIndicators() {
        const dots = sequenceIndicators?.querySelectorAll('.sequence-dot');
        if (!dots) return;
        
        dots.forEach((dot, index) => {
            if (index < userPattern.length) {
                dot.classList.remove('bg-gray-300');
                dot.classList.add('bg-blue-500');
            } else {
                dot.classList.remove('bg-blue-500');
                dot.classList.add('bg-gray-300');
            }
        });
    }
    
    function updatePatternStatus() {
        if (!patternStatus || !currentStep) return;
        
        const currentStepNumber = userPattern.length + 1;
        currentStep.textContent = currentStepNumber;
        
        if (userPattern.length === totalSteps) {
            patternStatus.innerHTML = `<span class="text-green-600 font-bold">${getTranslation('captcha.pattern_complete')}</span>`;
        } else {
            patternStatus.innerHTML = getTranslation('captcha.select_step', {
                current: `<span class="font-bold text-blue-600">${currentStepNumber}</span>`,
                total: totalSteps
            });
        }
    }
    
    function enableUserInput() {
        // Remove existing listeners and add new one
        patternGrid.removeEventListener('click', handleCellClick);
        patternGrid.addEventListener('click', handleCellClick);
    }
    
    function handleCellClick(e) {
        const cell = e.target.closest('.pattern-cell');
        if (!cell) return;
        
        const index = parseInt(cell.dataset.index);
        
        // Biarkan user memilih angka yang sama (tidak ada pengecekan duplikat)
        userPattern.push(index);
        
        // Update tampilan cell yang diklik
        cell.classList.remove('bg-white', 'border-gray-300');
        cell.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
        
        updateSequenceIndicators();
        updatePatternStatus();
        
        // Jika user sudah memilih semua step
        if (userPattern.length === totalSteps) {
            patternGrid.removeEventListener('click', handleCellClick);
            
            setTimeout(() => {
                const userPatternArray = userPattern.map(Number);
                const isMatch = JSON.stringify(userPatternArray) === JSON.stringify(expectedPattern);
                
                // console.log('Pattern verification:', {
                //     userPattern: userPatternArray,
                //     expectedPattern: expectedPattern,
                //     isMatch: isMatch
                // });
                
                if (isMatch) {
                    showPatternSuccess();
                    enableLoginButtonDirectly();
                    
                    // Kirim ke Livewire untuk verifikasi
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('verify-pattern', { 
                            pattern: userPatternArray 
                        });
                    }
                } else {
                    showPatternError();
                    
                    // Kirim ke Livewire untuk trigger refresh
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('verify-pattern', { 
                            pattern: userPatternArray 
                        });
                    }
                }
            }, 500);
        }
    }
    
    function showPatternError() {
        const cells = patternGrid.querySelectorAll('.pattern-cell');
        cells.forEach(cell => {
            cell.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
            cell.classList.add('bg-red-500', 'text-white', 'border-red-500');
        });
        
        if (patternStatus) {
            patternStatus.innerHTML = `<span class="text-red-600 font-bold">${getTranslation('captcha.pattern_incorrect')}</span>`;
        }
        
        patternGrid.style.pointerEvents = 'none';
    }
    
    function showPatternSuccess() {
        const cells = patternGrid.querySelectorAll('.pattern-cell');
        cells.forEach(cell => {
            if (cell.classList.contains('bg-blue-500')) {
                cell.classList.remove('bg-blue-500', 'border-blue-500');
                cell.classList.add('bg-green-500', 'border-green-500', 'text-white');
            }
        });
        
        if (patternStatus) {
            patternStatus.innerHTML = `<span class="text-green-600 font-bold">${getTranslation('captcha.verification_success')}</span>`;
        }
    }
    
    function showPatternDemo(pattern, compId, callback) {
        const cells = document.querySelectorAll(`#pattern-grid-${compId} .pattern-cell`);
        
        // Reset semua cell terlebih dahulu
        cells.forEach(cell => {
            cell.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
            cell.classList.add('bg-white', 'border-gray-300');
        });
        
        let step = 0;
        
        function highlightNext() {
            if (step >= pattern.length) {
                // Demo selesai, reset cells dan panggil callback
                resetDemoCells(cells);
                setTimeout(callback, 300);
                return;
            }
            
            const cellIndex = pattern[step];
            if (cells[cellIndex]) {
                // Highlight cell
                cells[cellIndex].classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                
                // Kembalikan ke normal setelah delay
                setTimeout(() => {
                    cells[cellIndex].classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                    step++;
                    setTimeout(highlightNext, 300); // Delay sebelum step berikutnya
                }, 500);
            } else {
                step++;
                setTimeout(highlightNext, 300);
            }
        }
        
        // Mulai demo
        setTimeout(highlightNext, 500);
    }
    
    function resetDemoCells(cells) {
        cells.forEach(cell => {
            cell.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
            cell.classList.add('bg-white', 'border-gray-300');
        });
    }
    
    // Initialize
    resetState();
    showPatternDemo(expectedPattern, componentId, () => {
        enableUserInput();
    });
}

// Translation helper function
function getTranslation(key, replacements = {}) {
    const translations = {
        'captcha.select_step': `Pilih ${replacements.current} dari ${replacements.total} angka`,
        'captcha.pattern_complete': 'Pola lengkap! Memverifikasi...',
        'captcha.verification_success': 'Verifikasi Berhasil!',
        'captcha.pattern_incorrect': 'Pola tidak sesuai! Memuat pola baru...',
    };
    
    return translations[key] || key;
}

function enableLoginButtonDirectly() {
    const loginBtns = document.querySelectorAll('.verif-btn');
    if (loginBtns.length > 0) {
        loginBtns.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
        });
        window.captchaVerified = true;
        // console.log('Login button enabled');
    }
}

function disableLoginButton() {
    const loginBtns = document.querySelectorAll('.verif-btn');
    if (loginBtns.length > 0) {
        loginBtns.forEach(btn => {
            btn.disabled = true;
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            btn.classList.add('bg-gray-400', 'cursor-not-allowed');
        });
        window.captchaVerified = false;
        // console.log('Login button disabled');
    }
}

function initializeCurrentChallenge() {
    const componentId = '{{ $componentId }}';
    const isRefreshing = @json($isRefreshing ?? false);
    
    if (isRefreshing) {
        // Jika sedang refreshing, tunggu sebentar lalu coba lagi
        setTimeout(() => {
            initializeCurrentChallenge();
        }, 100);
        return;
    }
    
    // console.log('Initializing current challenge:', componentId);
    initializePatternChallenge(componentId);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // console.log('DOM loaded, initializing captcha');
    initializeCurrentChallenge();
});

// Juga initialize jika DOM sudah ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCurrentChallenge);
} else {
    initializeCurrentChallenge();
}

// Livewire event handlers
document.addEventListener('livewire:init', function() {
    const componentId = '{{ $componentId }}';
    // console.log('Livewire initialized for captcha:', componentId);
    
    Livewire.on('new-challenge-generated', () => {
        // console.log('New challenge generated, reinitializing...');
        setTimeout(() => {
            initializeCurrentChallenge();
        }, 50);
    });
    
    Livewire.on('captchaReset', () => {
        // console.log('Captcha reset, disabling login button');
        disableLoginButton();
    });
    
    Livewire.on('patternMismatch', () => {
        // console.log('Pattern mismatch detected');
        // Error sudah ditangani oleh generateNewChallenge()
    });
    
    Livewire.on('captchaVerified', () => {
        // console.log('Captcha verified successfully');
        enableLoginButtonDirectly();
    });
    
    // Handle Livewire component updates
    Livewire.hook('element.updated', (el, component) => {
        if (el.id === componentId) {
            // console.log('Captcha element updated, reinitializing...');
            setTimeout(() => {
                initializeCurrentChallenge();
            }, 50);
        }
    });
});

// Handle manual refresh button
document.addEventListener('click', function(e) {
    if (e.target.closest('[wire\\:click="generateNewChallenge"]')) {
        // console.log('Manual refresh triggered');
        disableLoginButton();
    }
});
</script>