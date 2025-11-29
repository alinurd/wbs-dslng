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
        $this->newCategory = Combo::where('kelompok', 'news')
            ->select('data_id', 'data_en', 'data', 'id')
            ->where('is_active', true) 
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
                'news', 
                'public'
            );
        }
        // Upload image
        $imagePath = null;
        if (!empty($this->form['image']) && is_object($this->form['image'])) {
            $uploadedImages = FileHelper::uploadMultiple(
                [$this->form['image']], 
                'news', 
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
            'image' => !empty($imagePath) ? json_encode($imagePath) : null, 
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

    public function view($id)
{
    can_any([strtolower($this->modul) . '.view']);

    $news = $this->model::findOrFail($id);

    $imageData = $news->image ? json_decode($news->image, true) : null;
    $imagePath = $imageData['path'] ?? null; 
    $categoryName = $news->categoryData ? $news->categoryData->data_id : 'Uncategorized';

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

    // Format size file menjadi readable
    $formattedFilesData = [];
    foreach ($filesData as $file) {
        $formattedFilesData[] = [
            'path' => $file['path'],
            'filename' => $file['filename'],
            'original_name' => $file['original_name'],
            'extension' => $file['extension'],
            'size' => $file['size'] ? $this->formatFileSize($file['size']) : '0 KB'
        ];
    }

    // Struktur data yang sesuai dengan komponen Blade
    $this->detailData = [
        'id' => [
            'Judul' => $news->title_id,
            'Konten' => purifyHtml($news->content_id),
        ],
        'en' => [
            'Title' => $news->title_en,
            'Content' => purifyHtml($news->content_en),
        ],
        'common' => [
            // 'ID' => $news->id,
            'Kode Berita' => $news->code_news,
            // 'Slug' => $news->code_news,
            'Kategori' => $categoryName,
            // 'Kategori Slug' => $news->categoryData->param_str_1 ?? 'general',
            'Gambar' => $imagePath,
            'File' => $formattedFilesData,
            'Dibuat Pada' => $news->created_at,
        ]
    ];

    $this->detailTitle = "Detail " . $this->title;
    $this->showDetailModal = true;
}

// Helper method untuk format file size
private function formatFileSize($bytes)
{
    if ($bytes == 0) return '0 Bytes';
    
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

     

public function edit($id)
    {
        can_any([strtolower($this->modul) . '.edit']);

        $record = ($this->model)::findOrFail($id);

        foreach ($this->formDefault as $key => $default) {
            $this->form[$key] = $record->$key ?? $default;
        }

        $this->form['id'] = $id;

        $this->updateMode = true;
        $this->showModal = true;

        $this->dispatch('modalOpened');
    }
    
}