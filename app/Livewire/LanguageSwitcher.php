<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $locale;

    public function mount()
    {
        $this->locale = session('locale', config('app.locale'));
    }

   public function change($lang)
{
    session(['locale' => $lang]);
    app()->setLocale($lang);

    return redirect(request()->header('Referer'));
}


    public function render()
    {
        return view('livewire.language-switcher');
    }
}

