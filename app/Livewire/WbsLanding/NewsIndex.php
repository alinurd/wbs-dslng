<?php

namespace App\Livewire\WbsLanding;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class NewsIndex extends RootLanding
{
    
    public function mount($slug='')
    {
        parent::mount();
        $this->newsData = $this->getAllNews(); 
    }
    
    public function render()
    {
                return view('livewire.wbs-landing.news-index')
            ->layout('components.layouts.guest', [
                'title' => 'News',
                'currentLocale' => app()->getLocale(),
            ]);
    }
    
}