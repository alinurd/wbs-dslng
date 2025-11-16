<?php
namespace App\Livewire\Modules\Pengaduan;

use App\Helpers\FileHelper;
use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Comment;
use App\Models\Pengaduan;
use Livewire\WithFileUploads;

class Tracking extends Root
{
    use WithFileUploads;

    public $modul = 'p_tracking';
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.tracking';
    public $title = "Lacak Aduan";
    
    // Properties untuk chat
    public $trackingId = null;
    public $newMessage = '';
    public $messages = [];
    public $showComment = false;
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload di chat
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];
    public $attachFile = null;

    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
    }

    public function query()
    {
        $q = ($this->model)::query();
        
        if ($this->search && method_exists($this, 'columns')) {
            $columns = $this->columns();
            if (is_array($columns) && count($columns)) {
                $q->where(function ($p) use ($columns) {
                    foreach ($columns as $col) {
                        $p->orWhere($col, 'like', "%{$this->search}%");
                    }
                });
            }
        }

        if (is_array($this->filters)) {
            foreach ($this->filters as $key => $val) {
                if ($key == 'tahun' && !empty($val)) {
                    $q->whereRaw('YEAR(tanggal_pengaduan) = ?', [$val]);
                }
                if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                    $q->where('jenis_pengaduan_id', $val);
                }
            }
        }

        return $q;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
        ];
        
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

    public function comment($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::with(['comments.user'])->findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenisPengaduan->name ?? 'Tidak diketahui',
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
            'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
            'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
        ];
        
        $this->detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
        $this->showComment = true;
        $this->trackingId = $record->id;
        
        // Load data yang diperlukan
        $this->loadMessages();
        $this->loadUploadedFiles();
    }

    // Send message dengan attachment file
    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:attachFile|string|max:1000',
            'attachFile' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,rar',
        ], [
            'newMessage.required_without' => 'Pesan atau file harus diisi',
            'attachFile.max' => 'Ukuran file maksimal 10MB',
            'attachFile.mimes' => 'Format file harus: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR',
        ]);

        if (!$this->trackingId) return;

        $fileData = null;
        
        // Upload file jika ada
        if ($this->attachFile) {
            $uploadedFile = FileHelper::upload(
                $this->attachFile, 
                'pengaduan/chat', 
                'public'
            );
            
            $fileData = [
                'path' => $uploadedFile['path'],
                'original_name' => $uploadedFile['original_name'],
                'size' => $uploadedFile['size'],
                'type' => $uploadedFile['mime_type'],
            ];
        }

        // Create comment
        $comment = Comment::create([
            'pengaduan_id' => $this->trackingId,
            'user_id' => auth()->id(),
            'message' => $this->newMessage,
            'file_data' => $fileData ? json_encode($fileData) : null,
        ]);

        // Reset form
        $this->newMessage = '';
        $this->attachFile = null;
        $this->loadMessages();
    }

    // Load messages untuk chat
    public function loadMessages()
    {
        if (!$this->trackingId) return;

        $chatMessages = Comment::where('pengaduan_id', $this->trackingId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->messages = $chatMessages->map(function ($message) {
            $fileData = null;
            if ($message->file_data) {
                $fileData = json_decode($message->file_data, true);
                if ($fileData) {
                    $fileData['icon'] = FileHelper::getFileIcon(pathinfo($fileData['original_name'], PATHINFO_EXTENSION));
                    $fileData['formatted_size'] = FileHelper::formatSize($fileData['size']);
                }
            }

            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender' => $message->user->name,
                'is_own' => $message->user_id === auth()->id(),
                'time' => $message->created_at->format('H:i'),
                'date' => $message->created_at->format('d M Y'),
                'avatar' => $message->user->profile_photo_url ?? null,
                'file' => $fileData,
            ];
        })->toArray();
    }
 
 

  
 
    // Reset file attachment di chat
    public function resetFileAttachment()
    {
        $this->attachFile = null;
    }

    // Close comment modal
    public function closeComment()
    {
        $this->showComment = false;
        $this->trackingId = null;
        $this->newMessage = '';
        $this->messages = [];
        $this->fileUpload = null;
        $this->fileDescription = '';
        $this->uploadedFiles = [];
        $this->attachFile = null;
    }

    // Get file icon untuk display
    public function getFileIcon($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return FileHelper::getFileIcon($extension);
    }

    // Format file size untuk display
    public function formatFileSize($bytes)
    {
        return FileHelper::formatSize($bytes);
    }
















    public function uploadFile()
    {
        $this->validate([
            'fileUpload' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,rar',
            'fileDescription' => 'nullable|string|max:255',
        ]);

        if (!$this->trackingId) return;

        $uploadedFile = FileHelper::upload(
            $this->fileUpload, 
            'pengaduan/attachments', 
            'public'
        );

        // PERBAIKAN: Pastikan struktur data konsisten
        $fileInfo = [
            'id' => uniqid(),
            'name' => $uploadedFile['original_name'], // Pastikan key 'name' ada
            'path' => $uploadedFile['path'],
            'size' => $uploadedFile['size'],
            'type' => $uploadedFile['mime_type'],
            'description' => $this->fileDescription,
            'uploaded_at' => now()->format('d/m/Y H:i'),
            'uploaded_by' => auth()->user()->name,
            'formatted_size' => FileHelper::formatSize($uploadedFile['size']), // Tambahkan formatted_size
            'icon' => FileHelper::getFileIcon(pathinfo($uploadedFile['original_name'], PATHINFO_EXTENSION)), // Tambahkan icon
        ];

        // Tambahkan ke list uploaded files
        $this->uploadedFiles[] = $fileInfo;

        // Reset form
        $this->fileUpload = null;
        $this->fileDescription = '';

        // Tampilkan notifikasi sukses
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'File berhasil diupload'
        ]);
    }

    // Load uploaded files
    public function loadUploadedFiles()
    {
        if (!$this->trackingId) return;

        // TODO: Load files dari database jika sudah disimpan
        // Untuk sementara, kita reset uploadedFiles
        $this->uploadedFiles = [];
    }

    // Download file
    public function downloadFile($fileId)
    {
        $file = collect($this->uploadedFiles)->firstWhere('id', $fileId);
        
        if ($file && FileHelper::exists($file['path'])) {
            return response()->download(
                storage_path('app/public/' . $file['path']),
                $file['name'] // Pastikan key 'name' ada
            );
        }

        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'File tidak ditemukan'
        ]);
    }

    // Download file dari chat message
    public function downloadMessageFile($messageId)
    {
        $message = Comment::find($messageId);
        
        if ($message && $message->file_data) {
            $fileData = json_decode($message->file_data, true);
            
            if ($fileData && FileHelper::exists($fileData['path'])) {
                return response()->download(
                    storage_path('app/public/' . $fileData['path']),
                    $fileData['original_name'] // Pastikan key 'original_name' ada
                );
            }
        }

        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'File tidak ditemukan'
        ]);
    }

    // Delete file
    public function deleteFile($fileId)
    {
        $file = collect($this->uploadedFiles)->firstWhere('id', $fileId);
        
        if ($file) {
            // Hapus dari storage
            FileHelper::delete($file['path']);
            
            // Hapus dari list
            $this->uploadedFiles = collect($this->uploadedFiles)
                ->reject(function ($item) use ($fileId) {
                    return $item['id'] === $fileId;
                })
                ->values()
                ->toArray();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'File berhasil dihapus'
            ]);
        }
    }

 

    // Helper untuk mendapatkan file info dengan aman
    public function getFileInfo($file)
    {
        return [
            'name' => $file['name'] ?? $file['original_name'] ?? 'Unknown File',
            'size' => $file['formatted_size'] ?? FileHelper::formatSize($file['size'] ?? 0),
            'icon' => $file['icon'] ?? FileHelper::getFileIcon(pathinfo($file['name'] ?? $file['original_name'] ?? '', PATHINFO_EXTENSION)),
            'description' => $file['description'] ?? '',
        ];
    }
}