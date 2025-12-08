<?php

namespace App\Livewire\Modules\Pengaduan;

use App\Helpers\FileHelper;
use App\Helpers\NotificationHelper;
use App\Livewire\Root; 
use App\Models\Pengaduan;
use App\Services\PengaduanEmailService;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Report extends Root
{
    use WithFileUploads;

    public $modul = 'p_report'; // Ubah ke 'pengaduan'
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.report';
        public $updateMode = false;

    // Individual properties untuk form fields
    public $waktu_kejadian;
    public $nama_terlapor;
    public $email_pelapor;
    public $telepon_pelapor;
    public $jenis_pengaduan_id;
    public $saluran_aduan_id;
    public $direktorat;
    public $perihal;
    public $uraian;
    public $alamat_kejadian;
    
    public $lampiran = [];
    public $confirmation = false;
    
    public $jenisPengaduanList = [];
    public $saluranList = [];
    public $direktoratList = [];

    protected function rules()
    {
        return [
            'waktu_kejadian' => 'required|date',
            'nama_terlapor' => 'required|min:3',
            // 'email_pelapor' => 'required|email',
            // 'telepon_pelapor' => 'numeric|required|min:12',
            'jenis_pengaduan_id' => 'required|exists:combos,id',
            // 'saluran_aduan_id' => 'required|exists:combos,id',
            'direktorat' => 'required|exists:owners,id',
            // 'perihal' => 'required|min:5|max:200',
            'uraian' => 'required|min:10|max:1000',
            'alamat_kejadian' => 'required|min:10|max:500',
            
            'lampiran.*' => 'max:' . (FileHelper::getMaxPengaduanSize() * 1024) . '|mimes:' . implode(',', FileHelper::getAllowedPengaduanExtensions()),
            
            'confirmation' => 'required|accepted'
        ];
    }

    protected function messages()
    {
        return [
            'waktu_kejadian.required' => 'Waktu kejadian harus diisi.',
            'nama_terlapor.required' => 'Nama terlapor harus diisi.',
            'email_pelapor.required' => 'Email pelapor harus diisi.',
            'telepon_pelapor.required' => 'Telepon pelapor harus diisi.',
            'telepon_pelapor.numeric' => 'Telepon pelapor harus diisi dengan angka.',
            'jenis_pengaduan_id.required' => 'Jenis pengaduan harus dipilih.',
            'saluran_aduan_id.required' => 'Saluran aduan harus dipilih.',
            'direktorat.required' => 'Direktorat harus dipilih.',
            'perihal.required' => 'Perihal harus diisi.',
            'uraian.required' => 'Uraian pengaduan harus diisi.',
            'alamat_kejadian.required' => 'Alamat tempat kejadian harus diisi.',
            'lampiran.*.max' => 'Ukuran file maksimal 100MB.',
            'lampiran.*.mimes' => 'Format file harus:  DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF, JPG, JPEG, PNG, AVI, MP4, 3GP, MP3.',
            'confirmation.required' => 'Anda harus menyetujui pernyataan sebelum mengirim pengaduan.',
            'confirmation.accepted' => 'Anda harus menyetujui pernyataan sebelum mengirim pengaduan.'
        ];
    }

    public function mount()
    {
        parent::mount();
        
        // Set default values untuk individual properties
        $this->waktu_kejadian = now()->format('Y-m-d');
        $this->nama_terlapor = '';
        $this->email_pelapor = '';
        $this->telepon_pelapor = '';
        $this->jenis_pengaduan_id = '';
        $this->saluran_aduan_id = '';
        $this->direktorat = '';
        $this->perihal = '';
        $this->uraian = '';
        $this->alamat_kejadian = '';
        
        // Load dropdown data
        $this->loadDropdownData();
        
        // Auto-fill user data jika login
        $this->autoFillUserData();
    }

    

    protected function autoFillUserData()
    {
        if ($this->userInfo) {
            $this->email_pelapor = $this->userInfo['user']['email'] ?? '';
            $this->telepon_pelapor = $this->userInfo['user']['telepon'] ?? '';
        }
    }

    /**
     * Override saving method untuk menambahkan field khusus pengaduan
     */
    protected function saving($payload)
    {
        // Upload lampiran
        //  dd($this->userInfo);

       $lampiranPaths = [];
        if ($this->lampiran && count($this->lampiran) > 0) {
            $lampiranPaths = FileHelper::uploadMultiple(
                $this->lampiran, 
                'pengaduan/lampiran', 
                'public'
            );
        }
 
       $year = date('Y');
 
$shortYear = substr($year, -2);
 
$countThisYear = pengaduan::whereYear('created_at', $year)->count() + 1;
 
$countAll = pengaduan::count() + 1;
 
$countThisYearFormatted = str_pad($countThisYear, 4, '0', STR_PAD_LEFT); // 
$countAllFormatted = str_pad($countAll, 4, '0', STR_PAD_LEFT); // contoh 0013

$codePengaduan = $shortYear . '-' . $countThisYearFormatted . '-' . $countAllFormatted;


        // Build payload dari individual properties
        $payload = [
            'waktu_kejadian' => $this->waktu_kejadian,
            'nama_terlapor' => $this->nama_terlapor,
            'email_pelapor' => $this->email_pelapor,
            'telepon_pelapor' => $this->telepon_pelapor,
            'jenis_pengaduan_id' => $this->jenis_pengaduan_id,
            // 'saluran_aduan_id' => $this->saluran_aduan_id,
            'direktorat' => $this->direktorat,
            // 'perihal' => $this->perihal,
            'uraian' => $this->uraian,
            'alamat_kejadian' => $this->alamat_kejadian,
        ];

        // Tambahkan field khusus pengaduan ke payload
        $payload['code_pengaduan'] = $codePengaduan;
        $payload['lampiran'] = !empty($lampiranPaths) ? json_encode($lampiranPaths) : null;
        $payload['status'] = 0; // Status pending
        $payload['user_id'] = auth()->id();
        $payload['tanggal_pengaduan'] = now();


//         NotificationHelper::sendToRole(
//     2, // role ID
//     'Laporan Baru ID:' . $codePengaduan,
//     auth()->user()->name . ' membuat laporan baru',
//     auth()->id() // sender (optional)
// );

// NotificationHelper::sendToUser(
//     123, // user ID penerima
//     'Pesan Baru',
//     'Anda memiliki pesan baru',
//     auth()->id() // sender (optional)
// );

$emailService = new PengaduanEmailService();
$emailService->sendNewPengaduanNotifications($payload, auth()->id());
 

        return $payload;
    }

    /**
     * Override saved method untuk custom message
     */
    public function saved($record, $action)
{
    $message = $action === 'create' 
        ? 'Pengaduan berhasil dibuat dengan nomor: ' . $record->code_pengaduan
        : 'Pengaduan berhasil diperbarui.';
// $emailService->sendNewPengaduanNotifications($record, $this->email_pelapor);

$this->dispatch('notify', [
    'type' => 'success',
    'message' => 'Pengaduan berhasil dibuat dengan nomor: ' . $record->code_pengaduan
]);




 
    $this->resetLampiran();
    $this->redirectRoute('p_tracking');

}

  

  

    public function resetLampiran()
    {
        $this->lampiran = [];
        $this->confirmation = false;
    }

    // Override resetForm untuk include semua fields
    protected function resetForm()
    {
        $this->waktu_kejadian = now()->format('Y-m-d');
        $this->nama_terlapor = '';
        $this->email_pelapor = '';
        $this->telepon_pelapor = '';
        $this->jenis_pengaduan_id = '';
        $this->saluran_aduan_id = '';
        $this->direktorat = '';
        $this->perihal = '';
        $this->uraian = '';
        $this->alamat_kejadian = '';
        $this->resetLampiran();
        $this->resetErrorBag();
    }

    public function updated($propertyName)
    {
        if (session()->has('success') || session()->has('error')) {
            session()->forget(['success', 'error']);
        }
        
        if ($propertyName === 'confirmation') {
            $this->validateOnly($propertyName);
        }
    }

    public function render()
    {
        return view($this->viewPath(), [
            'jenisPengaduanList' => $this->jenisPengaduanList,
            'saluranList' => $this->saluranList,
            'direktoratList' => $this->direktoratList,
            'userInfo' => $this->userInfo,
            'permissions' => module_permissions(strtolower($this->modul))['can'] ?? []
        ]);
    }

    protected function getAuditMessage($action, $record, $data)
{
    // Custom message untuk model Pengaduan
    switch ($action) {
        case 'create':
            return 'Pengaduan berhasil dibuat dengan nomor: ' . $record->code_pengaduan;
            
        case 'update':
            return 'Pengaduan berhasil diperbarui.';
            
        case 'delete':
            return 'Pengaduan berhasil dihapus.';
            
        default:
            return parent::getAuditMessage($action, $record, $data);
    }
}


/**
     * Get file icon untuk display di view
     */
    public function getFileIcon($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return FileHelper::getFileIcon($extension);
    }

    /**
     * Format file size untuk display
     */
    public function formatFileSize($bytes)
    {
        return FileHelper::formatSize($bytes);
    }


    public function removeLampiranByName($filename)
    {
        foreach ($this->lampiran as $index => $file) {
            if ($file->getClientOriginalName() === $filename) {
                $this->removeLampiran($index);
                break;
            }
        }
    }

       
}