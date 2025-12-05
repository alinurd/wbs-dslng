<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class InnovativeCaptcha extends Component
{
    public $captchaVerified = false;
    public $captchaText = '';
    public $userInput = '';
    public $errorMessage = '';
    public $componentId;
    
    protected $listeners = ['verifyCaptcha'];

    public function mount()
    {
        $this->componentId = 'captcha-' . Str::random(8);
        $this->generateCaptcha();
    }

    public function generateCaptcha()
    {
        // Generate 4 digit angka acak (0000-9999)
        $this->captchaText = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        Cache::put('captcha_'.$this->componentId, $this->captchaText, 600);
    }

    public function verifyCaptcha($input = null)
    {
        $input = $input ?? $this->userInput;
        $cachedText = Cache::get('captcha_'.$this->componentId);
        
        if (!$cachedText) {
            $this->errorMessage = 'Kode kadaluarsa';
            $this->generateCaptcha();
            return;
        }
        
        if (trim($input) === $cachedText) {
            $this->captchaVerified = true;
            $this->errorMessage = '';
            Cache::forget('captcha_'.$this->componentId);
            $this->dispatch('captchaVerified');
        } else {
            $this->errorMessage = 'Kode salah, coba lagi';
            $this->userInput = '';
            $this->generateCaptcha();
        }
    }
    
    public function render()
    {
        return view('livewire.captcha');
    }
}