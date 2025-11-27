<?php

namespace App\Livewire\WbsLanding;
 
class Index extends RootLanding
{ 
    
    public function mount()
    { 
        parent::mount();
        $this->newsData = $this->getAllNews(4); 
    }

    public function render()
    {
        return view('livewire.wbs-landing.index')
            ->layout('components.layouts.guest', [
                'title' => 'Home',
                'currentLocale' => app()->getLocale(),
            ]);
    }
}