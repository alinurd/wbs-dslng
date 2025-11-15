<?php

namespace App\Livewire\Modules\Pengaduan;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Owner;
use App\Models\Pengaduan;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class Report extends Root
{
    use WithFileUploads;

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

      public $form = [
        'waktu_kejadian' => 'aduan',
        'nama_terlapor' => 'aduan',
        'email_pelapor' => null,
        'telepon_pelapor' => null,
         'jenis_pengaduan_id' => null,
        'saluran_aduan_id' => null,
        'direktorat' => null,
        'alamat_kejadian' => '',
        'perihal' => '',
        'uraian' => '',
        'lampiran' => '',
    ];

    public $rules = [
        'waktu_kejadian' => 'required|date',
        'nama_terlapor' => 'required|min:3|max:100',
        'email_pelapor' => 'required|email',
        'telepon_pelapor' => 'required|min:10|max:15',
        'jenis_pengaduan_id' => 'required',
        'saluran_aduan_id' => 'required',
        'direktorat' => 'nullable',
        'perihal' => 'required|min:5|max:200',
        'uraian' => 'required|min:10|max:1000',
        'alamat_kejadian' => 'required|min:10|max:500',
        'lampiran.*' => 'max:102400|mimes:zip,rar,doc,docx,xls,xlsx,ppt,pptx,pdf,jpg,jpeg,png,avi,mp4,3gp,mp3',
        'confirmation' => 'required|accepted'
    ];

    public $messages = [
        'waktu_kejadian.required' => 'Tanggal pengaduan harus diisi.',
        'nama_terlapor.required' => 'Nama pelapor harus diisi.',
        'email_pelapor.required' => 'Email pelapor harus diisi.',
        'telepon_pelapor.required' => 'Telepon pelapor harus diisi.',
        'jenis_pengaduan_id.required' => 'Jenis pengaduan harus dipilih.',
        'saluran_aduan_id.required' => 'Saluran aduan harus dipilih.',
        'perihal.required' => 'Perihal harus diisi.',
        'uraian.required' => 'Uraian pengaduan harus diisi.',
        'alamat_kejadian.required' => 'Alamat tempat kejadian harus diisi.',
        'lampiran.*.max' => 'Ukuran file maksimal 100MB.',
        'lampiran.*.mimes' => 'Format file harus: ZIP, RAR, DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF, JPG, JPEG, PNG, AVI, MP4, 3GP, MP3.',
        'confirmation.required' => 'Anda harus menyetujui pernyataan sebelum mengirim pengaduan.',
        'confirmation.accepted' => 'Anda harus menyetujui pernyataan sebelum mengirim pengaduan.'
    ];

    public function mount()
    {
        $this->waktu_kejadian = now()->format('Y-m-d');
        $this->loadJenisPengaduan();
        $this->loadSaluranAduan();
        $this->loadDirektorat();
    }

    public function loadJenisPengaduan()
    {
        
        $this->jenisPengaduanList = Combo::where('kelompok', 'jenis')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get();
    }

    public function loadSaluranAduan()
    {
        $this->saluranList = Combo::where('kelompok', 'aduan')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get();
    }

    public function loadDirektorat()
    {
        $this->direktoratList = Owner::where('is_active', 1)
             ->orderBy('owner_name')
            ->get();
            
    }

    public function simpanPengaduan()
    {
        $this->validate();

        try {
            $noPengaduan = 'ADU-' . date('Ymd') . '-' . Str::random(6);

            // Upload lampiran
            $lampiranPaths = [];
            if ($this->lampiran && count($this->lampiran) > 0) {
                foreach ($this->lampiran as $file) {
                    $path = $file->store('pengaduan/lampiran', 'public');
                    $lampiranPaths[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize()
                    ];
                }
            }

      
            // Simpan pengaduan
            $pengaduan = Pengaduan::create([
                'code_pengaduan' => $noPengaduan,
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
                'lampiran' => json_encode($lampiranPaths),
                'user_id' => auth()->user()->id,
                'status' => 1
            ]);

            $this->resetForm();
            session()->flash('success', 'Pengaduan berhasil dikirim dengan nomor: ' . $noPengaduan);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'waktu_kejadian',
            'nama_terlapor',
            'email_pelapor',
            'telepon_pelapor',
            'jenis_pengaduan_id',
            'saluran_aduan_id',
            'direktorat',
            'perihal',
            'uraian',
            'alamat_kejadian',
            'lampiran',
            'confirmation'
        ]);
        $this->waktu_kejadian = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    public function removeLampiran($index)
    {
        if (isset($this->lampiran[$index])) {
            unset($this->lampiran[$index]);
            $this->lampiran = array_values($this->lampiran);
        }
    }

    public function updatedLampiran($value)
    {
        // Validasi file yang diupload
        foreach ($this->lampiran as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedTypes = ['zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'jpg', 'jpeg', 'png', 'avi', 'mp4', '3gp', 'mp3'];
            
            if (!in_array($extension, $allowedTypes)) {
                session()->flash('error', 'File ' . $file->getClientOriginalName() . ' tidak diizinkan. Format yang didukung: ZIP, RAR, DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF, JPG, JPEG, PNG, AVI, MP4, 3GP, MP3.');
                $this->removeLampiran($index);
                continue;
            }
            
            if ($file->getSize() > 100 * 1024 * 1024) {
                session()->flash('error', 'File ' . $file->getClientOriginalName() . ' terlalu besar. Maksimal 100MB.');
                $this->removeLampiran($index);
                continue;
            }
        }
        
        $this->resetErrorBag('lampiran.*');
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
        return view('livewire.modules.pengaduan.report');
    }
}