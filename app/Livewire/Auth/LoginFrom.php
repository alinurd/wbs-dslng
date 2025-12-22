<?php

namespace App\Livewire\Auth;

use App\Models\Audit as AuditLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginFrom extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $captchaVerified = false;
    
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [ 
        'password.required' => 'auth.validation.required', 
        'email.required' => 'auth.validation.required',
        'email.email' => 'auth.validation.email', 
    ];

    // GUNAKAN FORMAT YANG BENAR UNTUK CHILD COMPONENT EVENTS
    protected $listeners = [
        'captchaVerified' => 'handleCaptchaVerified',
        'captchaReset' => 'handleCaptchaReset'
    ];
    
    public $currentLocale = 'en';
    public $locale;
    
    public function mount()
    {
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }

    public function changeLanguage($lang)
    {
        $this->locale = $lang;
        Session::put('locale', $lang);
        App::setLocale($lang);
        $this->dispatch('reload-page');
    }
    
    public function render()
    {
        return view('livewire.auth.login-form')
            ->layout('components.layouts.guest', [
                'title' => 'Login',
                'currentLocale' => app()->getLocale(),
            ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function login()
    {
        $this->validate();
        
        // if (!$this->captchaVerified) {
        //     $this->addError('captcha', 'Harap selesaikan verifikasi keamanan terlebih dahulu.');
        //     return;
        // }

        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => 1
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'login',
            'table_name' => 'users',
            'record_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode(['login_time' => now()]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);

        session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    public function handleCaptchaVerified()
    {
        \Log::info('CAPTCHA VERIFIED - Login component received event');
        $this->captchaVerified = true;
        $this->resetErrorBag('captcha');
        
        // Dispatch event untuk JavaScript
        $this->dispatch('enable-login-button');
    }

    public function handleCaptchaReset()
    {
        \Log::info('CAPTCHA RESET - Login component received event');
        $this->captchaVerified = false;
        $this->dispatch('disable-login-button');
    }
}