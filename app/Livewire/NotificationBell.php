<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class NotificationBell extends Component
{
    public $isOpen = false;
    public $unreadCount = 0;
    public $notifications = [];
    public $activeFilter = 'all';

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

    public function render()
    {
        return view('livewire.modules.notification-bell');
    }
}