<?php

namespace App\Livewire\Auth;

use App\Models\Audit as AuditLog;
use App\Models\User;
use App\Services\EmailService;
 use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
 
class ForgotPassword extends Component
{
    public $email = '';
     public $remember = false;
    public $captchaVerified = false;
    
    protected $rules = [
        'email' => 'required|email',
     ];

    protected $messages = [ 
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
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.guest', [
                'title' => 'forgot-password',
                'currentLocale' => app()->getLocale(),
            ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

  public function forgot()
{
    $this->validate([
        'email' => 'required|email|exists:users,email'
    ]);
    
    $user = User::where('email', $this->email)->first();
    
    if (!$user) {
        return back()->withErrors(['email' => 'User not found']);
    }
    
    // Generate reset token and link
    $token = Str::uuid(); // or Str::random(60)
    $link = route('password.reset', ['token' => $token]);
    
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $user->email],
        [
            'token' => Hash::make($token),
            'created_at' => now()
        ]
    );
    
    // Send email
    $emailService = new EmailService();
    $emailSent = $emailService->setUserId($user->id)
                             ->sendforgotPassword($user->email, $link, $user->name);
    
    AuditLog::create([
        'user_id' => $user->id, 
        'action' => 'password_reset_request',
        'table_name' => 'password_resets',
        'record_id' => $user->id,
        'old_values' => null,
        'new_values' => json_encode([
            'reset_token_created' => now(),
            'email' => $user->email,
            'link' => $link
        ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now()
    ]);

    return redirect()->route('login')
        ->with('success', 'Password reset link has been sent to your email.');
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