<?php

namespace App\Traits;

use App\Services\ChatService;

trait HasChat
{
    // Properties untuk chat
    public $trackingId = null;
    public $newMessage = '';
    public $messages = [];
    public $showComment = false;
    public $chatStats = [];

    // Properties untuk file upload di chat
    public $attachFile = null;

    protected $chatService;

    public function initializeHasChat()
    {
        $this->chatService = new ChatService();
    }

    /**
     * Open chat modal
     */
    public function openChat($id, $detailData = [], $detailTitle = '')
    {
        $this->trackingId = $id;
        $this->showComment = true;
        
        if (!empty($detailData)) {
            $this->detailData = $detailData;
        }
        
        if (!empty($detailTitle)) {
            $this->detailTitle = $detailTitle;
        }

        $this->loadChatData();
    }

    /**
     * Load semua data chat
     */
    public function loadChatData()
    {
        if (!$this->trackingId) return;

        $this->loadMessages();
        $this->loadChatStats();
    }

    /**
     * Load messages dengan method yang sesuai (realtime/polling)
     */
    public function loadMessages()
    {
        if (!$this->trackingId) return;

        $method = $this->chatService->getRealtimeUpdateMethod();
        $this->messages = $this->chatService->$method($this->trackingId);
    }

    /**
     * Send message
     */
  public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:attachFile|string|max:1000',
            'attachFile' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,rar',
        ]);

        if (!$this->trackingId) return;

        try {
            if (env('CHAT_REALTIME', false)) {
                // Gunakan method dengan broadcast
                $this->chatService->sendMessageWithBroadcast(
                    $this->trackingId, 
                    $this->newMessage, 
                    $this->attachFile
                );
            } else {
                // Gunakan method biasa
                $this->chatService->sendMessage(
                    $this->trackingId, 
                    $this->newMessage, 
                    $this->attachFile
                );
            }

            // Reset form
            $this->newMessage = '';
            $this->attachFile = null;
            
            // Untuk realtime, tidak perlu reload manual
            if (!env('CHAT_REALTIME', false)) {
                $this->loadMessages();
            }

            $this->loadChatStats();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ]);
        }
    }

    public function initializeRealtime()
    {
        if (env('CHAT_REALTIME', false)) {
            $this->initializeEcho();
        }
    }
     protected function initializeEcho()
    {
        $this->dispatch('initialize-echo');
    }

      public function getListeners()
    {
        if (env('CHAT_REALTIME', false)) {
            return [
                "echo:chat.{$this->trackingId},NewChatMessage" => 'handleNewMessage',
            ];
        }

        return [];
    }

    public function handleNewMessage($event)
    {
        // Tambahkan message baru ke list
        $this->messages[] = [
            'id' => $event['id'],
            'message' => $event['message'],
            'sender' => $event['sender'],
            'is_own' => false, // Karena message dari user lain
            'time' => $event['time'],
            'date' => now()->format('d M Y'),
            'file' => $event['file'],
        ];

        // Scroll ke bottom
        $this->dispatch('scroll-to-bottom');
    }
    /**
     * Load chat statistics
     */
    public function loadChatStats()
    {
        if (!$this->trackingId) return;

        $this->chatStats = $this->chatService->getChatStats($this->trackingId);
    }

    /**
     * Download file dari chat message
     */
    public function downloadMessageFile($messageId)
    {
        $response = $this->chatService->downloadMessageFile($messageId);
        
        if (!$response) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'File tidak ditemukan'
            ]);
        }

        return $response;
    }

    /**
     * Reset file attachment
     */
    public function resetFileAttachment()
    {
        $this->attachFile = null;
    }

    /**
     * Close chat modal
     */
    public function closeChat()
    {
        $this->showComment = false;
        $this->trackingId = null;
        $this->newMessage = '';
        $this->messages = [];
        $this->attachFile = null;
        $this->chatStats = [];
    }

    /**
     * Get realtime polling interval berdasarkan environment
     */
    public function getPollingInterval()
    {
        return env('CHAT_REALTIME', false) ? null : '1s';
    }
}