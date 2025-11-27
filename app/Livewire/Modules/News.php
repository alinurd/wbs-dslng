<?php
namespace App\Livewire\Modules;

use App\Helpers\FileHelper;
use App\Livewire\Root;
use App\Models\Combo;
use App\Models\LogApproval;
use App\Models\News as NewModel; 
use App\Traits\HasChat;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class News extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'news';
    public $model = NewModel::class; 
    public $views = 'modules.news';
    public $title = "News";
    public $dataFAQ =[];
    public $newCategory =[];
    
    public $files = [];
    public $title_en = '';
    public $title_id = '';
    public $content_en = '';
    public $content_id = '';
    public $image = null;
    public $category = '';
    public $is_active = true;

    public $form = [
        'category' => '', // Fixed typo from 'categry'
        'title_id' => '',
        'title_en' => '',
        'content_id' => '',
        'content_en' => '',
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

    

    protected function saving()
    {
        // Upload multiple files
        $filesPaths = [];
        if (!empty($this->form['files']) && is_array($this->form['files'])) {
            $filesPaths = FileHelper::uploadMultiple(
                $this->form['files'], 
                'pengaduan/news', 
                'public'
            );
        }

        // Upload single image
        $imagePath = null;
        if (!empty($this->form['image']) && is_object($this->form['image'])) {
            $uploadedImages = FileHelper::uploadMultiple(
                [$this->form['image']], 
                'pengaduan/news', 
                'public'
            );
            $imagePath = !empty($uploadedImages) ? $uploadedImages[0] : null;
        }

        // Generate unique code
        $codeNews = Str::random(8);
// \dd($this->form);
        // Build payload
        $payload = [
            'category' => $this->form['category'],
            'title_id' => $this->form['title_id'],
            'title_en' => $this->form['title_en'],
            'content_id' => $this->form['content_id'],
            'content_en' => $this->form['content_en'],
            'is_active' => $this->form['is_active'],
             'files' => !empty($filesPaths) ? json_encode($filesPaths) : null, // ENCODE to JSON
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

        // Reset form
        $this->resetForm();
        $this->closeModal();
        
    }

    protected function resetForm()
    {
        $this->form = [
            'category' => 'p_faq',
            'title_id' => '',
            'title_en' => '',
            'content_id' => '',
            'content_en' => '',
            'files' => [],
            'image' => null,
            'is_active' => true,
        ];
        
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // Method untuk remove file
    public function removeFile($model, $index)
    {
        $files = data_get($this, $model);
        
        if ($files === null) {
            return;
        }
        
        // Handle single file (bukan array)
        if (!is_array($files)) {
            data_set($this, $model, null);
            return;
        }
        
        // Handle multiple files (array)
        if (isset($files[$index])) {
            unset($files[$index]);
            $files = array_values($files);
            
            if (empty($files)) {
                data_set($this, $model, []);
            } else {
                data_set($this, $model, $files);
            }
        }
    }

    // Method untuk edit data
    public function edit($id)
    {
        parent::edit($id);
        
        $record = $this->model::find($id);
        if ($record) {
            $this->form = [
                'category' => $record->category,
                'title_id' => $record->title_id,
                'title_en' => $record->title_en,
                'content_id' => $record->content_id,
                'content_en' => $record->content_en,
                'files' => [],
                'image' => null,
                'is_active' => $record->is_active,
            ];
        }
    }
 
}