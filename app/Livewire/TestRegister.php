<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class TestRegister extends Component
{
    public $username;
    public $email;
    public $password;

    // Method submit harus public
    public function register()
    {
        // Debug untuk memastikan method terpanggil
        dd([
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        // Contoh menyimpan user
        // User::create([
        //     'username' => $this->username,
        //     'email' => $this->email,
        //     'password' => Hash::make($this->password),
        // ]);
    }

    public function render()
    {
        return view('livewire.test-register')
         ->layout('components.layouts.guest', [
                'title' => 'Register',
                'currentLocale' => 'en',
            ]);;;
    }
}
