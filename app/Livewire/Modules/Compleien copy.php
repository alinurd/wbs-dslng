<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class Compleien extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'complien';
    public $model = Pengaduan::class;
    public $views = 'modules.complien';
    public $title = "Complien";
    
    // Properties untuk form
    public $catatan = '';
    public $file_upload;
    public $submission_action = '';
    public $selected_pengaduan_id = '';

    // Properties untuk detail
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload di sidebar
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];

     

    public $rules = [
        'catatan' => 'required|min:10',
        'file_upload' => 'nullable|file|max:10240', // 10MB max
    ];

    public function removeFile()
    {
        $this->reset('file_upload');
    }

    public function setAction($action)
    {
        $this->submission_action = $action;
    }

    function getFlow(){
$user = auth()->user();


// Atau jika user memiliki multiple roles, ambil semua ID
$roleIds = $user->roles()->pluck('id');
// \dd($roleIds);
// if($roleIds ==2){
    return Combo::where('param_int',$this->submission_action )->first();
// }
    }

    public function submitForm()
    {
        // Validasi form
        // $this->validate();
     
 $flow=$this->getFlow();
 dd($flow);
 
        // Pastikan action sudah dipilih
        if (empty($this->submission_action)) {
            session()->flash('error', 'Silakan pilih action terlebih dahulu!');
            return;
        }

        // Pastikan ada pengaduan yang dipilih
        if (empty($this->selected_pengaduan_id)) {
            session()->flash('error', 'Tidak ada pengaduan yang dipilih!');
            return;
        }

        // Proses berdasarkan action
        if ($this->submission_action === 'approve') {
            $this->processApproval();
        } elseif ($this->submission_action === 'reject') {
            $this->processRejection();
        }

        // Reset form
        $this->resetForm();
    }
    

    private function processApproval()
    {
        try {
            
            // $flow= $this->getFlow()
            \dd(auth()->getRoleNames());
            // Simpan file jika ada
            $filePath = null;
            $fileName = null;
            
            if ($this->file_upload) {
                $filePath = $this->file_upload->store('pengaduan-approvals', 'public');
                $fileName = $this->file_upload->getClientOriginalName();
            }

            // Update database - sesuaikan dengan struktur tabel Anda
            $pengaduan = Pengaduan::find($this->selected_pengaduan_id);
            if ($pengaduan) {
                // Update status pengaduan
                $pengaduan->update([
                    'status' => 'completed', // atau status yang sesuai
                    'catatan_approval' => $this->catatan,
                    'file_approval_path' => $filePath,
                    'file_approval_name' => $fileName,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);

                // Tambahkan log/riwayat approval jika diperlukan
                // $pengaduan->approvalLogs()->create([
                //     'user_id' => auth()->id(),
                //     'action' => 'approved',
                //     'catatan' => $this->catatan,
                //     'file_path' => $filePath,
                //     'created_at' => now(),
                // ]);
            }

             $this->notify('success', $this->getAuditMessage('updateStatus', null, [])); 
            // Debug data yang dikirim
            // dd([
            //     'action' => 'approve',
            //     'pengaduan_id' => $this->selected_pengaduan_id,
            //     'catatan' => $this->catatan,
            //     'file_upload' => $fileName,
            //     'status' => 'completed'
            // ]);

            // Tutup modal setelah sukses
            $this->showuUdateStatus = false;

        } catch (\Exception $e) {
            $this->notify('error', $this->getAuditMessage('error', null, []), $e->getMessage()); 
        }
    }

    private function processRejection()
    {
        try {
            // Simpan file jika ada
            $filePath = null;
            $fileName = null;
            
            if ($this->file_upload) {
                $filePath = $this->file_upload->store('pengaduan-rejections', 'public');
                $fileName = $this->file_upload->getClientOriginalName();
            }

            // Update database - sesuaikan dengan struktur tabel Anda
            $pengaduan = Pengaduan::find($this->selected_pengaduan_id);
            if ($pengaduan) {
                // Update status pengaduan
                $pengaduan->update([
                    'status' => 'rejected', // atau status yang sesuai
                    'catatan_rejection' => $this->catatan,
                    'file_rejection_path' => $filePath,
                    'file_rejection_name' => $fileName,
                    'rejected_at' => now(),
                    'rejected_by' => auth()->id(),
                ]);

                // Tambahkan log/riwayat rejection jika diperlukan
                // $pengaduan->approvalLogs()->create([
                //     'user_id' => auth()->id(),
                //     'action' => 'rejected',
                //     'catatan' => $this->catatan,
                //     'file_path' => $filePath,
                //     'created_at' => now(),
                // ]);
            }

                        $this->notify('success', $this->getAuditMessage('updateStatus', null, [])); 
 
            
            // Debug data yang dikirim
            // dd([
            //     'action' => 'reject',
            //     'pengaduan_id' => $this->selected_pengaduan_id,
            //     'catatan' => $this->catatan,
            //     'file_upload' => $fileName,
            //     'status' => 'rejected'
            // ]);

            // Tutup modal setelah sukses
            $this->showuUdateStatus = false;

        } catch (\Exception $e) {
 
            $this->notify('error', $this->getAuditMessage('error', null, []), $e->getMessage()); 
        }
    }

    public function resetForm()
    {
        $this->reset(['catatan', 'file_upload', 'submission_action']);
    }

    public function submitCatatan()
    {
        $this->validate();

        try {
            // Simpan catatan
            $catatanData = [
                'catatan' => $this->catatan,
                'user_id' => auth()->id(),
                'created_at' => now(),
            ];

            // Simpan file jika ada
            if ($this->file_upload) {
                $filename = $this->file_upload->store('pengaduan-lampiran', 'public');
                $catatanData['file_path'] = $filename;
                $catatanData['file_name'] = $this->file_upload->getClientOriginalName();
            }

            // Simpan ke database atau lakukan processing
            // YourModel::create($catatanData);

            session()->flash('message', 'Catatan berhasil disimpan!');
            
            // Reset form
            $this->reset(['catatan', 'file_upload']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan catatan: ' . $e->getMessage());
        }
    }

    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
    }

    public function query()
    {
        $q = ($this->model)::with(['jenisPengaduan', 'pelapor']);
        
        if ($this->search && method_exists($this, 'columns')) {
            $columns = $this->columns();
            if (is_array($columns) && count($columns)) {
                $q->where(function ($p) use ($columns) {
                    foreach ($columns as $col) {
                        $p->orWhere($col, 'like', "%{$this->search}%");
                    }
                });
            }
        }

        if (is_array($this->filters)) {
            foreach ($this->filters as $key => $val) {
                if ($key == 'tahun' && !empty($val)) {
                    $q->whereYear('tanggal_pengaduan', $val);
                }
                if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                    $q->where('jenis_pengaduan_id', $val);
                }
            }
        }

        return $q;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
        ];
        
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

    public function comment($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::with(['comments.user'])->findOrFail($id);

        $detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenisPengaduan->name ?? 'Tidak diketahui',
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
            'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
            'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
        ];
        
        $detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
        
        $this->openChat($id, $detailData, $detailTitle);
        
        $this->loadUploadedFiles();
    }

    public function updateStatus($id, $status)
    {
        $record = $this->model::findOrFail($id);

        // Set pengaduan yang dipilih
        $this->selected_pengaduan_id = $id;

        $this->detailData = [
            'id' => $id, // Tambahkan ID untuk form
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
            'status_ex' => $status == 1 ? 'Lengkap [EX]' : 'Tidak Lengkap [EX]',
            'log'   =>[
                [
                                                'id' => '7-DTRPOG',
                                                'judul_pengaduan' => 'Pelanggaran Etika - Penyalahgunaan Wewenang',
                                                'status_akhir' => 'Menunggu Approval CCO',
                                                'progress' => 70,
                                                'log_approval' => [
                                                    [
                                                        'step' => 1,
                                                        'role' => 'Pelapor',
                                                        'nama' => 'Ahmad Santoso',
                                                        'status' => 'completed',
                                                        'status_text' => 'Disubmit',
                                                        'waktu' => '17/11/2024 10:30',
                                                        'catatan' => 'Laporan awal telah disampaikan dengan lengkap',
                                                        'file' => ['bukti_1.pdf', 'foto_1.jpg'],
                                                        'warna' => 'green',
                                                    ],
                                                    [
                                                        'step' => 2,
                                                        'role' => 'WBS Eksternal',
                                                        'nama' => 'dr. Sari Wijaya',
                                                        'status' => 'completed',
                                                        'status_text' => 'Approved',
                                                        'waktu' => '18/11/2024 14:15',
                                                        'catatan' => 'Dokumen sudah lengkap dan memenuhi syarat',
                                                        'file' => ['review_wbs_eksternal.pdf'],
                                                        'warna' => 'green',
                                                    ],
                                                    [
                                                        'step' => 3,
                                                        'role' => 'WBS internal',
                                                        'nama' => 'dr. santoso',
                                                        'status' => 'completed',
                                                        'status_text' => 'Approved',
                                                        'waktu' => '18/11/2024 14:15',
                                                        'catatan' => 'Dokumen sudah lengkap dan memenuhi syarat',
                                                        'file' => ['review_wbs_internal.pdf'],
                                                        'warna' => 'green',
                                                    ],
                                                    [
                                                        'step' => 4,
                                                        'role' => 'WBS Forward',
                                                        'nama' => 'Tim Investigasi',
                                                        'status' => 'in_progress',
                                                        'status_text' => 'Dalam Proses',
                                                        'waktu' => '20/11/2024 11:20',
                                                        'catatan' => 'Sedang dilakukan investigasi lebih lanjut',
                                                        'file' => [],
                                                        'warna' => 'yellow',
                                                    ],
                                                    [
                                                        'step' => 5,
                                                        'role' => 'CCO',
                                                        'nama' => '-',
                                                        'status' => 'pending',
                                                        'status_text' => 'Menunggu',
                                                        'waktu' => '-',
                                                        'catatan' => 'Menunggu hasil investigasi dari WBS Forward',
                                                        'file' => [],
                                                        'warna' => 'gray',
                                                    ],
                                                ],
                                            ]
            ]
        ];
        
        $this->detailTitle = "Update Status " . $this->title;
        $this->showuUdateStatus = true;
        
        $this->loadUploadedFiles();
    }

    public function addNote($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
        ];
        
        $this->detailTitle = "Noted " . $this->title;
        $this->ShowNote = true;
        
        $this->loadUploadedFiles();
    }

   
}