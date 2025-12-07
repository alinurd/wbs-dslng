<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Kirim notifikasi ke role tertentu
     */
    public static function sendToRole(int $roleId, string $title, string $message, ?int $senderId = null): bool
    {
        try {
            $senderId = $senderId ?? auth()->id();
            
            // Ambil semua user dengan role ini yang aktif
            $users = User::whereHas('roles', function($q) use ($roleId) {
                $q->where('id', $roleId)->where('is_active', 1);
            })->get(['id']);
            
            if ($users->isEmpty()) {
                return false;
            }
            
            $notifications = [];
            $now = now();
            
            foreach ($users as $user) {
                // Skip jika sender = receiver
                if ($user->id == $senderId) continue;
                
                $notifications[] = [
                    'sender_id' => $senderId,
                    'to' => $user->id,
                    'type' => 2, // approval
                    'type_text' => 'approval',
                    'is_read' => 0,
                    'title' => $title,
                    'message' => substr($message, 0, 97) . (strlen($message) > 97 ? '...' : ''),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            
            if (!empty($notifications)) {
                DB::table('notifications')->insert($notifications);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Notification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kirim notifikasi ke user tertentu
     */
    public static function sendToUser(int $userId, string $title, string $message, ?int $senderId = null): bool
    {
        try {
            $senderId = $senderId ?? auth()->id();
            
            // Skip jika sender = receiver
            if ($userId == $senderId) return false;
            
            DB::table('notifications')->insert([
                'sender_id' => $senderId,
                'to' => $userId,
                'type' => 1, // chat
                'type_text' => 'chat',
                'is_read' => 0,
                'title' => $title,
                'message' => substr($message, 0, 97) . (strlen($message) > 97 ? '...' : ''),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Notification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kirim notifikasi ke role 2 (approval team)
     */
    public static function notifyApprovers(string $title, string $message, ?int $senderId = null): bool
    {
        return self::sendToRole(2, $title, $message, $senderId);
    }
}