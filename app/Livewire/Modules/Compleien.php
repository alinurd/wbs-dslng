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

    // Properties untuk forward
    public $showForwardDropdown = false;
    public $forwardDestination = '';
    public $forwardPengaduanId = '';

    protected $listeners = ['openDetailModal' => 'openModal'];

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

    // Method untuk forward
 

     
  

  public function submitForm()
{
    try {
        // Debug: cek data masuk
        \Log::info('submitForm called', [
            'submission_action' => $this->submission_action,
            'selected_pengaduan_id' => $this->selected_pengaduan_id,
            'has_catatan' => !empty($this->catatan),
            'has_file' => !empty($this->file_upload)
        ]);

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

            \Log::info('Status info found', [
                'statusInfo' => $statusInfo,
                'submission_action' => $this->submission_action
            ]);

            if ($statusInfo) {
              $updateData = [
    'status' => $this->submission_action,
    'fwd_to' => ($this->submission_action == 5) ? $this->forwardDestination : null,
    'sts_final' => in_array($this->submission_action, [3, 6, 7]) ? 1 : 0,
    'updated_at' => now(),
];

                // Add catatan if provided
                if (!empty($this->catatan)) {
                    $updateData['catatan'] = $this->catatan;
                }

                \Log::info('Updating pengaduan', [
                    'pengaduan_id' => $pengaduan->id,
                    'updateData' => $updateData
                ]);

                $pengaduan->update($updateData);

                // Create log approval
                $this->createLogApproval($pengaduan, $statusInfo, $filePath, $fileName);
                
                \Log::info('Pengaduan updated successfully');
            } else {
                \Log::error('Status info not found for action: ' . $this->submission_action);
                $this->notify('error', 'Status tidak valid: ' . $this->submission_action);
                return;
            }
        } else {
            \Log::error('Pengaduan not found: ' . $this->selected_pengaduan_id);
            $this->notify('error', 'Pengaduan tidak ditemukan!');
            return;
        }

        $this->notify('success', 'Status pengaduan berhasil diupdate!');
        $this->showuUdateStatus = false;

        // Reset semua form dan forward dropdown
        $this->resetForm();
        $this->hideForwardDropdown();

    } catch (\Exception $e) {
        \Log::error('Error in submitForm: ' . $e->getMessage());
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

    public function updateStatus($id, $status = null)
    {
        $record = $this->model::findOrFail($id);
        $this->selected_pengaduan_id = $id;
        $this->pengaduan_id = $id;

        // Reset forward dropdown ketika membuka modal baru
        $this->hideForwardDropdown();

        $logHistory = $this->getLogHistory($id);
        $currentStatusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        // Get available status options untuk user saat ini
        $statusOptions = Combo::where('kelompok', 'sts-aduan')
            ->where('is_active', 1)
            ->orderBy('param_int')
            ->get();

        $this->detailData = [
            'id' => $id,
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status Saat Ini' => $currentStatusInfo->data_id ?? 'Menunggu Review',
            'status_ex' => [
                'name' => $currentStatusInfo->data_id ?? 'Menunggu Review',
                'color' => $currentStatusInfo->param_str ?? 'yellow',
            ],
            'status_id' => $record->status,
            'user' => $this->userInfo,
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
                'role' => $log->user->getRoleNames()->implode(', ') ?? 'Reviewer',
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
        return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500'>
                 <i class='fas fa-check' ></i>
            </span>" ;
    }

    public function getAprvCco($record)
    {
        $sts = $this->getStatusBadge($record->status);
        if($record->sts_final == 0 && $record->status !== 3){
            $sts .= $this->getStatusBadge(12);
        }
        return $sts;
    }

    public function getNamaUser($record)
    {
        return $record->pelapor->name ?? $record->user->name ?? 'N/A';
    }


    // Method untuk close modal
    public function closeModal()
    {
        $this->showuUdateStatus = false;
        $this->resetForm();
        $this->hideForwardDropdown();
    }

    // Columns dan query method
    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function query()
    {
        $q = ($this->model)::with(['jenisPengaduan', 'pelapor']);
        
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

        // Filter berdasarkan role user
        $roleId = (int)($this->userInfo['role']['id'] ?? 0);
        
        switch($roleId){
            case 2: // WBS External
                $stsGet = [0, 6, 10];
                break;
            case 4: // WBS Internal  
                $stsGet = [6, 7, 9, 11];
                break;
            case 5: // WBS CC
                $stsGet = [7, 1, 9];
                break; 
            case 7: // WBS CCO
                $stsGet = [1, 3, 8];
                break;
            default:
                $stsGet = [-1];
        }

        $q->whereIn('status', $stsGet);
                
        return $q;
    }

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



    // DI CLASS COMPLEIEN - PASTIKAN METHOD INI ADA
public function ShowFWD($pengaduanId)
{
            // $this->notify('error', 'ShowFWD');
// $forwardDestination=true;    
    $this->showForwardDropdown = true;
    $this->forwardPengaduanId = $pengaduanId;
    $this->selected_pengaduan_id = $pengaduanId;
    $this->pengaduan_id = $pengaduanId;
}

public function hideForwardDropdown()
{
    $this->showForwardDropdown = false;
    $this->forwardDestination = '';
    $this->forwardPengaduanId = '';
}

public function setActionWithForward($action, $id = null)
// DI CLASS COMPLEIEN - PERBAIKI METHOD setActionWithForward
 {
    // Debug: cek apakah method dipanggil
    \Log::info('setActionWithForward called', [
        'action' => $action,
        'id' => $id,
        'forwardDestination' => $this->forwardDestination
    ]);

    // Validasi jika forward destination belum dipilih
    if ($action == 5 && empty($this->forwardDestination)) {
        $this->notify('error', 'Silakan pilih tujuan forward terlebih dahulu!');
        return;
    }

    // Set properties dengan benar
    $this->submission_action = $action;
    
    if ($id) {
        $this->selected_pengaduan_id = $id;
        $this->pengaduan_id = $id;
    }
    
    // Jika action adalah forward (5), tambahkan catatan otomatis
    if ($action == 5 && !empty($this->forwardDestination)) {
        $destinationText = $this->getDestinationText($this->forwardDestination);
        $existingCatatan = $this->catatan ?? '';
        $this->catatan = "Dialihkan ke: " . $destinationText . "\n\n" . $existingCatatan;
    }

    // Debug: cek data sebelum submit
    \Log::info('Before submitForm', [
        'submission_action' => $this->submission_action,
        'selected_pengaduan_id' => $this->selected_pengaduan_id,
        'forwardDestination' => $this->forwardDestination
    ]);

    // Submit form - PASTIKAN ini dieksekusi
    $this->submitForm();
    
    // Reset dropdown setelah submit
    $this->hideForwardDropdown();
}

// DI CLASS COMPLEIEN - PERBAIKI METHOD getDestinationText
protected function getDestinationText($destination)
{
    // Ambil data dari combos untuk dropdown forward
    $forwardOptions = Combo::where('kelompok', 'wbs-forward')
        ->where('is_active', true)
        ->orderBy('data_id')
        ->get();
    
    // Cari teks berdasarkan value
    $option = $forwardOptions->firstWhere('data_en', $destination);
    
    return $option ? $option->data_id : $destination;
}



}