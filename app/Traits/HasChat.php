<?php

namespace App\Traits;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Pengaduan;
use App\Models\User;
use App\Services\ChatService;
use App\Services\EmailService;
use App\Services\PengaduanEmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HasChat
{
    // Properties untuk chat
    public $trackingId = null;
    public $type = 1;
    public $codePengaduan = null;
    public $newMessage = '';
    public $messages = [];
    public $showComment = false;
    public $chatStats = [];

    public $mentionUsers = [];
    public $mentionQuery = '';
    public $showMentionDropdown = false;
    public $mentionDropdownPosition = 0;
    public $isMentionMode = false;

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
    public function openChat($id, $detailData = [], $detailTitle = '', $codePengaduan='')
    {
        $this->trackingId = $id;
        $this->codePengaduan = $codePengaduan;
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
        // Jangan load messages jika sedang dalam mode mention
        if ($this->isMentionMode && $this->showMentionDropdown) {
            return;
        }
        
        if (!$this->trackingId) return;

        $method = $this->chatService->getRealtimeUpdateMethod();
        $this->messages = $this->chatService->$method($this->trackingId);
    }

    /**
     * Send message dengan mention
     */
    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:attachFile|string|max:1000',
            'attachFile' => 'nullable|file|max:102400|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        if (!$this->trackingId) return;

        try {
            // Extract mentions dari message
            $mentions = $this->extractMentions($this->newMessage);
            
            $messageId = null;
            
            if (env('CHAT_REALTIME', false)) {
                // Gunakan method dengan broadcast
                $result = $this->chatService->sendMessageWithBroadcast(
                    $this->trackingId, 
                    $this->newMessage, 
                    $this->attachFile
                );
                $messageId = $result['id'] ?? null;
            } else {
                // Gunakan method biasa
                dd("tes");
                $result = $this->chatService->sendMessage(
                    $this->trackingId, 
                    $this->newMessage, 
                    $this->attachFile
                );
                $messageId = $result['id'] ?? null;
            }

            // \dd($this->type);
            // Kirim notifikasi untuk mentions
            // if (!empty($mentions) && $messageId) {
                $this->sendMentionNotifications(
                    $this->newMessage, 
                    $this->trackingId, 
                    $messageId, 
                    [],
                    // $mentions,
                    $this->type
                );
            // }

            // Reset form
            $this->newMessage = '';
            $this->attachFile = null;
            $this->resetMentionDropdown();
            
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
        $listeners = [];
        
        if (env('CHAT_REALTIME', false) && $this->trackingId) {
            $listeners["echo:chat.{$this->trackingId},NewChatMessage"] = 'handleNewMessage';
        }

        // Add mention-related listeners
       $listeners = array_merge($listeners, [
            'check-mention-trigger' => 'checkForMentionTrigger',
            'reset-mention-dropdown' => 'resetMentionDropdown',
            'set-cursor-position' => 'handleSetCursorPosition',
            'focus-message-input' => 'handleFocusMessageInput',
        ]);

        return $listeners;
    }
        public function handleSetCursorPosition($data)
    { 
    }
      public function handleFocusMessageInput()
    { 
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
        $this->resetMentionDropdown();
    }

    /**
     * Get realtime polling interval berdasarkan environment
     */
    public function getPollingInterval()
    {
        return env('CHAT_REALTIME', false) ? null : '1s';
    }

    /**
     * Load mention users berdasarkan query
     */
    public function loadMentionUsers($query = '')
    {
        // Cek jika ada @ dalam text
        $text = $this->newMessage;
        $cursorPos = $this->mentionDropdownPosition;
        
        // Cari posisi @ terakhir sebelum cursor
        $lastAtPos = strrpos(substr($text, 0, $cursorPos), '@');
        
        if ($lastAtPos === false) {
            $this->resetMentionDropdown();
            return;
        }
        
        // Ambil query setelah @
        $query = substr($text, $lastAtPos + 1, $cursorPos - $lastAtPos - 1);
        
        // Hapus spasi dari query
        $query = trim($query);
        
        // Jika query kosong atau mengandung spasi, reset
        if (empty($query) || strpos($query, ' ') !== false) {
            $this->resetMentionDropdown();
            return;
        }
        
        $this->isMentionMode = true;
        $this->mentionQuery = $query;
        
        // Load users dengan query yang lebih sedikit karakter
        $users = User::where(function($q) use ($query) {
                $q->where('name', 'like', "{$query}%")
                  ->orWhere('username', 'like', "{$query}%")
                  ->orWhere('email', 'like', "{$query}%");
            })
            ->where('id', '!=', auth()->id()) 
            ->take(8)
            ->get(['id', 'name', 'username', 'email'])
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'display' => $user->name . ' (' . $user->email . ')',
                ];
            });

        $this->mentionUsers = $users->toArray();
        $this->showMentionDropdown = count($this->mentionUsers) > 0;
        
        // Dispatch event untuk update UI
        $this->dispatch('mention-dropdown-updated');
    }

    public function updatedNewMessage($value)
    {
        // Trigger check for mention
        $this->checkForMentionTrigger();
    }

    public function checkForMentionTrigger()
    {
        $text = $this->newMessage;
        $cursorPos = $this->mentionDropdownPosition;
        
        // Cari @ terakhir sebelum cursor
        $lastAtPos = strrpos(substr($text, 0, $cursorPos), '@');
        
        if ($lastAtPos !== false) {
            // Cek jika @ tidak didahului oleh karakter (spasi atau awal string)
            $charBefore = $lastAtPos > 0 ? $text[$lastAtPos - 1] : ' ';
            
            if ($charBefore === ' ' || $lastAtPos === 0) {
                // Ambil kata setelah @
                $query = substr($text, $lastAtPos + 1, $cursorPos - $lastAtPos - 1);
                
                // Jika tidak ada spasi dalam query
                if (strpos($query, ' ') === false) {
                    // Tampilkan semua user jika query kosong atau hanya 1 karakter
                    if (strlen($query) <= 1) {
                        $this->loadAllMentionableUsers();
                    } else {
                        $this->loadMentionUsers($query);
                    }
                    return;
                }
            }
        }
        
        // Jika tidak ada @ yang valid, reset
        $this->resetMentionDropdown();
    }

    /**
     * Load semua user yang bisa di-mention (saat @ diketik tanpa query)
     */
    public function loadAllMentionableUsers()
    {
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->take(10)
            ->get(['id', 'name', 'username', 'email'])
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'display' => $user->name . ' (' . $user->email . ')',
                ];
            });

        $this->mentionUsers = $users->toArray();
        $this->showMentionDropdown = count($this->mentionUsers) > 0;
        $this->isMentionMode = true;
        
        $this->dispatch('mention-dropdown-updated');
    }
    
    /**
     * Select mention user - TAMPILKAN EMAIL BUKAN USERNAME
     */
 public function selectMentionUser($userIndex)
    {
        if (isset($this->mentionUsers[$userIndex])) {
            $user = $this->mentionUsers[$userIndex];
            
            // Insert mention tag ke dalam text input dengan EMAIL
            $mentionTag = '@' . $user['email'] . ' ';
            
            // Split text berdasarkan cursor position
            $textBeforeCursor = substr($this->newMessage, 0, $this->mentionDropdownPosition);
            $textAfterCursor = substr($this->newMessage, $this->mentionDropdownPosition);
            
            // Hapus query mention dari text
            $lastAtPos = strrpos($textBeforeCursor, '@');
            if ($lastAtPos !== false) {
                $textBeforeCursor = substr($textBeforeCursor, 0, $lastAtPos);
            }
            
            // Gabungkan kembali dengan mention tag (EMAIL)
            $this->newMessage = $textBeforeCursor . $mentionTag . $textAfterCursor;
            
            // Reset mention dropdown
            $this->resetMentionDropdown();
            
            // Update cursor position ke setelah mention tag
            $newCursorPos = strlen($textBeforeCursor) + strlen($mentionTag);
            $this->dispatch('set-cursor-position', ['position' => $newCursorPos]);
            
            // Focus ke input
            $this->dispatch('focus-message-input');
        }
    }
    /**
     * Reset mention dropdown
     */
    public function resetMentionDropdown()
    {
        $this->mentionUsers = [];
        $this->mentionQuery = '';
        $this->showMentionDropdown = false;
        $this->isMentionMode = false;
        $this->mentionDropdownPosition = 0;
        
        $this->dispatch('mention-dropdown-reset');
    }

    /**
     * Extract mentions dari message - SEKARANG MENCARI EMAIL BUKAN USERNAME
     */
    protected function extractMentions($message)
    {
        // Regex untuk mencari email dalam format @email (lebih simple)
        // Mencari pattern @diikuti oleh karakter email yang valid
        preg_match_all('/@([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $message, $matches);
        
        if (empty($matches[1])) {
            // Juga coba cari format @username (fallback)
            preg_match_all('/@([a-zA-Z0-9_]+)/', $message, $usernameMatches);
            
            if (empty($usernameMatches[1])) {
                return [];
            }
            
            $usernames = array_unique($usernameMatches[1]);
            
            // Get user IDs dari usernames
            $users = User::whereIn('username', $usernames)
                ->where('id', '!=', auth()->id())
                ->get(['id', 'username', 'email'])
                ->keyBy('username');
            
            $mentions = [];
            foreach ($usernames as $username) {
                if (isset($users[$username])) {
                    $mentions[] = [
                        'user_id' => $users[$username]->id,
                        'email' => $users[$username]->email,
                        'username' => $username,
                        'mentioned_at' => now()
                    ];
                }
            }
            
            return $mentions;
        }
        
        $emails = array_unique($matches[1]);
        
        // Get user IDs dari emails
        // $users = User::whereIn('email', $emails)
        //     ->where('id', '!=', auth()->id())
        //     ->get(['id', 'email', 'username'])
        //     ->keyBy('email');

                    $users = User::where('id', '!=', auth()->id())
                    ->where('is_active', 1)
                                ->whereHas('roles', function($query) {
                                    $query->where('id', 2);
                                })
                    ->get(['id', 'email', 'username'])
                    ->keyBy('email');
        
        $mentions = [];
        foreach ($emails as $email) {
            if (isset($users[$email])) {
                $mentions[] = [
                    'user_id' => $users[$email]->id,
                    'email' => $email,
                    'username' => $users[$email]->username ?? $email,
                    'mentioned_at' => now()
                ];
            }
        }
        
        return $mentions;
    }

    /**
     * Send notification untuk mention
     */
    protected function sendMentionNotifications($message, $trackingId, $messageId, $mentions, $type=1)
    {
         $auth = auth()->user(); 
          
$role = $auth->roles->first(); 
 
$roleIds = $auth->roles->pluck('id')->toArray();


        if(in_array(3, $roleIds))  {
            $users = User::where('id', '!=', auth()->id())
                    ->where('is_active', 1)
                                ->whereHas('roles', function($query) {
                                    $query->where('id', 2);
                                })
                    ->get(['id', 'email', 'username'])
                    ->keyBy('email');
        }elseif(in_array(2, $roleIds)){
            $n= Notification::select('sender_id', 'type')->where('ref_id', $trackingId)->where('type', 4)->first();
            $cek['Notification']=$n->toarray();
            $cek['trackingId']=$trackingId;
            $senderID=0;
            if($n){
                $senderID=$n->sender_id;
            }else{
                $n= Pengaduan::select('user_id')->where('id', $trackingId)->first();

                $senderID=$n->user_id;

            }
            $cek['senderID']=$senderID;
            $cek['type']=$n->type;
             $users = User::where('id', '!=', auth()->id())
                    ->where('id', $senderID)
                                ->whereHas('roles', function($query) {
                                    $query->where('id', 3);
                                })
                    ->get(['id', 'email', 'username'])
                    ->keyBy('email');
                    
        }
        
        
           $cek['user']=$users->toarray();
           $cek['type']=$type;
           $cek['roleIds']=$roleIds;
           $cek['trackingId']=$trackingId;
                   dd($cek); 
        // foreach ($mentions as $mention) {
        foreach ($users as $mention) {
            $notificationData = [
                'sender_id' => auth()->id(),
                'ref_id' => $trackingId,
                'to' => $mention['id'],
                'type' => $type,
                'type_text' => 'chat',
                'is_read' => 0,
                'title' => 'Anda disebutkan dalam chat',
                'message' => auth()->user()->name . ' menyebutkan Anda dalam chat  #' . $this->codePengaduan,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Insert ke notifications table
            DB::table('notifications')->insert($notificationData);
            //  dd($mention);
            $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$formattedMessage = nl2br($safeMessage);

$content = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h3 style='color: #333; margin-bottom: 20px;'>Halo {$mention['username']}</h3>
        
        <p style='color: #666; font-size: 16px; line-height: 1.5;'>
            Anda disebutkan dalam chat oleh <strong style='color: #007bff;'>" . auth()->user()->username . "</strong>.
        </p>
        
        <div style='margin: 25px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;'>
            <h4 style='color: #495057; margin-top: 0; margin-bottom: 15px;'>📨 Pesan:</h4>
            <div style='color: #333; font-size: 15px; line-height: 1.6; padding: 10px; background: white; border-radius: 5px;'>
                {$formattedMessage}
            </div>
        </div>
        
        <div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; 
                   padding: 15px; margin: 20px 0;'>
            <p style='margin: 0; color: #856404; font-size: 14px;'>
                <strong>📢 Tindakan yang diperlukan:</strong><br>
                Silakan masuk ke aplikasi dan cek notifikasi di <strong>tombol lonceng</strong> 
                untuk merespon atau melihat percakapan. Pastikan Anda sudah login.
            </p>
        </div>
        
        
    </div>
";
        
            $emailService = new EmailService();
             $emailService->sendNotificationEmail(
                $mention['email'],
                ' Chat Notification',
                $content,
                'info'
            );
            // Dispatch event untuk realtime notification
            if (env('CHAT_REALTIME', false)) {
                $this->dispatch('new-notification', [
                    'user_id' => $mention['id'],
                    'notification' => $notificationData
                ]);
            }
        }
    }
} 