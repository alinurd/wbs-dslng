<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class NotificationBell extends Component
{
    public $isOpen = false;
    public $unreadCount = 0;
    public $notifications = [];

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        try {
            $this->notifications = [
                // NOTIFIKASI BARU (UNREAD)
                [
                    'id' => 1,
                    'type' => 'info',
                    'title' => 'System Maintenance',
                    'message' => 'System will undergo maintenance tonight from 10 PM to 2 AM',
                    'time' => now()->subMinutes(15),
                    'read' => false,
                    'icon' => 'fas fa-tools'
                ],
                [
                    'id' => 2,
                    'type' => 'success',
                    'title' => 'Report Approved',
                    'message' => 'Your monthly financial report has been approved by manager',
                    'time' => now()->subMinutes(45),
                    'read' => false,
                    'icon' => 'fas fa-check-circle'
                ],
                [
                    'id' => 3,
                    'type' => 'warning',
                    'title' => 'Deadline Approaching',
                    'message' => 'Project "Q3 Analysis" deadline in 2 days',
                    'time' => now()->subHours(1),
                    'read' => false,
                    'icon' => 'fas fa-exclamation-triangle'
                ],

                // NOTIFIKASI SUDAH DIBACA (READ)
                [
                    'id' => 4,
                    'type' => 'success',
                    'title' => 'Payment Received',
                    'message' => 'Payment of $1,500.00 has been received from Client ABC',
                    'time' => now()->subDays(1),
                    'read' => true,
                    'icon' => 'fas fa-dollar-sign'
                ],
                [
                    'id' => 5,
                    'type' => 'info',
                    'title' => 'Team Meeting',
                    'message' => 'Weekly team meeting scheduled for tomorrow at 10:00 AM',
                    'time' => now()->subDays(1),
                    'read' => true,
                    'icon' => 'fas fa-users'
                ],
            ];

            $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            
            Log::info('Notifications loaded', [
                'total' => count($this->notifications),
                'unread' => $this->unreadCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
        }
    }

    public function toggleNotifications()
    {
        $this->isOpen = !$this->isOpen;
        Log::info('Toggle notifications', ['isOpen' => $this->isOpen]);
        
        if ($this->isOpen) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        try {
            foreach ($this->notifications as &$notification) {
                if ($notification['id'] == $notificationId) {
                    $notification['read'] = true;
                    break;
                }
            }
            $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            
            Log::info('Notification marked as read', ['id' => $notificationId]);
            
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
        }
    }

    public function markAllAsRead()
    {
        try {
            foreach ($this->notifications as &$notification) {
                $notification['read'] = true;
            }
            $this->unreadCount = 0;
            
            Log::info('All notifications marked as read');
            
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
        }
    }

    public function deleteNotification($notificationId)
    {
        try {
            $this->notifications = collect($this->notifications)
                ->reject(fn($notification) => $notification['id'] == $notificationId)
                ->values()
                ->toArray();
            
            $this->unreadCount = collect($this->notifications)->where('read', false)->count();
            
            Log::info('Notification deleted', ['id' => $notificationId]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
        }
    }

    public function getUnreadNotificationsProperty()
    {
        return collect($this->notifications)->where('read', false)->values();
    }

    public function getReadNotificationsProperty()
    {
        return collect($this->notifications)->where('read', true)->values();
    }

    public function render()
    {
        return view('livewire.modules.notification-bell');
    }
}