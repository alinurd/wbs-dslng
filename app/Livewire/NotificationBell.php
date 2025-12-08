<?php

namespace App\Livewire;

use App\Helpers\FileHelper;
use App\Models\Combo;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class NotificationBell extends Component
{
    public $isOpen = false;
    public $unreadCount = 0;
    public $notifications = [];
     public $activeFilter = 'all';
    public $detailData = [];
    public $detailTitle = '';
      public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];
    
    use WithFileUploads, HasChat;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        try {
            $dbNotifications = Notification::where('to', Auth::id())
            ->where('is_read', 0)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $this->notifications = $dbNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $this->getNotificationType($notification->type, $notification->type_text),
                    'type_class' => $this->getTypeClass($notification->type, $notification->type_text),
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time' => $notification->created_at,
                    'read' => (bool) $notification->is_read,
                    'icon' => $this->getIcon($notification->type, $notification->type_text),
                    'badge_color' => $this->getBadgeColor($notification->type, $notification->type_text)
                ];
            })->toArray();

            // if (empty($this->notifications)) {
            //     $this->loadSampleNotifications();
            // }

            $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
            // $this->loadSampleNotifications();
        }
    }

    private function getNotificationType($type, $typeText)
    {
        if (!empty($typeText)) {
            $typeText = strtolower(trim($typeText));
            
            if (str_contains($typeText, 'chat') || str_contains($typeText, 'pesan') || str_contains($typeText, 'message')) {
                return 'chat';
            } elseif (str_contains($typeText, 'complien') || str_contains($typeText, 'persetujuan') || 
                     str_contains($typeText, 'approve') || str_contains($typeText, 'disetujui')) {
                return 'complien';
            } else {
                return 'other';
            }
        }
        
        return match($type) {
            1 => 'chat',
            2 => 'complien',
            default => 'other',
        };
    }

    private function getIcon($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);
        
        return match($notificationType) {
            'chat' => 'fas fa-comment-alt',
            'complien' => 'fas fa-clipboard-check',
            'other' => 'fas fa-bell',
            default => 'fas fa-bell',
        };
    }

      private function getTypeClass($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);
        
        return match($notificationType) {
            'chat' => 'bg-blue-100 text-blue-800 border-blue-300',
            'complien' => 'bg-green-100 text-green-800 border-green-300',
            'other' => 'bg-gray-100 text-gray-800 border-gray-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
 private function getBadgeColor($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);
        
        return match($notificationType) {
            'chat' => 'blue',
            'complien' => 'green',
            'other' => 'gray',
            default => 'gray',
        };
    }
 

    public function filterNotifications($filter)
    {
        $this->activeFilter = $filter;
    }

    public function getFilteredNotificationsProperty()
    {
        $notifications = collect($this->notifications);
        
        return match($this->activeFilter) {
            'unread' => $notifications->where('read', false)->values()->toArray(),
            'chat' => $notifications->where('type', 'chat')->values()->toArray(),
            'complien' => $notifications->where('type', 'complien')->values()->toArray(),
            'other' => $notifications->where('type', 'other')->values()->toArray(),
            default => $notifications->values()->toArray(),
        };
    }

    public function getNotificationCountsProperty()
    {
        $notifications = collect($this->notifications);
        
        return [
            'all' => $notifications->count(),
            'unread' => $notifications->where('read', false)->count(),
            'chat' => $notifications->where('type', 'chat')->count(),
            'complien' => $notifications->where('type', 'complien')->count(),
            'other' => $notifications->where('type', 'other')->count(),
        ];
    }

    // Untuk kompatibilitas dengan view lama (opsional)
    public function getUnreadNotificationsProperty()
    {
        return collect($this->notifications)->where('read', false)->values();
    }

    public function getReadNotificationsProperty()
    {
        return collect($this->notifications)->where('read', true)->values();
    }

public function toggleNotifications()
{
    $this->isOpen = !$this->isOpen;
    // Tidak perlu loadNotifications() di sini karena sudah di handle Alpine.js
}

public function markAsRead($notificationId)
{
    try {
        // Update database
        \DB::table('notifications')
            ->where('id', $notificationId)
            ->where('to', Auth::id())
            ->update(['is_read' => 1]);
        
        // Update local state
        $this->notifications = collect($this->notifications)
            ->reject(fn($notification) => $notification['id'] == $notificationId)
            ->values()
            ->toArray();
        
        $this->unreadCount = collect($this->notifications)->where('read', false)->count();
        
        // Dispatch event untuk animasi
        $this->dispatch('notification-marked-read', ['id' => $notificationId]);
        
    } catch (\Exception $e) {
        Log::error('Error marking notification as read: ' . $e->getMessage());
    }
}

public function markAllAsRead()
{
    try {
        // Update semua di database
        Notification::where('to', Auth::id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        
        // Hapus semua dari array notifications
        $this->notifications = collect($this->notifications)
            ->map(function ($notification) {
                $notification['read'] = true;
                return $notification;
            })
            ->toArray();
        
        $this->unreadCount = 0;
        
        // Dispatch event untuk refresh
        $this->dispatch('notifications-updated');
        
        Log::info('All notifications marked as read', ['user_id' => Auth::id()]);
        
    } catch (\Exception $e) {
        Log::error('Error marking all notifications as read: ' . $e->getMessage());
    }
}
 

    public function deleteNotification($notificationId)
    {
        try {
            Notification::where('id', $notificationId)
                ->where('to', Auth::id())
                ->delete();
            
            $this->notifications = collect($this->notifications)
                ->reject(fn($notification) => $notification['id'] == $notificationId)
                ->values()
                ->toArray();
            
            $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
        }
    }

public function runComment($notificationId)
{ 
    $n=Notification::where('id', $notificationId)->first();
    // $p=Pengaduan::where('id', $notificationId)->first();
    //  $this->markAsRead($notificationId);
    if($n->type==1){
        $this->comment($n->ref_id);
    }
    if($n->type==2){
                $this->comment($n->ref_id);

        $this->notify('info', 'form approve');
    }
    if($n->type==3){
        $this->notify('info', 'Notification berhasil ditandai sudah dibaca');
    }
        // $this->notify('error', 'this coment:'. $notificationId);

          
             
}

public function comment($id)
    {
        
    
        $record = Pengaduan::with(['comments.user'])->findOrFail($id);
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            // 'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $statusInfo->data_id ?? 'Menunggu Review',
            'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
            'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
        ];

        $detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;

      $this->trackingId = $id;
        $this->codePengaduan = $record->code_pengaduan;
        $this->showComment = true;
        
        if (!empty($detailData)) {
            $this->detailData = $detailData;
        }
        
        if (!empty($detailTitle)) {
            $this->detailTitle = $detailTitle;
        }

        $this->loadChatData();

        $this->uploadFile();
    }
    
     public function getJenisPelanggaran($record)
    {
        return $record->jenisPengaduan->data_id ?? 'Tidak diketahui';
    }
    
public function closeDetailModal()
    { 
        $this->showComment = false; 
        $this->detailData = [];
        $this->detailTitle = '';
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

    public function loadUploadedFiles()
    {
        if (!$this->trackingId) return;

        $this->uploadedFiles = [];
    }

    public function downloadFile($filePath, $originName)
    {
        if ($filePath && FileHelper::exists($filePath)) {
            return response()->download( storage_path('app/public/' . $filePath), $originName);
        }
        $this->dispatch('notify', ['type' => 'error', 'message' => 'File tidak ditemukan: ' . $originName, 'errMessage'=> 'patchFile:'.$filePath ]);
        return back();
    }

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



    public function closeChat()
    {
        parent::closeChat(); // Panggil parent dari HasChat
        $this->uploadedFiles = []; // Reset uploaded files spesifik untuk Tracking
    }


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

    public function getFileInfo($file)
    {
        return [
            'name' => $file['name'] ?? $file['original_name'] ?? 'Unknown File',
            'size' => $file['formatted_size'] ?? FileHelper::formatSize($file['size'] ?? 0),
            'icon' => $file['icon'] ?? FileHelper::getFileIcon(pathinfo($file['name'] ?? $file['original_name'] ?? '', PATHINFO_EXTENSION)),
            'description' => $file['description'] ?? '',
        ];
    }
    


     public function notify($type, $message, $errMessage = '')
    {
        // \dd($errMessage);
        $this->dispatch('notify', [
            'type' => $type,
            'message' => $message,
            'errMessage' => $errMessage
        ]);
    }
    public function render()
    {
        return view('livewire.modules.notification-bell');
    }
}