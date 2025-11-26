<?php 
namespace App\Services;

use App\Models\Audit as AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    private $userId;

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Test koneksi email sederhana
     */
    public function testEmailConnection()
    {
        try {
            Mail::raw('Test connection email', function ($message) {
                $message->to('test@example.com')
                        ->subject('Test Connection');
            });
            
            return ['status' => true, 'message' => 'Koneksi email berhasil'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send email dengan template yang dinamis
     */
    public function sendEmail(string $to, string $subject, string $view, array $data = [], array $attachments = [], string $purpose = ''): bool
    {
        $status = false;
        $error = '';

        // Check jika view exists
        if (!view()->exists($view)) {
            $error = "View {$view} tidak ditemukan";
            Log::error($error);
            $this->createAuditLog($to, $subject, $purpose, false, $error);
            return false;
        }

        try {
            

            Mail::send($view, $data, function ($message) use ($to, $subject, $attachments) {
                $message->to($to)
                        ->subject($subject);
                
                $message->from(
                    config('mail.from.address'), 
                    config('mail.from.name')
                );
                
                foreach ($attachments as $attachment) {
                    if (isset($attachment['path'])) {
                        $message->attach($attachment['path'], $attachment['options'] ?? []);
                    }
                }
            });

            $status = true;
           
            
        } catch (\Exception $e) {
            $error = $e->getMessage();
             
        }

        $this->createAuditLog($to, $subject, $purpose, $status, $error);
        return $status;
    }

    /**
     * Create audit log untuk email
     */
    private function createAuditLog(string $to, string $subject, string $purpose, bool $status, string $error = '')
    {
        try {
            AuditLog::create([
                'user_id' => $this->userId ?? auth()->id() ?? null,
                'action' => 'send-email',
                'table_name' => 'email_logs',
                'record_id' => null,
                'old_values' => null,
                'new_values' => json_encode([
                    'recipient' => $to,
                    'subject' => $subject,
                    'purpose' => $purpose,
                    'status' => $status ? 'success' : 'failed',
                    'error_message' => $error,
                    'sent_at' => now()->toDateTimeString(),
                    'mail_host' => config('mail.mailers.smtp.host')
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal membuat audit log untuk email', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim email verifikasi
     */
    public function sendVerificationEmail(string $to, string $verificationCode, string $userName = ''): bool
    {
        $subject = 'Kode Verifikasi Akun - The Gallery Villa';
        $view = 'emails.verification';
        $purpose = 'account_verification';
        
        $data = [
            'verificationCode' => $verificationCode,
            'userName' => $userName,
            'expiresIn' => 30
        ];
        
        return $this->sendEmail($to, $subject, $view, $data, [], $purpose);
    }

    /**
     * Kirim email welcome
     */
    public function sendWelcomeEmail(string $to, string $userName = ''): bool
    {
        $subject = 'Selamat Datang di The Gallery Villa';
        $view = 'emails.welcome';
        $purpose = 'welcome_email';
        
        $data = [
            'userName' => $userName,
        ];
        
        return $this->sendEmail($to, $subject, $view, $data, [], $purpose);
    }

    /**
     * Kirim email reset password
     */
    public function sendPasswordResetEmail(string $to, string $resetLink, string $userName = ''): bool
    {
        $subject = 'Reset Password - The Gallery Villa';
        $view = 'emails.password-reset';
        $purpose = 'password_reset';
        
        $data = [
            'resetLink' => $resetLink,
            'userName' => $userName,
            'expiresIn' => 60
        ];
        
        return $this->sendEmail($to, $subject, $view, $data, [], $purpose);
    }

    /**
     * Kirim email notifikasi umum
     */
    public function sendNotificationEmail(string $to, string $title, string $message, string $type = 'info'): bool
    {
        $subject = $title;
        $view = 'emails.notification';
        $purpose = 'general_notification';
        
        $data = [
            'title' => $title,
            'message' => $message,
            'type' => $type
        ];
        
        return $this->sendEmail($to, $subject, $view, $data, [], $purpose);
    }

    /**
     * Kirim email dengan template custom
     */
    public function sendCustomEmail(string $to, string $subject, array $data, string $templateType = 'general', string $purpose = 'custom_email'): bool
    {
        $view = "emails.{$templateType}";
        return $this->sendEmail($to, $subject, $view, $data, [], $purpose);
    }
}