<?php 
namespace App\Services;

use App\Models\Audit as AuditLog;
use App\Models\EmailConfig;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    private $userId;
    private $emailConfig;

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

 
    
    // File: app/Services/EmailService.php
 public function sendTestEmailWithConfig($toEmail, $subject, array $config)
    {
        Log::info('Testing email config DSLNG with Laravel Mail', [
            'to' => $toEmail,
            'host' => $config['host'],
            'port' => $config['port'],
            'config_id' => $config['id'] ?? 'unknown'
        ]);

        try {

            // SET CONFIG CUSTOM untuk DSLNG Exchange
            $this->setDSLNGMailConfig($config);
            
              $view = 'emails.test-connection';
            $data = [
                'subject' => $subject,
                'testTime' => now()->format('d/m/Y H:i:s'),
                'recipient' => $toEmail,
                'config' => $config
            ];

            // Kirim email menggunakan Laravel Mail
            Mail::send($view, $data, function ($message) use ($toEmail, $subject, $config) {
                $message->to($toEmail)
                        ->subject($subject)
                        ->from(
                            $config['from_address'] ?? 'wbs@dslng.com',
                            $config['from_name'] ?? 'WBS System'
                        );
            });

            // Kembalikan config asli
            // $this->restoreMailConfig($originalConfig);

            Log::info('Email test berhasil dikirim via Laravel Mail', [
                'to' => $toEmail,
                'from' => $config['from_address'],
                'host' => $config['host']
            ]);

            return true;
            
        } catch (\Exception $e) {
            Log::error('Laravel Mail Error: ' . $e->getMessage(), [
                'to' => $toEmail,
                'config' => $config,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Set custom mail config for DSLNG Exchange
     * - NO AUTHENTICATION untuk port 25
     */
    private function setDSLNGMailConfig(array $config)
    {
        // Gunakan PHPMailer sebagai transport dengan setting khusus
        $mailer = new PHPMailer(true);
        
        // Setup untuk DSLNG Exchange (tanpa auth)
        $mailer->isSMTP();
        $mailer->Host = $config['host'];
        $mailer->Port = (int)$config['port'];
        $mailer->Timeout = 30;
        $mailer->SMTPAuth = false; // NO AUTHENTICATION
        $mailer->SMTPSecure = false; // NO encryption
        $mailer->SMTPAutoTLS = false; // NO auto TLS
        
        // SSL options
        $mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Set Laravel mail config
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $config['host'],
            'port' => $config['port'],
            'encryption' => null, // No encryption
            'username' => null, // No auth
            'password' => null, // No auth
            'timeout' => 30,
            'auth_mode' => null,
            'verify_peer' => false,
        ]);

        // Set from address
        Config::set('mail.from', [
            'address' => $config['from_address'] ?? 'wbs@dslng.com',
            'name' => $config['from_name'] ?? 'WBS System'
        ]);

        Log::debug('DSLNG Mail config set', [
            'host' => $config['host'],
            'port' => $config['port'],
            'auth' => 'disabled'
        ]);
    }


private function setCustomConfig(array $config)
{
    Config::set('mail.default', 'smtp'); // pastikan default mailer smtp
    Config::set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => $config['host'],
        'port' => $config['port'],      // 25
        'encryption' => null,            // PENTING: null untuk plain SMTP
        'username' => $config['username'],
        'password' => $config['password'],
        'timeout' => 30,
    ]);

    Config::set('mail.from.address', $config['from_address']);
    Config::set('mail.from.name', $config['from_name']);

    // Reset mailer
    app()->forgetInstance('mail.manager');
    app()->forgetInstance('mailer');
}



    // private function setCustomConfig(array $config)
    // {
    //     Config::set('mail.default', $config['mailer'] ?? 'smtp');
    //     Config::set('mail.mailers.smtp.host', $config['host']);
    //     Config::set('mail.mailers.smtp.port', $config['port']);
    //     Config::set('mail.mailers.smtp.encryption', $config['encryption']);
    //     Config::set('mail.mailers.smtp.username', $config['username']);
    //     Config::set('mail.mailers.smtp.password', $config['password']);
    //     Config::set('mail.from.address', $config['from_address']);
    //     Config::set('mail.from.name', $config['from_name']);

    //     app()->forgetInstance('mail.manager');
    // }

 



    
      private function getEmailConfig()
    {
        if ($this->emailConfig) {
            return $this->emailConfig;
        }

        try {
            $this->emailConfig = EmailConfig::where('active', true)->first();
            
            if (!$this->emailConfig) {
                throw new \Exception('Tidak ada konfigurasi email aktif yang ditemukan');
            }
            
            return $this->emailConfig;
        } catch (\Exception $e) {
            Log::error('Gagal mengambil konfigurasi email dari database: ' . $e->getMessage());
            throw $e;
        }
    }
      private function setMailConfig()
    {
        try {
            $config = $this->getEmailConfig();
            
            // Set konfigurasi mail
            Config::set('mail.default', $config->mailer);
            Config::set('mail.mailers.smtp.host', $config->host);
            Config::set('mail.mailers.smtp.port', $config->port);
            Config::set('mail.mailers.smtp.encryption', $config->encryption);
            Config::set('mail.mailers.smtp.username', $config->username);
            Config::set('mail.mailers.smtp.password', $config->password);
            Config::set('mail.from.address', $config->from_address);
            Config::set('mail.from.name', $config->from_name);

            // Reset mail instance untuk memastikan konfigurasi baru diterapkan
            app()->forgetInstance('mail.manager');
            
        } catch (\Exception $e) {
            Log::error('Gagal mengatur konfigurasi email: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Test koneksi email sederhana
     */
    public function testEmailConnection()
    {
                    $this->setMailConfig();

        try {
            Mail::raw('Test connection email', function ($message) {
                 $config = $this->getEmailConfig();
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
        $this->setMailConfig();
        
            $config = $this->getEmailConfig();
        // Check jika view exists
        if (!view()->exists($view)) {
            $error = "View {$view} tidak ditemukan";
            Log::error($error);
            $this->createAuditLog($to, $subject, $purpose, false, $error);
            return false;
        }
$this->setDSLNGMailConfig($config);
        try {
           Mail::send($view, $data, function ($message) use ($to, $subject, $attachments, $config) {
                $message->to($to)
                        ->subject($subject)
                        ->from($config->from_address, $config->from_name);
                
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
            Log::error("Email error to {$to}: {$error}");
        }

        $this->createAuditLog($to, $subject, $purpose, $status, $error);
        // return true;
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
        $subject = 'Kode Verifikasi Akun - Whistleblowing System DSLNG';
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
        $subject = 'Selamat Datang di Whistleblowing System DSLNG';
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
        $subject = 'Reset Password - Whistleblowing System DSLNG';
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

    public function sendNotificationEmail(string $to, string $title, string $content, string $type = 'info'): bool
    {
        $subject = $title;
        $view = 'emails.notification';
        $purpose = 'general_notification';
        
        $data = [
            'title' => $title,
            'content' => $content, // Ganti dari 'message' jadi 'content'
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