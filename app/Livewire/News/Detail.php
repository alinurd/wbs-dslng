<?php

namespace App\Livewire\News;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Detail extends Component
{
    public $title = "PT DONGGI-SENORO LNG";
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
        return view('livewire.news.index')
            ->layout('components.layouts.guest', [
                'title' => 'News Detail',
                'currentLocale' => app()->getLocale(),
            ]);
    }
}