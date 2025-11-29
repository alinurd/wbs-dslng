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

    
    public $showModal = false;
    public $updateMode = false;

    public $modul = 'news';
    public $model = NewModel::class; 
    public $views = 'modules.news';
    public $title = "News";
    public $dataFAQ = [];
    public $newCategory = [];
      public $existingImage = null;
    public $existingFiles = null;
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

      protected $listeners = [
        'editorContentUpdated' => 'handleEditorContentUpdate',
        'modalOpened' => 'handleModalOpened'
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
    // Handle files - pertahankan file lama jika tidak ada upload baru
    $filesPaths = $this->existingFiles ?? null;
    
    // Jika ada file baru yang diupload
    if (!empty($this->form['files']) && is_array($this->form['files'])) {
        $newFilesPaths = FileHelper::uploadMultiple(
            $this->form['files'], 
            'news', 
            'public'
        );
        
        // Jika ada file lama, gabungkan dengan file baru
        if ($filesPaths && !empty($newFilesPaths)) {
            $existingFilesArray = json_decode($filesPaths, true) ?? [];
            $filesPaths = json_encode(array_merge($existingFilesArray, $newFilesPaths));
        } elseif (!empty($newFilesPaths)) {
             $filesPaths = json_encode($newFilesPaths);
        }
    }
    
     $imagePath = $this->existingImage ?? null;
    
     if (!empty($this->form['image']) && is_object($this->form['image'])) {
        $uploadedImages = FileHelper::uploadMultiple(
            [$this->form['image']], 
            'news', 
            'public'
        );
        $imagePath = !empty($uploadedImages) ? json_encode($uploadedImages[0]) : null;
    }
    
    $codeNews = Str::random(8);
// 
    $payload = [
        'category' => $this->form['category'],
        'title_id' => $this->form['title_id'],
        'title_en' => $this->form['title_en'],
        'content_id' => $this->content_id ?: '',
        'content_en' => $this->content_en ?: '',
        'is_active' => $this->form['is_active'],
        'files' => $filesPaths,
        'image' => $imagePath, 
        'code_news' => $codeNews,
        'updated_by' => auth()->id(),
    ];
    
    // Untuk create, set created_by
    if (!$this->updateMode) {
        $payload['created_by'] = auth()->id();
    }
    
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
         $this->existingImage = null;
    $this->existingFiles = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function view($id)
{
    can_any([strtolower($this->modul) . '.view']);

    $news = $this->model::findOrFail($id);

    $imageData = $news->image ? json_decode($news->image, true) : null;
   
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
            'Gambar' =>[
                'imagePath'=>$imageData['path']?? null,
                'original_name'=>$imageData['original_name']?? null,
            ],
            'File' => $formattedFilesData,
            'Dibuat Pada' => $news->created_at,
        ]
    ];

    $this->detailTitle = "Detail " . $this->title;
    $this->showDetailModal = true;
}

public function isValidFileFormat($file, $allowedFormats)
{
    if (empty($file)) {
        return false;
    }

    // Clean allowed formats - remove empty values
    $allowedFormats = array_filter($allowedFormats, function($format) {
        return !empty(trim($format));
    });

    // Jika file adalah object Livewire UploadedFile (file baru)
    if (is_object($file) && method_exists($file, 'getClientOriginalExtension')) {
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $allowedFormats);
    }

    // Jika file adalah string JSON (data existing dari database) - skip validation
    if (is_string($file) && $this->isJsonString($file)) {
        return true; // Skip validation untuk data JSON existing dari database
    }

    // Jika file adalah string path biasa
    if (is_string($file)) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($extension, $allowedFormats);
    }

    return false;
}

/**
 * Helper method untuk generate accept attribute dengan perbaikan
 */
public function getAcceptAttribute($allowedFormats)
{
    // Clean allowed formats - remove empty values
    $allowedFormats = array_filter($allowedFormats, function($format) {
        return !empty(trim($format));
    });

    $mimeTypes = [
        'zip' => '.zip',
        'rar' => '.rar',
        'doc' => '.doc',
        'docx' => '.docx,.doc',
        'xls' => '.xls',
        'xlsx' => '.xlsx,.xls',
        'ppt' => '.ppt',
        'pptx' => '.pptx,.ppt',
        'pdf' => '.pdf',
        'jpg' => '.jpg,.jpeg',
        'jpeg' => '.jpg,.jpeg',
        'png' => '.png',
        'gif' => '.gif',
        'webp' => '.webp',
        'avi' => '.avi',
        'mp4' => '.mp4',
        '3gp' => '.3gp',
        'mp3' => '.mp3',
    ];

    $accept = [];
    foreach ($allowedFormats as $format) {
        $format = trim($format);
        if (isset($mimeTypes[$format])) {
            $accept[] = $mimeTypes[$format];
        } else {
            $accept[] = ".{$format}";
        }
    }

    return implode(',', array_unique($accept));
}
 
 
public function edit($id)
{
    can_any([strtolower($this->modul) . '.edit']);

    $record = ($this->model)::findOrFail($id);

    foreach ($this->formDefault as $key => $default) {
        $this->form[$key] = $record->$key ?? $default;
    }

    $this->form['id'] = $id;
    
    // Set content untuk editor
    $this->content_id = $record->content_id ?? '';
    $this->content_en = $record->content_en ?? '';
    
    // Simpan data file lama untuk referensi
    $this->existingImage = $record->image;
    $this->existingFiles = $record->files;
    
    $this->updateMode = true;
    $this->showModal = true;

    // Dispatch event setelah modal terbuka
    $this->dispatch('modal-opened');
}


    public function handleEditorContentUpdate($model, $content)
    {
        // Update property berdasarkan model
        $this->$model = $content;
    }

    public function handleModalOpened()
    {
        // Refresh editor content dengan delay sedikit
        $this->dispatch('refreshEditor', content: $this->content_id);
        $this->dispatch('refreshEditor', content: $this->content_en);
    }











    public function removeFileCore($model, $index, $type = 'new')
{ 
    
    if ($type === 'existing') {
        $this->removeExistingFile($model, $index);
    } else {
        $this->removeNewFile($model, $index);
    } 
}

private function removeExistingFile($model, $index)
{ 
    
    if ($model === 'form.files' && !empty($this->existingFiles)) {
        $filesArray = json_decode($this->existingFiles, true); 
        
        if (is_array($filesArray) && isset($filesArray[$index])) { 
            // Hapus file dari array
            array_splice($filesArray, $index, 1); 
            
            if (empty($filesArray)) {
                $this->existingFiles = null; 
            } else {
                $this->existingFiles = json_encode($filesArray); 
            }
             
        } else { 
            if (is_array($filesArray)) { 
            }
        }
    } elseif ($model === 'form.image' && !empty($this->existingImage)) { 
        $this->existingImage = null; 
    } else { 
    }
     
}

private function removeNewFile($model, $index)
{ 
    
    $files = data_get($this, $model);
     
    if (is_array($files)) { 
        
        if (isset($files[$index])) { 
            
            // Hapus file dari array
            array_splice($files, $index, 1); 
            
            data_set($this, $model, empty($files) ? null : $files);
         } else {
            \Log::debug("❌ File not found at index " . $index);
        }
    } else {
        \Log::debug("❌ Files is not an array, setting to null");
        data_set($this, $model, null);
    }
    
    $filesAfter = data_get($this, $model); 
}
}