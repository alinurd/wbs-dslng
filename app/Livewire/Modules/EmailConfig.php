<?php
namespace App\Livewire\Modules;

use App\Livewire\Root; 
use App\Models\Audit as AuditLog;
use App\Models\EmailConfig as ModelsEmailConfig;
use App\Models\LogApproval;
use App\Services\EmailService;

class EmailConfig extends Root
{ 

    public $modul = 'c_email';
    public $model = ModelsEmailConfig::class;
    public $logModel = LogApproval::class;
    public $views = 'modules.config-email';
    public $title = "Email Configuration";
    public $dataFAQ =[];
    public $testEmail = '';
    public $isTesting = false;
    public $testingConfigId = null;
    public $form = [
        'mailer' => 'smtp',
        'host' => '',
        'port' => '',
        'encryption' => '',
        'username' => '',
        'password' => '',
        'from_address' => '',
        'from_name' => '',
        'active' => true,
    ];

    public $rules = [
        'form.mailer' => 'required|string|max:255',
        'form.host' => 'required|string|max:255',
        'form.port' => 'required|integer',
        'form.encryption' => 'nullable|string|max:255',
        'form.username' => 'required|email',
        'form.password' => 'required|string',
        'form.from_address' => 'required|email',
        'form.from_name' => 'required|string|max:255',
        'form.active' => 'boolean',
    ];

    protected $messages = [
        'form.mailer.required' => 'Mailer wajib diisi',
        'form.host.required' => 'Host wajib diisi',
        'form.port.required' => 'Port wajib diisi',
        'form.port.integer' => 'Port harus berupa angka',
        'form.username.required' => 'Username wajib diisi',
        'form.username.email' => 'Username harus berupa email valid',
        'form.password.required' => 'Password wajib diisi',
        'form.from_address.required' => 'From address wajib diisi',
        'form.from_address.email' => 'From address harus berupa email valid',
        'form.from_name.required' => 'From name wajib diisi',
    ];

    public function columns()
    {
        return [
            'mailer',
            'host', 
            'port',
            'username',
            'from_address',
            'from_name',
            'active'
        ];
    }

    public function mount()
    {
        parent::mount();  
    }

   public function view($id)
{
    can_any([strtolower($this->modul).'.view']);
    
    $record = $this->model::findOrFail($id);

    $this->detailData = [
        'Mailer' => $record->mailer,
        'Host' => $record->host,
        'Port' => $record->port,
        'Encryption' => $record->encryption ?? '-',
        'Username' => $record->username,
        'From Address' => $record->from_address,
        'From Name' => $record->from_name,
        'Status' => $record->active ? 'Aktif' : 'Nonaktif',
        'Dibuat Pada' => $record->created_at ? $record->created_at->format('d/m/Y H:i') : '-',
        'Diupdate Pada' => $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : '-',
        'Tes Connect' => $record->id,
    ];
    
    $this->detailTitle = "Detail " . $this->title;
    $this->showDetailModal = true;
}

    

 public function testConnect($id)
    {
        $this->validate();
        
        $record = EmailConfig::findOrFail($id);
        $this->testingConfigId = $id;
        $this->isTesting = true;
        
        Log::info('Testing email config ID: ' . $id, [
            'record' => $record->toArray(),
            'test_email' => $this->testEmail
        ]);

        try {
            // Kirim email test dengan konfigurasi spesifik
            $emailService = app(EmailService::class);
            
            $sendTest = $emailService->sendTestEmailWithConfig(
                $this->testEmail,
                'Test Koneksi Email - Whistleblowing System DSLNG',
                [
                    'id' => $record->id, // Tambahkan ID untuk logging
                    'mailer' => $record->mailer,
                    'host' => $record->host,
                    'port' => $record->port,
                    'encryption' => $record->encryption,
                    'username' => $record->username,
                    'password' => $record->password,
                    'from_address' => $record->from_address,
                    'from_name' => $record->from_name,
                ]
            );
            
            if ($sendTest) {
                // Audit Log untuk TEST CONNECTION BERHASIL
                $this->createSuccessAuditLog($record);
                $this->notify('success', '✅ Test email berhasil dikirim ke: ' . $this->testEmail);
            } else {
                $this->createFailedAuditLog($record, 'Gagal mengirim test email');
                $this->notify('error', '❌ Gagal mengirim test email ke: ' . $this->testEmail);
            }

        } catch (\Exception $e) {
            Log::error('Email test error: ' . $e->getMessage(), [
                'config_id' => $id,
                'error' => $e->getTraceAsString()
            ]);
            
            $this->createErrorAuditLog($record, $e);
            $this->notify('error', '❌ Error testing connection: ' . $e->getMessage());
        }

        $this->isTesting = false;
        $this->testingConfigId = null;
    }

/**
 * Helper method untuk menyamarkan email
 */
private function maskEmail($email)
{
    if (empty($email)) return '***@***.***';
    
    $parts = explode('@', $email);
    if (count($parts) !== 2) return '***@***.***';
    
    $username = $parts[0];
    $domain = $parts[1];
    
    $maskedUsername = substr($username, 0, 2) . '***' . (strlen($username) > 5 ? substr($username, -1) : '');
    $domainParts = explode('.', $domain);
    $maskedDomain = substr($domainParts[0], 0, 2) . '***.' . (end($domainParts) ?? 'com');
    
    return $maskedUsername . '@' . $maskedDomain;
}

/**
 * Helper method untuk menyamarkan string
 */
private function maskString($string, $visibleChars = 3)
{
    if (empty($string)) return '***';
    
    $visible = substr($string, 0, $visibleChars);
    return $visible . '***';
}

    

    public function resetTest()
    {
        $this->testEmail = '';
        $this->isTesting = false;
        $this->testingConfigId = null;
    }
    
}