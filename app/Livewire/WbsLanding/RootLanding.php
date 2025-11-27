<?php

namespace App\Livewire\WbsLanding;

use App\Models\News;  
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

abstract class RootLanding extends Component
{
    public $title = "PT DONGGI-SENORO LNG";
    public $currentLocale = 'en';
    public $locale;
    public $newsData = [];
    
    public function mount()
    {
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }
  
    public function getAllNews($limit = 0)
    {
        $query = News::with(['categoryData'])
            ->where('is_active', true)
            ->orderBy('id', 'desc');
 
        if ($limit > 0) {
            $query->limit($limit);
        }

        $news = $query->get();

        return $news->map(function ($item) {
            return $this->mapNewsData($item);
        })->toArray();
    }

    /**
     * Get news by ID with mapping for FE
     */
    public function getNewsById($id)
    {
        $news = News::with(['categoryData'])
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$news) {
            return null;
        }

        return $this->mapNewsData($news);
    }

    /**
     * Map news data for FE requirements
     */
    public function mapNewsData($news)
    {
        $isIndonesian = $this->locale === 'id';
         
        $imageData = $news->image ? json_decode($news->image, true) : null;
        $imagePath = $imageData['path'] ?? null; 
        $categoryField = $isIndonesian ? 'data_id' : 'data_en';
        $categoryName = $news->categoryData ? $news->categoryData->$categoryField : 'Uncategorized';
 
        $filesData = [];
        if ($news->files) {
            $files = json_decode($news->files, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    $filesData[] = [
                        'path' => $file['path'] ?? null,
                        'filename' => $file['filename'] ?? null,
                        'original_name' => $file['original_name'] ?? null,
                        'extension' => $file['extension'] ?? null,
                        'size' => $file['size'] ?? null,
                    ];
                }
            }
        }

        return [
            'id' => $news->id,
            'code_news' => $news->code_news,
            'slug' => $news->code_news,
            'title_id' => $news->title_id,
            'title_en' => $news->title_en,
            'content_id' => purifyHtml($news->content_id),
            'content_en' => purifyHtml($news->content_en),
            'desc_id' => $this->extractDescription($news->content_id),
            'desc_en' => $this->extractDescription($news->content_en),
            'image' => $imagePath,
            'category' => $categoryName,
            'category_slug' => $news->categoryData->param_str_1 ?? 'general',
            'created' => $news->created_at,
            'publish_date' => $news->publish_date,
            'views' => $news->views,
            'files' => $filesData,
        ];
    }

    /**
     * Extract description from content (strip tags and limit length)
     */
    private function extractDescription($content, $length = 150)
    {
        $cleanContent = strip_tags($content);
        $cleanContent = str_replace(['&nbsp;', '\r', '\n'], ' ', $cleanContent);
        $cleanContent = trim(preg_replace('/\s+/', ' ', $cleanContent));
        
        if (strlen($cleanContent) <= $length) {
            return $cleanContent;
        }
        
        return substr($cleanContent, 0, $length) . '...';
    }

    public function changeLanguage($lang)
    {
        $this->locale = $lang;
        Session::put('locale', $lang);
        App::setLocale($lang); 
         
        $this->newsData = $this->getAllNews();
        
        $this->dispatch('reload-page');
    }
}