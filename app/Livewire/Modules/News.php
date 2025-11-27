<?php
namespace App\Livewire\Modules;

use App\Helpers\FileHelper;
use App\Livewire\Root;
use App\Models\Combo;
use App\Models\News as NewModel; 
use App\Traits\HasChat;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class News extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'news';
    public $model = NewModel::class; 
    public $views = 'modules.news';
    public $title = "News";
    public $dataFAQ = [];
    public $newCategory = [];
    
    public $content_id = '';
    public $content_en = '';
    
    public $form = [
        'category' => '',
        'title_id' => '',
        'title_en' => '',
        'files' => [], 
        'image' => null, 
        'is_active' => true, 
    ];

    public function mount()
    {
        parent::mount(); 
        $this->newCategory = Combo::where('kelompok', 'pertanyaan')
            ->select('data_id', 'data_en', 'data', 'id')
            ->where('is_active', true)
            ->where('param_int', true)
            ->orderBy('data_id')
            ->get(); 
    }

    // Event listener dengan nama yang lebih spesifik
    #[On('editor-content-updated')]
    public function handleEditorContentUpdated($model, $content)
    {
       
        
        if ($model === 'content_id') {
            $this->content_id = $content;
        } elseif ($model === 'content_en') {
            $this->content_en = $content;
        }
    }

    protected function saving()
    {
        // Upload files
        $filesPaths = [];
        if (!empty($this->form['files']) && is_array($this->form['files'])) {
            $filesPaths = FileHelper::uploadMultiple(
                $this->form['files'], 
                'pengaduan/news', 
                'public'
            );
        }

        // Upload image
        $imagePath = null;
        if (!empty($this->form['image']) && is_object($this->form['image'])) {
            $uploadedImages = FileHelper::uploadMultiple(
                [$this->form['image']], 
                'pengaduan/news', 
                'public'
            );
            $imagePath = !empty($uploadedImages) ? $uploadedImages[0] : null;
        }

        $codeNews = Str::random(8);

        $payload = [
            'category' => $this->form['category'],
            'title_id' => $this->form['title_id'],
            'title_en' => $this->form['title_en'],
            'content_id' => $this->content_id ?: '',
            'content_en' => $this->content_en ?: '',
            'is_active' => $this->form['is_active'],
            'files' => !empty($filesPaths) ? json_encode($filesPaths) : null,
            'image' => $imagePath,
            'code_news' => $codeNews,
            'created_by' => auth()->id(),
        ]; 
        
        return $payload;
    }

    public function saved($record, $action)
    {
        $message = $action === 'create' 
            ? 'News berhasil dibuat dengan kode: ' . $record->code_news
            : 'News berhasil diperbarui.';

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);

        $this->resetForm();
        $this->closeModal();
    }

    protected function resetForm()
    {
        $this->form = [
            'category' => 'p_faq',
            'title_id' => '',
            'title_en' => '',
            'files' => [],
            'image' => null,
            'is_active' => true,
        ];
        
        $this->content_id = '';
        $this->content_en = '';
        
        $this->resetErrorBag();
        $this->resetValidation();
    }

     
}