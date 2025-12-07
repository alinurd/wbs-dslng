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

            if (empty($this->notifications)) {
                $this->loadSampleNotifications();
            }

            $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
            $this->loadSampleNotifications();
        }
    }

    private function getNotificationType($type, $typeText)
    {
        if (!empty($typeText)) {
            $typeText = strtolower(trim($typeText));
            
            if (str_contains($typeText, 'chat') || str_contains($typeText, 'pesan') || str_contains($typeText, 'message')) {
                return 'chat';
            } elseif (str_contains($typeText, 'approval') || str_contains($typeText, 'persetujuan') || 
                     str_contains($typeText, 'approve') || str_contains($typeText, 'disetujui')) {
                return 'approval';
            } else {
                return 'other';
            }
        }
        
        return match($type) {
            1 => 'chat',
            2 => 'approval',
            default => 'other',
        };
    }

    private function getIcon($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);
        
        return match($notificationType) {
            'chat' => 'fas fa-comment-alt',
            'approval' => 'fas fa-clipboard-check',
            'other' => 'fas fa-bell',
            default => 'fas fa-bell',
        };
    }

      private function getTypeClass($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);
        
        return match($notificationType) {
            'chat' => 'bg-blue-100 text-blue-800 border-blue-300',
            'approval' => 'bg-green-100 text-green-800 border-green-300',
            'other' => 'bg-gray-100 text-gray-800 border-gray-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
 private function getBadgeColor($type, $typeText)
    {
        $notificationType = $this->getNotificationType($type, $typeText);
        
        return match($notificationType) {
            'chat' => 'blue',
            'approval' => 'green',
            'other' => 'gray',
            default => 'gray',
        };
    }

    private function loadSampleNotifications()
    {
        $this->notifications = [
            [
                'id' => 1,
                'type' => 'chat',
                'type_class' => 'bg-blue-100 text-blue-800 border-blue-300',
                'title' => 'Pesan Baru',
                'message' => 'Anda memiliki pesan baru dari Budi di grup "Project Team"',
                'time' => now()->subMinutes(5),
                'read' => false,
                'icon' => 'fas fa-comment-alt',
                'badge_color' => 'blue'
            ],
            [
                'id' => 2,
                'type' => 'approval',
                'type_class' => 'bg-green-100 text-green-800 border-green-300',
                'title' => 'Pengajuan Cuti Disetujui',
                'message' => 'Pengajuan cuti tanggal 15-17 Desember telah disetujui',
                'time' => now()->subMinutes(30),
                'read' => false,
                'icon' => 'fas fa-clipboard-check',
                'badge_color' => 'green'
            ],
            [
                'id' => 3,
                'type' => 'other',
                'type_class' => 'bg-gray-100 text-gray-800 border-gray-300',
                'title' => 'System Maintenance',
                'message' => 'System akan maintenance hari ini pukul 22:00 - 02:00',
                'time' => now()->subMinutes(15),
                'read' => false,
                'icon' => 'fas fa-tools',
                'badge_color' => 'gray'
            ],
        ];
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
            'approval' => $notifications->where('type', 'approval')->values()->toArray(),
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
            'approval' => $notifications->where('type', 'approval')->count(),
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
        
        if ($this->isOpen) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('to', Auth::id())
                ->first();
            
            if ($notification) {
                $notification->update(['is_read' => 1]);
                
                foreach ($this->notifications as &$notification) {
                    if ($notification['id'] == $notificationId) {
                        $notification['read'] = true;
                        break;
                    }
                }
                $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            }
            
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
        }
    }

    public function markAllAsRead()
    {
        try {
            Notification::where('to', Auth::id())
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
            
            foreach ($this->notifications as &$notification) {
                $notification['read'] = true;
            }
            $this->unreadCount = 0;
            
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