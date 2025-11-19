<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\LogApproval;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class Compleien extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'complien';
    public $model = Pengaduan::class;
    public $logModel = LogApproval::class;
    public $views = 'modules.complien';
    public $title = "Complien";
    
    // Properties untuk form
    public $catatan = '';
    public $file_upload;
    public $submission_action = '';
    public $selected_pengaduan_id = '';
    public $pengaduan_id = '';

    // Properties untuk detail modal
    public $showuUdateStatus = false;
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

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
        $this->userInfo();
    }

    public function removeFile()
    {
        $this->reset('file_upload');
    }
    
    public function setAction($action, $id = null)
    {
        $this->submission_action = $action;
        if ($id) {
            $this->selected_pengaduan_id = $id;
            $this->pengaduan_id = $id;
        }
    }

    public function submitForm()
    {
        try {
            // Validasi form
            $this->validate();

            // Validasi action dan pengaduan_id
            if (empty($this->submission_action)) {
                $this->notify('error', 'Silakan pilih action terlebih dahulu!');
                return;
            }

            if (empty($this->selected_pengaduan_id)) {
                $this->notify('error', 'Tidak ada pengaduan yang dipilih!');
                return;
            }

            // Handle file upload
            $filePath = null;
            $fileName = null;
            
            if ($this->file_upload) {
                $filePath = $this->file_upload->store('pengaduan-approvals', 'public');
                $fileName = $this->file_upload->getClientOriginalName();
            }

            // Update pengaduan
            $pengaduan = Pengaduan::find($this->selected_pengaduan_id);
            
            if ($pengaduan) {
                // Get status info dari combos
                $statusInfo = Combo::where('kelompok', 'sts-aduan')
                    ->where('param_int', $this->submission_action)
                    ->first();

                if ($statusInfo) {
                    $updateData = [
                        'status' => $this->submission_action, // Simpan param_int sebagai status
                        'sts_final' => in_array($this->submission_action, [3, 6, 7]) ? 1 : 0, // Disetujui, Lengkap [Ex], Lengkap [In]
                        'updated_at' => now(),
                    ];

                    // Add catatan if provided
                    if (!empty($this->catatan)) {
                        $updateData['catatan'] = $this->catatan;
                    }

                    $pengaduan->update($updateData);

                    // Create log approval
                    $this->createLogApproval($pengaduan, $statusInfo, $filePath, $fileName);
                }
            }

            $this->notify('success', 'Status pengaduan berhasil diupdate!');
            $this->showuUdateStatus = false;

            $this->resetForm();
        } catch (\Exception $e) {
            $this->notify('error', 'Gagal update status: ' . $e->getMessage());
        }

    }

    protected function createLogApproval($pengaduan, $statusInfo, $filePath = null, $fileName = null)
    {
        try {
            $currentStep = $this->getCurrentStep($pengaduan);
            $fileData = $fileName ? [$fileName] : [];

            $logData = [
                'pengaduan_id' => $pengaduan->id,
                'user_id' => auth()->id(),
                'status_id' => $statusInfo->param_int,
                'status_text' => $statusInfo->data_id,
                'status' => $statusInfo->data_en,
                'catatan' => $this->catatan ?? '',
                'file' => json_encode($fileData),
                'color' => $statusInfo->param_str ?? 'gray',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            LogApproval::create($logData);

        } catch (\Exception $e) {
            \Log::error('Error creating log approval: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getCurrentStep($pengaduan)
    {
        $logCount = LogApproval::where('pengaduan_id', $pengaduan->id)->count();
        return $logCount + 1;
    }

    public function resetForm()
    {
        $this->reset(['catatan', 'file_upload', 'submission_action', 'selected_pengaduan_id', 'pengaduan_id']);
    }

    // Method updateStatus yang sesuai dengan parent class
    public function updateStatus($id, $status = null)
    {
        $record = $this->model::findOrFail($id);
        $this->selected_pengaduan_id = $id;
        $this->pengaduan_id = $id;

        // Get available status options dari combos
        $statusOptions = Combo::where('kelompok', 'sts-aduan')
            ->where('is_active', 1)
            ->orderBy('param_int')
            ->get();

        $logHistory = $this->getLogHistory($id);
        $currentStatusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $this->detailData = [
            'id' => $id,
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status Saat Ini' => $currentStatusInfo->data_id ?? 'Menunggu Review',
            'status_ex' => $currentStatusInfo->data_id ?? 'Menunggu Review',
            'user' => [
                'sts' => $statusOptions,
                'user' => $this->userInfo,
            ],
            'log' => [
                [
                    'id' => $record->code_pengaduan,
                    'judul_pengaduan' => $record->perihal,
                    'status_akhir' => $currentStatusInfo->data_id ?? 'Menunggu Review',
                    'progress' => $this->calculateProgress($record),
                    'log_approval' => $logHistory
                ]
            ],
        ];
        
        $this->detailTitle = "Update Status - " . $record->code_pengaduan;
        $this->showuUdateStatus = true;
        $this->loadUploadedFiles();
    }

    protected function getLogHistory($pengaduanId)
    {
        $logs = LogApproval::with('user')
            ->where('pengaduan_id', $pengaduanId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($logs->isEmpty()) {
            return [
                [
                    'pengaduan_id' => $pengaduanId,
                    'role' => 'Pelapor',
                    'step' => 1,
                    'nama' => $this->getNamaUser(Pengaduan::find($pengaduanId)),
                    'status' => 'new',
                    'status_text' => 'Dilaporkan',
                    'waktu' => now()->subDays(2)->format('d/m/Y H:i'),
                    'catatan' => 'Laporan awal telah disampaikan',
                    'file' => [],
                    'warna' => 'gray',
                ]
            ];
        }

        return $logs->map(function ($log, $index) {
            return [
                'pengaduan_id' => $log->pengaduan_id,
                'role' => $log->user->role ?? 'Reviewer',
                'step' => $index + 1,
                'nama' => $log->user->name ?? 'System',
                'status' => $log->status,
                'status_text' => $log->status_text,
                'waktu' => $log->created_at->format('d/m/Y H:i'),
                'catatan' => $log->catatan,
                'file' => json_decode($log->file, true) ?? [],
                'warna' => $log->color,
            ];
        })->toArray();
    }

    protected function calculateProgress($pengaduan)
    {
        $logCount = LogApproval::where('pengaduan_id', $pengaduan->id)->count();
        $totalSteps = 5;
        return min(100, (($logCount + 1) / $totalSteps) * 100);
    }

    // Method untuk mendapatkan status badge
    public function getStatusBadge($statusId)
    {
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $statusId)
            ->first();

        if (!$statusInfo) {
            $color = 'gray';
            $text = 'Menunggu Review';
        } else {
            $color = $statusInfo->param_str ?? 'gray';
            $text = $statusInfo->data_id;
        }

        return "
            <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>
                <span class='w-1.5 h-1.5 bg-{$color}-500 rounded-full mr-1.5'></span>
                {$text}
            </span>
        ";
    }

    public function getComplienProgress($record)
    {
        $progress = $this->calculateProgress($record);
        return "
            <div class='flex items-center space-x-2'>
                <div class='w-16 bg-gray-200 rounded-full h-2'>
                    <div class='bg-blue-600 h-2 rounded-full transition-all duration-500' style='width: {$progress}%'></div>
                </div>
                <span class='text-xs font-medium text-gray-700'>{$progress}%</span>
            </div>
        ";
    }

    public function getAprvCco($record)
    {
        $statusId = $record->status;
        return $this->getStatusBadge($statusId);
    }

    public function getNamaUser($record)
    {
        return $record->pelapor->name ?? $record->user->name ?? 'N/A';
    }

    public function getJenisPelanggaran($record)
    {
        return $record->jenisPengaduan->name ?? 'Tidak diketahui';
    }

    // Method untuk close modal
    public function closeModal()
    {
        $this->showuUdateStatus = false;
        $this->resetForm();
    }

    // Columns dan query method
    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function query()
    {
        $q = ($this->model)::with(['jenisPengaduan', 'pelapor', 'user']);
        
        if ($this->search && method_exists($this, 'columns')) {
            $columns = $this->columns();
            if (is_array($columns) && count($columns)) {
                $q->where(function ($p) use ($columns) {
                    foreach ($columns as $col) {
                        if ($col === 'user_id') {
                            $p->orWhereHas('pelapor', function ($q) {
                                $q->where('name', 'like', "%{$this->search}%")
                                  ->orWhere('username', 'like', "%{$this->search}%");
                            });
                        } elseif ($col === 'jenis_pengaduan_id') {
                            $p->orWhereHas('jenisPengaduan', function ($q) {
                                $q->where('name', 'like', "%{$this->search}%");
                            });
                        } else {
                            $p->orWhere($col, 'like', "%{$this->search}%");
                        }
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
                if ($key == 'status' && !empty($val)) {
                    $q->where('status', $val);
                }
            }
        }

        return $q;
    }

    // View dan comment method
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $statusInfo->data_id ?? 'Menunggu Review',
            'Status Color' => $statusInfo->param_str ?? 'gray',
            'Lokasi Kejadian' => $record->alamat_kejadian ?? '-',
            'Deskripsi' => $record->uraian ?? '-',
        ];
        
        $this->detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
        $this->showDetailModal = true;
    }

    public function comment($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::with(['comments.user'])->findOrFail($id);
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $statusInfo->data_id ?? 'Menunggu Review',
            'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
            'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
        ];
        
        $detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
        
        $this->openChat($id, $detailData, $detailTitle);
        $this->loadUploadedFiles();
    }
}