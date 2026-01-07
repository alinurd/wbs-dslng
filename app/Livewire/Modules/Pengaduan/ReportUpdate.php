<?php

namespace App\Livewire\Modules\Pengaduan;

use App\Helpers\FileHelper;
use App\Helpers\NotificationHelper;
use App\Livewire\Root; 
use App\Models\Pengaduan;
use App\Services\PengaduanEmailService;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class ReportUpdate extends Root
{
    use WithFileUploads;

    public $modul = 'p_report_update';
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.report-update';
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
    public $temporaryFiles = [];
    public $existingFiles = [];
    public $confirmation = true;
    
    public $jenisPengaduanList = [];
    public $saluranList = [];
    public $direktoratList = [];

    public $recordId;
    public $recordCode;

    protected function rules()
    {
        $rules = [
            'waktu_kejadian' => 'required|date',
            'nama_terlapor' => 'required|min:3',
            'jenis_pengaduan_id' => 'required|exists:combos,id',
            'direktorat' => 'required|exists:owners,id',
            'uraian' => 'required|min:10|max:1000',
            'alamat_kejadian' => 'required|min:10|max:500',
            'temporaryFiles' => 'sometimes|array',
            'temporaryFiles.*' => 'max:' . (FileHelper::getMaxPengaduanSize() * 1024) . '|mimes:' . implode(',', FileHelper::getAllowedPengaduanExtensions()),
        ];
        
        // Hanya butuh confirmation saat create, bukan update
        if (!$this->updateMode) {
            $rules['confirmation'] = 'required|accepted';
        }
        
        return $rules;
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
            'temporaryFiles.*.max' => 'Ukuran file maksimal 100MB.',
            'temporaryFiles.*.mimes' => 'Format file harus:  DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF, JPG, JPEG, PNG, AVI, MP4, 3GP, MP3.',
            'confirmation.required' => 'Anda harus menyetujui pernyataan sebelum mengirim pengaduan.',
            'confirmation.accepted' => 'Anda harus menyetujui pernyataan sebelum mengirim pengaduan.'
        ];
    }

    public function mount($code = null)
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
        
        // Jika ada code, berarti mode update
        if ($code) {
            $this->recordCode = $code;
            $this->updateMode = true;
            $this->loadRecordData($code);
        }

        // Load dropdown data
        $this->loadDropdownData();
        
        // Auto-fill user data jika login
        $this->autoFillUserData();
    }


    protected function loadRecordData($code)
    {
        $record = $this->model::where('code_pengaduan', $code)->firstOrFail();
        
        if($record->status!==8){
        //      $this->dispatch('notify', [
        //         'type' => 'success',
        //         'message' => 'Ppdate laporan tidak diizinkan, Pengaduan anda dalam proses review'
        //     ]);
        //     // $this->redirectRoute('p_tracking');
                    $this->updateMode = false;

        }
        // Isi form dengan data yang ada
        $this->waktu_kejadian = $record->waktu_kejadian ? date('Y-m-d', strtotime($record->waktu_kejadian)) : now()->format('Y-m-d');
        $this->nama_terlapor = $record->nama_terlapor ?? '';
        $this->email_pelapor = $record->email_pelapor ?? '';
        $this->telepon_pelapor = $record->telepon_pelapor ?? '';
        $this->jenis_pengaduan_id = $record->jenis_pengaduan_id ?? '';
        $this->saluran_aduan_id = $record->saluran_aduan_id ?? 0;
        $this->direktorat = $record->direktorat ?? '';
        $this->perihal = $record->perihal ?? '';
        $this->uraian = $record->uraian ?? '';
        $this->alamat_kejadian = $record->alamat_kejadian ?? '';
        
        // Load lampiran yang sudah ada
        if ($record->lampiran && $record->lampiran !== 'null') {
            $lampiranData = $this->fixJsonLampiran($record->lampiran);
            $this->temporaryFiles = $lampiranData;
            $this->existingFiles = $lampiranData;
        }
         
        // Simpan recordId untuk update nanti
        $this->recordId = $record->id;
    }

    /**
     * Helper untuk memperbaiki JSON lampiran yang rusak
     */
    protected function fixJsonLampiran($lampiranString)
    {
        // Jika string kosong atau null, return array kosong
        if (empty($lampiranString) || $lampiranString === 'null') {
            return [];
        }

        // Coba decode langsung
        $decoded = json_decode($lampiranString, true);
        
        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
            return $decoded;
        }
        
        // Jika gagal, coba perbaiki string JSON
        // Hilangkan double quotes di awal dan akhir jika ada
        $fixedString = trim($lampiranString, '"');
        
        // Unescape slashes
        $fixedString = stripslashes($fixedString);
        
        // Coba decode lagi
        $decoded = json_decode($fixedString, true);
        
        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
            return $decoded;
        }
        
        // Jika masih gagal, coba ekstrak array secara manual
        if (str_contains($fixedString, 'path') && str_contains($fixedString, 'filename')) {
            // Coba parse sebagai array
            preg_match('/"path":"(.*?)"/', $fixedString, $pathMatch);
            preg_match('/"filename":"(.*?)"/', $fixedString, $filenameMatch);
            
            if (isset($pathMatch[1]) && isset($filenameMatch[1])) {
                return [[
                    'path' => $pathMatch[1],
                    'filename' => $filenameMatch[1]
                ]];
            }
        }
        
        return [];
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
        // Start with existing lampiran
        // $lam['lampiran']=$this->lampiran;
        // $lam['temporaryFiles']=$this->temporaryFiles;
        // dd($lam);
        $lampiranPaths =$this->existingFiles;
        
        // Upload new files jika ada
        // dd($this->temporaryFiles);
        if ($this->temporaryFiles && count($this->temporaryFiles) > 0) {
            $newLampiranPaths = FileHelper::uploadMultiple(
                $this->temporaryFiles, 
                'pengaduan/lampiran', 
                'public'
            );
            $lampiranPaths = array_merge($lampiranPaths, $newLampiranPaths);
        }
        
        // \dd($lampiranPaths);
        // Build payload dari individual properties
        $payload = [
            'waktu_kejadian' => $this->waktu_kejadian,
            'nama_terlapor' => $this->nama_terlapor,
            'email_pelapor' => $this->email_pelapor,
            'telepon_pelapor' => $this->telepon_pelapor,
            'jenis_pengaduan_id' => $this->jenis_pengaduan_id,
            'saluran_aduan_id' => $this->saluran_aduan_id,
            'direktorat' => $this->direktorat,
            'perihal' => $this->perihal,
            'uraian' => $this->uraian,
            'alamat_kejadian' => $this->alamat_kejadian,
            'fwd_to' => 0,
            'sts_fwd' => 0,
            'act_eks' => 0,
            'act_int' => 0,
            'act_cc' => 0,
            'act_cco' => 0,
            'status' => 0,
        ];
        
        // Tambahkan lampiran ke payload jika ada
        if (!empty($lampiranPaths)) {
            $payload['lampiran'] = json_encode($lampiranPaths);
        } else {
            $payload['lampiran'] = null;
        }
        
        // Hanya generate code untuk create mode
        if (!$this->updateMode) {
            $year = date('Y');
            $shortYear = substr($year, -2);
            $countThisYear = Pengaduan::whereYear('created_at', $year)->count() + 1;
            $countAll = Pengaduan::count() + 1;
            $countThisYearFormatted = str_pad($countThisYear, 4, '0', STR_PAD_LEFT);
            $countAllFormatted = str_pad($countAll, 4, '0', STR_PAD_LEFT);
            $codePengaduan = $shortYear . '-' . $countThisYearFormatted . '-' . $countAllFormatted;
            
            $payload['code_pengaduan'] = $codePengaduan;
            $payload['status'] = 0; // Status pending
            $payload['user_id'] = auth()->id();
            $payload['tanggal_pengaduan'] = now();
            
            // Kirim notifikasi email hanya untuk create
            $emailService = new PengaduanEmailService();
            $emailService->sendNewPengaduanNotifications($payload, auth()->id());
        }
        
        return $payload;
    }

    /**
     * Save method untuk handle create dan update
     */
    public function save()
    {
        // $this->validate();
        
        $payload = $this->saving([]);
        
        if ($this->updateMode && $this->recordId) {
            // Update existing record
            // dd($payload);
            $record = $this->model::findOrFail($this->recordId);
            $record->update($payload);
            $this->saved($record, 'update');
        } else {
            // Create new record
            $record = $this->model::create($payload);
            $this->saved($record, 'create');
        }
    }

    public function saved($record, $action)
    {
        if ($action === 'create') {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pengaduan berhasil dibuat dengan nomor: ' . $record->code_pengaduan
            ]);
            $this->resetLampiran();
            $this->redirectRoute('p_tracking');
        } else {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pengaduan berhasil diperbarui.'
            ]);
            // Redirect kembali ke halaman tracking
            $this->redirectRoute('p_tracking');
        }
    }

 public function updatedLampiran($value)
{
    if ($value) {
        // Tambahkan file baru ke array temporaryFiles
        foreach ($value as $file) {
            $this->temporaryFiles[] = $file;
        }
        
        // Reset input file (penting!)
        $this->lampiran = [];
        
        // Dispatch event untuk refresh UI jika diperlukan
        $this->dispatch('files-added');
    }
}

    public function resetLampiran()
    {
        $this->lampiran = [];
        $this->temporaryFiles = [];
        $this->confirmation = false;
    }

    // Override resetForm untuk include semua fields
    public function resetForm()
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

    public function submit()
    {
        sleep(3); 
    }

    public function render()
    {
        return view($this->viewPath(), [
            'jenisPengaduanList' => $this->jenisPengaduanList,
            'saluranList' => $this->saluranList,
            'direktoratList' => $this->direktoratList,
            'userInfo' => $this->userInfo,
            'permissions' => module_permissions(strtolower($this->modul))['can'] ?? [],
            'temporaryFiles' => $this->temporaryFiles,
            'updateMode' => $this->updateMode,
            'recordCode' => $this->recordCode ?? null,
        ]);
    }

    protected function getAuditMessage($action, $record, $data)
    {
        // Custom message untuk model Pengaduan
        switch ($action) {
            case 'create':
                return 'Pengaduan berhasil dibuat dengan nomor: ' . $record->code_pengaduan;
                
            case 'update':
                return 'Pengaduan berhasil diperbarui. #'  . $record->code_pengaduan;
                
            case 'delete':
                return 'Pengaduan berhasil dihapus.';
                
            default:
                return parent::getAuditMessage($action, $record, $data);
        }
    }

    /**
     * Get file icon untuk display di view
     */

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
    // Method untuk menghapus file dari temporaryFiles
    public function removeFile($index)
{
            $type='error';
               $msg="Files gagal dihapus dari daftar unggahan.";

    if (isset($this->temporaryFiles[$index])) {
        unset($this->temporaryFiles[$index]);
        $this->temporaryFiles = array_values($this->temporaryFiles); // Reset array index
               $msg="Files berhasil dihapus dari daftar unggahan.";
                       $type='success';

    }
    if (isset($this->existingFiles[$index])) {
        unset($this->existingFiles[$index]);
        $this->existingFiles = array_values($this->existingFiles);
        $msg="Files berhasil dihapus dari daftar unggahan.";
        $type='success';
       
    }
     $this->dispatch('notify', [
                'type' => $type,
                'message' => $msg
            ]);

}

    // Jika sudah ada method removeLampiran, ubah atau buat method baru
public function removeLampiran($index)
{
    // Hapus dari temporaryFiles (bukan lampiran)
    if (isset($this->temporaryFiles[$index])) {
        unset($this->temporaryFiles[$index]);
        $this->temporaryFiles = array_values($this->temporaryFiles);
    }
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