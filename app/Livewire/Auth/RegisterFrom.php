<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class RegisterFrom extends Component
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
    public $reporter_type = 'employee';
    public $verification_code;
    public $confirmation = false;

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
        $this->validate();

        // Proses registrasi
        $user = User::create([
            'username' => $this->username,
            'security_question' => $this->security_question,
            'security_answer' => Hash::make($this->answer),
            'password' => Hash::make($this->password),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'id_number' => $this->id_number,
            'phone' => $this->phone,
            'reporter_type' => $this->reporter_type,
        ]);

        // Login user atau redirect
        auth()->login($user);

        return redirect()->route('dashboard');
    }
}