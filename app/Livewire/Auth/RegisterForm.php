<?php

namespace App\Livewire\Auth;

use App\Models\Audit as AuditLog;
use App\Models\Combo;

use App\Models\User; 
use App\Services\EmailService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
class RegisterForm extends Component
{
    public $username;
    public $security_question;
    public $answer;
    public $password;
    public $password_confirmation;
    public $full_name;
    public $email;
    public $id_number;
    public $phone;
    public $detail;
    public $reporter_type = 'employee';
    public $verification_code;
    public $confirmation = false;
    public $question = [];

    protected $rules = [
        'username' => 'required|min:3|unique:users',
        'security_question' => 'required',
        'answer' => 'required|min:2',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
        'full_name' => 'nullable|min:2',
        'email' => 'required|email|unique:users',
        'id_number' => 'nullable|min:5',
        'phone' => 'nullable|min:10',
        'reporter_type' => 'required|in:employee,non_employee',
        'verification_code' => 'required',
        'confirmation' => 'accepted'
    ];

    protected $messages = [
        'username.required' => 'auth.validation.required',
        'username.min' => 'auth.validation.min',
        'username.unique' => 'auth.validation.unique',
        'security_question.required' => 'auth.validation.required',
        'answer.required' => 'auth.validation.required',
        'answer.min' => 'auth.validation.min',
        'password.required' => 'auth.validation.required',
        'password.min' => 'auth.validation.min',
        'password.confirmed' => 'auth.validation.confirmed',
        'email.required' => 'auth.validation.required',
        'email.email' => 'auth.validation.email',
        'email.unique' => 'auth.validation.unique',
        'verification_code.required' => 'auth.validation.required',
        'confirmation.accepted' => 'auth.validation.required',
    ];

    public $currentLocale = 'en';

        public $locale;
        
    public function mount()
    {
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
        $this->question = Combo::where('kelompok', 'pertanyaan')
        ->where('is_active', 1)
        ->where('param_int', null)
        ->orderBy('created_at')
        ->get();
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
        return view('livewire.auth.register-form')
         ->layout('components.layouts.guest', [
                'title' => 'Register',
                'currentLocale' => app()->getLocale(),
            ]);;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

public function register()
{
    //  $this->validate();
    $codeVerif =  Str::random(8);
    $user = User::create([
    'username' => $this->username,
    'name' => $this->full_name, // mapping full_name
    'email' => $this->email,
    'password' => Hash::make($this->password),
    'security_question' => $this->security_question,
    'answer' => $this->answer, 
    'no_identitas' => $this->id_number,
    'telepon' => $this->phone,
    'reporter_type' => $this->reporter_type === 'employee' ? 1 : 0,
    'alamat' => $this->detail,
    'code_verif' => $codeVerif,
    'active' => 1,
    'status' => 0,
    'must_change_password' => 0, 
]);

$user->assignRole('user Pelapor');  
$emailService = new EmailService();
    
    $emailSent = $emailService->setUserId($user->id)
                             ->sendVerificationEmail($this->email, $codeVerif, $this->full_name);
                             
AuditLog::create([
        'user_id' => $user->id,
        'action' => 'register',
        'table_name' => 'users',
        'record_id' => $user->id,
        'old_values' => null,
        'new_values' => json_encode([
            'username' => $this->username,
            'name' => $this->full_name,
            'email' => $this->email,
            'security_question' => $this->security_question,
            'no_identitas' => $this->id_number,
            'telepon' => $this->phone,
            'reporter_type' => $this->reporter_type,
            'alamat' => $this->detail,
            'active' => 1,
            'status' => 0,
            'code_verif' => $codeVerif,
            'email_verification_sent' => $emailSent,
            'registered_at' => now()->toDateTimeString()
        ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now()
    ]);

 
    

auth()->login($user);

 if ($emailSent) {
     $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Registrasi berhasil! Silakan cek email Anda untuk kode verifikasi.',
            'errMessage' => ''
        ]);
        
    } else {
           $this->dispatch('notify', [
            // 'type' => 'error',
            // 'message' => 'Registrasi berhasil, Silakan hubungi administrator untuuk lakukan verifikasi',
            
            'type' => 'success',
            'message' => 'Registrasi berhasil! Silakan cek email Anda untuk kode verifikasi.',
            'errMessage' => 'Registrasi berhasil! Namun email verifikasi gagal dikirim. Silakan hubungi administrator.'
        ]);
    }
    
return redirect()->route('dashboard'); 
}

}