<?php

namespace App\Livewire;

use App\Helpers\FileHelper;
use App\Livewire\Root;
use App\Models\Audit as AuditLog;
use App\Models\Combo;
use App\Models\Comment;
use App\Models\LogApproval;
use App\Models\Notification;
use App\Models\Pengaduan;
use App\Services\PengaduanEmailService;
use App\Traits\HasChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class NotificationBell extends Root
{
    public $isOpen = false;
    public $showuUdateStatus = false;
    public $unreadCount = 0;
    public $notifications = [];
    public $activeFilter = 'all';
    public $detailData = [];
    public $detailTitle = '';
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];
    public $catatan = '';
    public $submission_action = '';

    use WithFileUploads, HasChat;

    public function mount()
    {
        $this->loadNotifications();
        $this->loadDropdownData();
        $this->userInfo();
    }


    protected function rules()
    {
        return [

            'catatan' => 'required|min:10',
            'lampiran.*' => 'max:' . (FileHelper::getMaxPengaduanSize() * 1024) . '|mimes:' . implode(',', FileHelper::getAllowedPengaduanExtensions()),

        ];
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
            } elseif (
                str_contains($typeText, 'complien') || str_contains($typeText, 'persetujuan') ||
                str_contains($typeText, 'approve') || str_contains($typeText, 'disetujui')
            ) {
                return 'complien';
            } else {
                return 'other';
            }
        }

        return match ($type) {
            1 => 'chat',
            2 => 'complien',
            default => 'other',
        };
    }

    private function getIcon($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);

        return match ($notificationType) {
            'chat' => 'fas fa-comment-alt',
            'complien' => 'fas fa-clipboard-check',
            'other' => 'fas fa-bell',
            default => 'fas fa-bell',
        };
    }

    private function getTypeClass($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);

        return match ($notificationType) {
            'chat' => 'bg-blue-100 text-blue-800 border-blue-300',
            'complien' => 'bg-green-100 text-green-800 border-green-300',
            'other' => 'bg-gray-100 text-gray-800 border-gray-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
    private function getBadgeColor($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);

        return match ($notificationType) {
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

        return match ($this->activeFilter) {
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
        $n = Notification::where('id', $notificationId)->first();
        if (!is_numeric($n->ref_id)) {
            $p = Pengaduan::where('code_pengaduan', $n->ref_id)->first();
            $n->ref_id = $p->id;
        }
        $this->markAsRead($notificationId);
        if ($n->type == 1) {
            $this->comment($n->ref_id);
        }
        if ($n->type == 2) {
            $roleId = (int)($this->userInfo['role']['id'] ?? 0);
            if ($roleId == 3) {
                $this->viewPengaduan($n->ref_id);
            } else {
                $this->updateStatus($n->ref_id);
            }
        }
        if ($n->type == 3) {
            $this->notify('info', 'Notification berhasil ditandai sudah dibaca');
        }
    }
    public function setAction($action, $id = null)
    {
        $this->submission_action = $action;
        if ($id) {
            $this->selected_pengaduan_id = $id;
            $this->pengaduan_id = $id;
        }
    }

    public function submitForm()
    {
        try {
            // Debug: cek data masuk
            \Log::info('submitForm called', [
                'submission_action' => $this->submission_action,
                'selected_pengaduan_id' => $this->selected_pengaduan_id,
                'has_catatan' => !empty($this->catatan),
                'has_file' => !empty($this->lampiran)
            ]);

            // Validasi form
            $this->validate();

            // Validasi action dan pengaduan_id
            if (empty($this->submission_action)) {
                $this->notify('error', 'Silakan pilih action terlebih dahulu!');
                return;
            }

            if (empty($this->selected_pengaduan_id)) {
                $this->notify('error', 'Tidak ada pengaduan yang dipilih!');
                return;
            }

            $filePath = [];
            if ($this->lampiran && count($this->lampiran) > 0) {
                $filePath = FileHelper::uploadMultiple(
                    $this->lampiran,
                    'pengaduan/lampiran',
                    'public'
                );
            }


            // Update pengaduan
            $pengaduan = Pengaduan::with('pelapor')->find($this->selected_pengaduan_id);

            if ($pengaduan) {
                $dataOld = [
                    'status' => $pengaduan->status,
                    'fwd_to' => $pengaduan->fwd_to,
                    'sts_fwd' => $pengaduan->sts_fwd,
                    'sts_final' => $pengaduan->sts_final,
                    'updated_at' => $pengaduan->updated_at,
                ];

                $statusInfo = Combo::where('kelompok', 'sts-aduan')
                    ->where('param_int', $this->submission_action)
                    ->first();

                \Log::info('Status info found', [
                    'statusInfo' => $statusInfo,
                    'submission_action' => $this->submission_action
                ]);

                if ($statusInfo) {
                    $updateData = [
                        'status' => $this->submission_action,
                        'fwd_to' => ($this->submission_action == 5 && $this->forwardDestination != 0 && !$pengaduan['fwd_to'])
                            ? $this->forwardDestination
                            : $pengaduan['fwd_to'],
                        'sts_fwd' => ($this->submission_action == 2 && $this->forwardDestination !== 0)
                            ? 1
                            : (($this->submission_action == 5) ? 0 : $pengaduan['sts_fwd']),

                        'sts_final' => in_array($this->submission_action, [3, 6, 7]) ? 1 : 0,
                        'updated_at' => now(),
                    ];

                    $roleId = (int)($this->userInfo['role']['id'] ?? 0);
                    if ($roleId == 5) {
                        $updateData['act_cc'] = 1;
                    }
                    if ($roleId == 7) {
                        $updateData['act_cco'] = 1;
                    }
                    // Add catatan if provided
                    if (!empty($this->catatan)) {
                        $updateData['catatan'] = $this->catatan;
                    }

                    \Log::info('Updating pengaduan', [
                        'pengaduan_id' => $pengaduan->id,
                        'updateData' => $updateData
                    ]);

                    $pengaduan->update($updateData);

                    AuditLog::create([
                        'user_id' => $this->userInfo['user']['id'],
                        'action' => 'updStatus',
                        'table_name' => 'Complien',
                        'record_id' => $pengaduan->id,
                        'old_values' => json_encode($dataOld),
                        'new_values' =>  json_encode($updateData),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'created_at' => now()
                    ]);

                    // dd($pengaduan);
                    $emailService = new PengaduanEmailService();
                    $emailService->handleStatusChange(
                        $pengaduan,                    // Object pengaduan
                        $this->submission_action,      // Status action (6, 10, 7, dll)
                        $roleId,                       // Role ID user yang melakukan aksi
                        $this->catatan,                // Catatan (opsional)
                        ($this->forwardDestination ?? 0),      // Forward destination (opsional),
                        auth()->id() //user yang melakukan action
                    );
                    // Create log approval
                    $this->createLogApproval($pengaduan, $statusInfo, $filePath);

                    \Log::info('Pengaduan updated successfully');
                } else {
                    \Log::error('Status info not found for action: ' . $this->submission_action);
                    $this->notify('error', 'Status tidak valid: ' . $this->submission_action);
                    return;
                }
            } else {
                \Log::error('Pengaduan not found: ' . $this->selected_pengaduan_id);
                $this->notify('error', 'Pengaduan tidak ditemukan!');
                return;
            }

            $this->notify('success', 'Status pengaduan berhasil diupdate!');
            $this->showuUdateStatus = false;

            // Reset semua form dan forward dropdown
            $this->resetForm();
            $this->hideForwardDropdown();
        } catch (\Exception $e) {
            \Log::error('Error in submitForm: ' . $e->getMessage());
            $this->notify('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    protected function createLogApproval($pengaduan, $statusInfo, $filePath = null)
    {
        try {
            $logData = [
                'pengaduan_id' => $pengaduan->id,
                'user_id' => auth()->id(),
                'status_id' => $statusInfo->param_int,
                'status_text' => $statusInfo->data_id,
                'status' => $statusInfo->data_en,
                'catatan' => $this->catatan ?? '',
                'file' => json_encode($filePath),
                'color' => $statusInfo->param_str ?? 'gray',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            LogApproval::create($logData);
        } catch (\Exception $e) {
            \Log::error('Error creating log approval: ' . $e->getMessage());
            throw $e;
        }
    }


    public function hideForwardDropdown()
    {
        $this->showForwardDropdown = false;
        $this->forwardDestination = '';
        $this->forwardPengaduanId = '';
    }

    public function updateStatus($id, $status = null)
    {
        $record = Pengaduan::with(['jenisPengaduan'])->orderBy('created_at', 'desc')->findOrFail($id);

        $this->hideForwardDropdown();

        $logHistory = $this->getLogHistory($id);
        $currentStatusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $act_int = ($record->act_cco) == 1 ? false : true;
        // $act_int = ($record->act_cc || $record->act_cco) == 1 ? false : true;
        $this->detailData = [
            'id' => $id,
            'Kode Tracking' => $record->code_pengaduan,
            // 'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status Saat Ini' => $currentStatusInfo->data_id ?? 'Menunggu Review',
            'status_ex' => [
                'name' => $currentStatusInfo->data_id ?? 'Menunggu Review',
                'color' => $currentStatusInfo->param_str ?? 'yellow',
            ],
            'status_id' => $record->status,
            'act_cc' => $record->act_cc,
            // 'act_int' => $act_int,
            'act_int' => $act_int,
            'act_cco' => $record->act_cco,
            'sts_fwd' => [
                'id' => $record->sts_fwd,
                'data' => $this->getStatusInfo(2, 0)
            ],
            'user' => $this->userInfo,
            'log' => [
                [
                    'id' => $record->id,
                    'code' => $record->code_pengaduan,
                    'jenis_pengaduan' => $record->jenisPengaduan->data_id ?? 'Tidak diketahui',
                    'status_akhir' => $currentStatusInfo->data_id ?? 'Menunggu Review',
                    'progress' => $this->calculateProgress($record),
                    'log_approval' => $logHistory,


                ]
            ],
        ];

        $this->detailTitle = "Update Status - " . $record->code_pengaduan;
        $this->showuUdateStatus = true;
        $this->uploadFile();
    }


    protected function getLogHistory($pengaduanId)
    {
        $logs = LogApproval::with('user')
            ->where('pengaduan_id', $pengaduanId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($logs->isEmpty()) {
            return [
                [
                    'pengaduan_id' => $pengaduanId,
                    'role' => 'Pelapor',
                    'step' => 1,
                    'user_name' => $this->getNamaUser(Pengaduan::find($pengaduanId)),
                    'status' => 'new',
                    'status_text' => 'Dilaporkan',
                    'waktu' => now()->subDays(2)->format('d/m/Y H:i'),
                    'catatan' => 'Laporan awal telah disampaikan',
                    'file' => [],
                    'warna' => 'gray',
                    'infoSts' => $this->getStatusInfo(0, 0),
                    'status_color' => 'gray',
                ]
            ];
        }

        return $logs->map(function ($item, $index) {
            $catatan = $item->catatan ?: 'Tidak ada catatan';

            $truncatedCatatan = strlen($catatan) > 60
                ? substr($catatan, 0, 60) . '...'
                : $catatan;
            return [
                'id' => $item->id,
                'pengaduan_id' => $item->pengaduan_id,
                'code' => '#' . ($item->pengaduan->code_pengaduan ?? $item->pengaduan_id),
                'waktu' => $this->getTimeAgo($item->created_at),
                'catatan' => $truncatedCatatan,
                'catatan_full' => $catatan,
                'file' => $item->file ?? json_decode($item->file, true) ?? [],
                'status_color' => $item->color ?? 'blue',
                'user_name' => $item->user->name ?? 'Unknown',
                'role' => $item->user->getRoleNames()->first() ?? 'Unknown',
                'status' => $item->status_text,
                'infoSts' => $this->getStatusInfo($item->status_id, 0)
            ];
        })->toArray();
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
        $this->showDetailModal = false;
        $this->showuUdateStatus = false;
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
            return response()->download(storage_path('app/public/' . $filePath), $originName);
        }
        $this->dispatch('notify', ['type' => 'error', 'message' => 'File tidak ditemukan: ' . $originName, 'errMessage' => 'patchFile:' . $filePath]);
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



    public function viewPengaduan($id)
    {
        $this->getPengaduanById($id);
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
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
