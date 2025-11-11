<?php

namespace App\Livewire\WbsLanding;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class Index extends Component
{
    public $title = "Whistleblowing System - PT DONGGI-SENORO LNG";
    public $currentLocale = 'en';

    public function mount()
    {
        // Set locale dari session atau default 'en'
        $this->currentLocale = session('locale', 'en');
        App::setLocale($this->currentLocale);
    }

    public function changeLanguage($locale)
{
    if (!in_array($locale, ['en', 'id'])) {
        return;
    }

    session(['locale' => $locale]);
    App::setLocale($locale);
    $this->currentLocale = $locale;

    $this->title = $locale === 'id' 
        ? "Sistem Pelaporan - PT DONGGI-SENORO LNG"
        : "Whistleblowing System - PT DONGGI-SENORO LNG";

    // Dispatch event untuk JavaScript
    $this->dispatch('languageChanged', locale: $locale);
}

    public function render()
    {
        return view('livewire.wbs-landing.index')
            ->layout('components.layouts.wbs-landing', [
                'title' => $this->title,
                'currentLocale' => $this->currentLocale
            ]);
    }
}