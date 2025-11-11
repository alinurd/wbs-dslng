<?php

namespace App\View\Components;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public $locale;
    
    public function __construct()
    {
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }

    public function changeLanguage($lang)
{
    $this->locale = $lang;
    Session::put('locale', $lang);
    App::setLocale($lang);
    
    $this->dispatch('languageChanged');
}

    public function render(): View
    {
        return view('components.layouts.guest', [
           'currentLocale' => $this->locale,
        ]);
    }
}