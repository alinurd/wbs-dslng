<?php

namespace App\Livewire\Auth;

use App\Models\Audit as AuditLog;
use App\Models\User;
use Carbon\Carbon;
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

    public function login_()
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

    
public function login()
{
    $this->validate();
    
    $user = User::where('email', $this->email)->first();
    
    if (!$user) {
        $this->throwAuthError(__('auth.failed'));
    }
    
    // Handle blocked user
    $this->handleBlockedUser($user);
    
    // Attempt login
    if (!$this->attemptLogin($user)) {
        $this->handleFailedLogin($user);
    }
    
    // Login successful
    $this->handleSuccessfulLogin($user);
    
    session()->regenerate();
    return redirect()->intended('/dashboard');
}

/**
 * Handle blocked user check
 */
private function handleBlockedUser($user): void
{
    if (!$user->blocked_at) return;
    
    $blockedAt = Carbon::parse($user->blocked_at);
    $now = Carbon::now();
    $minutesPassed = $blockedAt->diffInMinutes($now);
    
    if ($minutesPassed < 30) {
        $remainingMinutes = (int) ceil(30 - $minutesPassed);
        $this->throwAuthError(__('auth.accountTempBlock', ['minutes' => $remainingMinutes]));
    }
    
    // Unblock user if 30 minutes have passed
    $user->update([
        'count_try_login' => 0,
        'blocked_at' => null,
    ]);
}

/**
 * Attempt user login
 */
private function attemptLogin($user): bool
{
    return Auth::attempt([
        'email' => $this->email,
        'password' => $this->password,
        'is_active' => 1
    ], $this->remember);
}

/**
 * Handle failed login attempt
 */
private function handleFailedLogin($user): void
{
    $user->increment('count_try_login');
    
     if ($user->count_try_login >= 5) {
         $user->update([
            'blocked_at' => now(),
            'count_try_login' => 5
        ]);
        
            
    AuditLog::create([
        'user_id' => $user->id,
        'action' => 'login_failed',
        'table_name' => 'users',
        'record_id' => $user->id,
        'old_values' => null,
        'new_values' => json_encode([
            'login_time' => now(),
            'ip_address' => request()->ip(),
            'is_active' => ($user->is_active==1?'Active': 'Non Active'),
            'count_try_login' => $user->count_try_login,
            'blocked_at' => $user->blocked_at,
            //  'username' => $this->email,
            // 'password' => $this->password,
        ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now()
    ]);


        $this->throwAuthError(__('auth.accountBlock', ['count' => 5]));
    }
    AuditLog::create([
        'user_id' => $user->id,
        'action' => 'login_failed',
        'table_name' => 'users',
        'record_id' => $user->id,
        'old_values' => null,
        'new_values' => json_encode([
            'login_time' => now(),
            'ip_address' => request()->ip(),
            'is_active' => ($user->is_active==1?'Active': 'Non Active'),
            'count_try_login' => $user->count_try_login,
            'blocked_at' => $user->blocked_at,
            //  'username' => $this->email,
            // 'password' => $this->password,
        ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now()
    ]);
    
    $remainingAttempts = 5 - $user->count_try_login;
    $errorMessage = __('auth.failed') . ' ' . __('auth.remainingAttempts', ['attempts' => $remainingAttempts]);
    
    $this->throwAuthError($errorMessage);
}

/**
 * Handle successful login
 */
private function handleSuccessfulLogin($user): void
{
        $ctlOld = $user->count_try_login;
        $baOld = $user->blocked_at;
    // Reset login attempts
    $user->update([
        'count_try_login' => 0,
        'blocked_at' => null,
    ]);
    
    // Create audit log
    AuditLog::create([
        'user_id' => Auth::id(),
        'action' => 'login',
        'table_name' => 'users',
        'record_id' => Auth::id(),
        'old_values' => json_encode([
            'login_time' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent(), 0, 255),
            'count_try_login' => $ctlOld,
            'blocked_at' => $baOld,

        ]),
        'new_values' => json_encode([
            'login_time' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent(), 0, 255),
                        'count_try_login' => $user->count_try_login,
                        'blocked_at' => $user->blocked_at,

        ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now()
    ]);
    
    // Optional: Log last login time
    $user->update(['last_login_at' => now()]);
}

/**
 * Throw authentication error
 */
private function throwAuthError(string $message): void
{
    throw ValidationException::withMessages([
        'email' => $message,
    ]);
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