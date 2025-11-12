<?php

namespace App\Livewire\NewsDetail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Layout extends Component
{
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
        return view('livewire.news-detail.layout', [
            'currentLocale' => $this->locale,
        ]);
    }
}
