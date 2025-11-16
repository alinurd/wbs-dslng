<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;


class ChatService
{
    /**
     * Send message dengan atau tanpa attachment
     */
    public function sendMessage($pengaduanId, $message, $file = null, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $fileData = null;
        
        // Upload file jika ada
        if ($file) {
            $uploadedFile = FileHelper::upload($file, 'pengaduan/chat', 'public');
            
            $fileData = [
                'path' => $uploadedFile['path'],
                'original_name' => $uploadedFile['original_name'],
                'size' => $uploadedFile['size'],
                'type' => $uploadedFile['mime_type'],
            ];
        }

        // Create comment
        $comment = Comment::create([
            'pengaduan_id' => $pengaduanId,
            'user_id' => $userId,
            'message' => $message,
            'file_data' => $fileData ? json_encode($fileData) : null,
        ]);

        // Load relation untuk response
        $comment->load('user');

        return $comment;
    }

    /**
     * Load messages untuk chat
     */
    public function loadMessages($pengaduanId)
    {
        $chatMessages = Comment::where('pengaduan_id', $pengaduanId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return $chatMessages->map(function ($message) {
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
                'is_own' => $message->user_id === Auth::id(),
                'time' => $message->created_at->format('H:i'),
                'date' => $message->created_at->format('d M Y'),
                'avatar' => $message->user->profile_photo_url ?? null,
                'file' => $fileData,
            ];
        })->toArray();
    }

    /**
     * Get real-time update method berdasarkan environment
     */
    public function getRealtimeUpdateMethod()
    {
        if (env('CHAT_REALTIME', false)) {
            return 'loadMessagesRealtime'; // Untuk websocket/pusher
        } else {
            return 'loadMessagesPolling'; // Untuk polling tradisional
        }
    }

    /**
     * Load messages dengan polling (fallback)
     */
    public function loadMessagesPolling($pengaduanId)
    {
        return $this->loadMessages($pengaduanId);
    }

    /**
     * Load messages real-time (untuk websocket)
     */
    public function loadMessagesRealtime($pengaduanId)
    {
        // TODO: Implement websocket/pusher integration
        // Untuk sekarang gunakan polling sebagai fallback
        return $this->loadMessages($pengaduanId);
    }

    /**
     * Download file dari chat message
     */
    public function downloadMessageFile($messageId)
    {
        $message = Comment::find($messageId);
        
        if ($message && $message->file_data) {
            $fileData = json_decode($message->file_data, true);
            
            if ($fileData && FileHelper::exists($fileData['path'])) {
                return response()->download(
                    storage_path('app/public/' . $fileData['path']),
                    $fileData['original_name']
                );
            }
        }

        return false;
    }

    /**
     * Get chat statistics
     */
    public function getChatStats($pengaduanId)
    {
        $totalMessages = Comment::where('pengaduan_id', $pengaduanId)->count();
        $totalFiles = Comment::where('pengaduan_id', $pengaduanId)
            ->whereNotNull('file_data')
            ->count();
        $lastActivity = Comment::where('pengaduan_id', $pengaduanId)
            ->latest()
            ->first();

        return [
            'total_messages' => $totalMessages,
            'total_files' => $totalFiles,
            'last_activity' => $lastActivity ? $lastActivity->created_at->diffForHumans() : 'No activity',
            'participants' => $this->getParticipants($pengaduanId),
        ];
    }

    /**
     * Get participants in chat
     */
    private function getParticipants($pengaduanId)
    {
        return Comment::where('pengaduan_id', $pengaduanId)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->values();
    }

     /**
     * Broadcast new message ke channel
     */
    public function broadcastMessage($comment)
    {
        if (!env('CHAT_REALTIME', false)) {
            return;
        }

        try {
            Broadcast::channel('chat.' . $comment->pengaduan_id, function ($user) use ($comment) {
                return [
                    'id' => $user->id,
                    'name' => $user->name
                ];
            });

            // Trigger event untuk realtime update
            broadcast(new \App\Events\NewChatMessage($comment));
            
        } catch (\Exception $e) {
            \Log::error('Broadcast error: ' . $e->getMessage());
        }
    }

    public function sendMessageWithBroadcast($pengaduanId, $message, $file = null, $userId = null)
    {
        $comment = $this->sendMessage($pengaduanId, $message, $file, $userId);
        
        // Broadcast message jika realtime aktif
        if (env('CHAT_REALTIME', false)) {
            $this->broadcastMessage($comment);
        }

        return $comment;
    }
}

