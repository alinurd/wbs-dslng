<?php

namespace App\Livewire\WbsLanding;

use App\Models\News;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class NewsDetail extends RootLanding
{
    public $slug;
    public $newsDetail;

    public $currentLocale = 'en';
    public $locale;

    public function mount($slug=null)
    {
        $this->slug = $slug;
        $this->newsDetail = $this->getNewsBySlug($slug);
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
        if (!$this->newsDetail) {
            abort(404);
        }
    }

    /**
     * Get news by slug
     */
    public function getNewsBySlug($slug)
    {
        $news = News::with(['categoryData'])
            ->where('code_news', $slug) // Using code_news as slug
            ->where('is_active', true)
            ->first();

        if (!$news) {
            return null;
        }

        return $this->mapNewsData($news);
    }


   

    
    public function render()
    {
        return view('livewire.wbs-landing.news-detail')
            ->layout('components.layouts.guest', [
                'title' => $this->newsDetail['title_' . $this->locale] ?? 'News Detail',
                'currentLocale' => app()->getLocale(),
            ]);
    }
}