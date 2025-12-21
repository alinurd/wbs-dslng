<?php

namespace App\Livewire\Modules;

use App\Helpers\FileHelper;
use App\Livewire\Root;

use App\Models\Audit as AuditLog;
use App\Models\Combo;
use App\Models\LogApproval;
use App\Models\Pengaduan;
use App\Services\PengaduanEmailService;
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
    public $submission_action = '';
    public $selected_pengaduan_id = '';
    public $pengaduan_id = '';

    // Properties untuk detail modal 
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload di sidebar
    public $fileDescription = '';
    public $lampiran = [];

    // Properties untuk forward
    public $showForwardDropdown = false;
    public $forwardDestination = '';
    public $forwardPengaduanId = '';

    protected $listeners = ['openDetailModal' => 'openModal'];

    protected function rules()
    {
        return [

            'catatan' => 'required|min:10',
            'lampiran.*' => 'max:' . (FileHelper::getMaxPengaduanSize() * 1024) . '|mimes:' . implode(',', FileHelper::getAllowedPengaduanExtensions()),

        ];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
        $this->userInfo();
    }

    public function removeFile()
    {
        $this->reset('lampiran');
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
                'has_file' => !empty($this->lampiran)
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

            $filePath = [];
            if ($this->lampiran && count($this->lampiran) > 0) {
                $filePath = FileHelper::uploadMultiple(
                    $this->lampiran,
                    'pengaduan/lampiran',
                    'public'
                );
            }


            // Update pengaduan
            $pengaduan = Pengaduan::with('pelapor')->find($this->selected_pengaduan_id);

            if ($pengaduan) {
                $dataOld = [
                    'status' => $pengaduan->status,
                    'fwd_to' => $pengaduan->fwd_to,
                    'sts_fwd' => $pengaduan->sts_fwd,
                    'sts_final' => $pengaduan->sts_final,
                    'updated_at' => $pengaduan->updated_at,
                ];

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
                        'fwd_to' => ($this->submission_action == 5 && $this->forwardDestination != 0 && !$pengaduan['fwd_to'])
                            ? $this->forwardDestination
                            : $pengaduan['fwd_to'],
                        'sts_fwd' => ($this->submission_action == 2 && $this->forwardDestination !== 0)
                            ? 1
                            : (($this->submission_action == 5) ? 0 : $pengaduan['sts_fwd']),

                        'sts_final' => in_array($this->submission_action, [3, 6, 7]) ? 1 : 0,
                        'updated_at' => now(),
                    ];

                    $roleId = (int)($this->userInfo['role']['id'] ?? 0);
                    if ($roleId == 5) {
                        $updateData['act_cc'] = 1;
                    }
                    if ($roleId == 7) {
                        $updateData['act_cco'] = 1;
                    }
                    // Add catatan if provided
                    if (!empty($this->catatan)) {
                        $updateData['catatan'] = $this->catatan;
                    }

                    \Log::info('Updating pengaduan', [
                        'pengaduan_id' => $pengaduan->id,
                        'updateData' => $updateData
                    ]);

                    $pengaduan->update($updateData);

                    AuditLog::create([
                        'user_id' => $this->userInfo['user']['id'],
                        'action' => 'updStatus',
                        'table_name' => 'Complien',
                        'record_id' => $pengaduan->id,
                        'old_values' => json_encode($dataOld),
                        'new_values' =>  json_encode($updateData),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'created_at' => now()
                    ]);

                    // dd($pengaduan);
$emailService = new PengaduanEmailService();
$emailService->handleStatusChange(
    $pengaduan,                    // Object pengaduan
    $this->submission_action,      // Status action (6, 10, 7, dll)
    $roleId,                       // Role ID user yang melakukan aksi
    $this->catatan,                // Catatan (opsional)
    ($this->forwardDestination??0),      // Forward destination (opsional),
    auth()->id() //user yang melakukan action
);
                    // Create log approval
                    $this->createLogApproval($pengaduan, $statusInfo, $filePath);

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

    protected function createLogApproval($pengaduan, $statusInfo, $filePath = null)
    {
        try {
            $logData = [
                'pengaduan_id' => $pengaduan->id,
                'user_id' => auth()->id(),
                'status_id' => $statusInfo->param_int,
                'status_text' => $statusInfo->data_id,
                'status' => $statusInfo->data_en,
                'catatan' => $this->catatan ?? '',
                'file' => json_encode($filePath),
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
        $this->reset(['catatan', 'lampiran', 'submission_action', 'selected_pengaduan_id', 'pengaduan_id']);
    }

    public function updateStatus($id, $status = null)
    {
        $record = $this->model::with(['jenisPengaduan'])->orderBy('created_at', 'desc')->findOrFail($id);
        $this->selected_pengaduan_id = $id;
        $this->pengaduan_id = $id;

        $this->hideForwardDropdown();

        $logHistory = $this->getLogHistory($id);
        $currentStatusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $act_int = ( $record->act_cco) == 1 ? false : true;
        // $act_int = ($record->act_cc || $record->act_cco) == 1 ? false : true;
        $this->detailData = [
            'id' => $id,
            'Kode Tracking' => $record->code_pengaduan,
            // 'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status Saat Ini' => $currentStatusInfo->data_id ?? 'Menunggu Review',
            'status_ex' => [
                'name' => $currentStatusInfo->data_id ?? 'Menunggu Review',
                'color' => $currentStatusInfo->param_str ?? 'yellow',
            ],
            'status_id' => $record->status,
            'act_cc' => $record->act_cc,
            // 'act_int' => $act_int,
            'act_int' => $act_int,
            'act_cco' => $record->act_cco,
            'sts_fwd' => [
                'id' => $record->sts_fwd,
                'data' => $this->getStatusInfo(2, 0)
            ],
            'user' => $this->userInfo,
            'log' => [
                [
                    'id' => $record->id,
                    'code' => $record->code_pengaduan,
                    'jenis_pengaduan' => $record->jenisPengaduan->data_id ?? 'Tidak diketahui',
                    'status_akhir' => $currentStatusInfo->data_id ?? 'Menunggu Review',
                    'progress' => $this->calculateProgress($record),
                    'log_approval' => $logHistory,


                ]
            ],
        ];

        $this->detailTitle = "Update Status - " . $record->code_pengaduan;
        $this->showuUdateStatus = true;
        $this->uploadFile();
    }

    protected function getLogHistory($pengaduanId)
    {
        $logs = LogApproval::with('user')
            ->where('pengaduan_id', $pengaduanId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($logs->isEmpty()) {
            return [
                [
                    'pengaduan_id' => $pengaduanId,
                    'role' => 'Pelapor',
                    'step' => 1,
                    'user_name' => $this->getNamaUser(Pengaduan::find($pengaduanId)),
                    'status' => 'new',
                    'status_text' => 'Dilaporkan',
                    'waktu' => now()->subDays(2)->format('d/m/Y H:i'),
                    'catatan' => 'Laporan awal telah disampaikan',
                    'file' => [],
                    'warna' => 'gray',
                    'infoSts' => $this->getStatusInfo(0, 0),
                    'status_color' => 'gray',
                ]
            ];
        }

        return $logs->map(function ($item, $index) {
            $catatan = $item->catatan ?: 'Tidak ada catatan';

            $truncatedCatatan = strlen($catatan) > 60
                ? substr($catatan, 0, 60) . '...'
                : $catatan;
            return [
                'id' => $item->id,
                'pengaduan_id' => $item->pengaduan_id,
                'code' => '#' . ($item->pengaduan->code_pengaduan ?? $item->pengaduan_id),
                'waktu' => $this->getTimeAgo($item->created_at),
                'catatan' => $truncatedCatatan,
                'catatan_full' => $catatan,
                'file' => $item->file ?? json_decode($item->file, true) ?? [],
                'status_color' => $item->color ?? 'blue',
                'user_name' => $item->user->name ?? 'Unknown',
                'role' => $item->user->getRoleNames()->first() ?? 'Unknown',
                'status' => $item->status_text,
                'infoSts' => $this->getStatusInfo($item->status_id, 0)
            ];
        })->toArray();
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

     
    public function filterDefault()
    {
        
        // \dd($this->userInfo['role']['id']);
        $filterArray = [];
if ($this->userInfo['role']['id'] === 3) {
    $filterArray[] = ['f' => 'user_id', 'v' => auth()->id()];
}

return $filterArray;

        // // dd($this->pelapor); == true maka jalankan filter user_id
        //  return [
        //     ['f' => 'user_id', 'v' => auth()->id()],
        // ];
    }
    
    // Columns dan query method
  public function columns()
    {
        return ['code_pengaduan', 'user_id', 'tanggal_pengaduan', 'jenis_pengaduan_id'];
    }

   public function query()
    {
        $q = ($this->model)::query();

            $q->with(['pelapor', 'jenisPengaduan']);
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
                             $q->where('data_id', 'like', "%{$this->search}%")
                              ->orWhere('data_en', 'like', "%{$this->search}%");                        
                            });
                    } else {
                        $p->orWhere($col, 'like', "%{$this->search}%");
                    }
                }
            });
        }
    }
    
        if (method_exists($this, 'filterDefault')) {
            $filterDefault = $this->filterDefault();
            if (is_array($filterDefault) && count($filterDefault)) {
                $q->where(function ($q) use ($filterDefault) {
                    foreach ($filterDefault as $col) {
                        if (!empty($col['f'])) {
                            $q->Where($col['f'], $col['v']);
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

     $roleId = (int)($this->userInfo['role']['id'] ?? 0);
        switch ($roleId) {
            case 2: // WBS External
                $stsGet = 'all';
                break;
            case 4: // WBS Internal  
                $stsGet = 'all';
                break;
            case 5: // WBS CC
                $stsGet = [7,    3];

                // $q->where(function ($query) use ($stsGet) {
                //     $query->where('status', 7);
                //     // $query->orWhere(function ($subQuery) {
                //     //     $subQuery->whereIn('status', [  9,])
                //     //         ->where('act_cc', 1);
                //     // });
                // });
                break;
                break;
            case 7: // WBS CCO
                $stsGet = [1, 3, 8];
                break;
            case 6: // WBS FWD
                $stsGet = [5, 2];
                $q->where(function ($query) {
                    $query->where('fwd_to', $this->userInfo['user']['fwd_id'])
                        ->orWhere('sts_fwd', 1);
                });
                break;
            default:
                $stsGet = [-1,0]; // Tidak akan pernah match
        }
        // Apply status filters
        if ($roleId == 4) {
            $q->whereNotIn('status', [0, 10]);
        } elseif ($stsGet !== 'all') {
            $q->whereIn('status', $stsGet);
        }


        return $q;
    }


    public function closeViewDetail()
    {
        $this->showDetailModal1 = false;
        $this->updateStatus($this->pengaduan_id, $status = null);
    }
    public function viewDetail($id)
    {
        can_any([strtolower($this->modul) . '.view']);
        $this->getPengaduanById($id);
        $this->showDetailModal1 = true;
        $this->showuUdateStatus = false;
    }
    public function view($id)
    {
        can_any([strtolower($this->modul) . '.view']);
        $this->pengaduan_id = $id;
        $this->getPengaduanById($id);
        $this->showDetailModal = true;
    }

    public function comment($id)
    {
        can_any([strtolower($this->modul) . '.view']);

        $record = $this->model::with(['comments.user'])->findOrFail($id);
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();

        $detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            // 'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $statusInfo->data_id ?? 'Menunggu Review',
            'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
            'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
            // 'status_ex' => [
            //     'name' => $statusInfo->data_id ?? 'Menunggu Review',
            //     'color' => $statusInfo->param_str ?? 'yellow',
            // ],
            //  'sts_fwd' => [
            //     'id' => $record->sts_fwd,
            //     'data' => $this->getStatusInfo(2, 0)
            // ],
        ];

        $detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;

        $this->openChat($id, $detailData, $detailTitle, $record->code_pengaduan);
        $this->uploadFile();
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
    {
        \Log::info('setActionWithForward called', [
            'action' => $action,
            'id' => $id,
            'forwardDestination' => $this->forwardDestination
        ]);

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
