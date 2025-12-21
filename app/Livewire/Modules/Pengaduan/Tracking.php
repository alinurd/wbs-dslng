<?php

namespace App\Livewire\Modules\Pengaduan;

use App\Livewire\Root;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class Tracking extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'p_tracking';
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.tracking';
    public $title = "Lacak Aduan";

    // Properties untuk detail (dari HasChat sudah include chat properties)
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload di sidebar (spesifik untuk Tracking)
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];

    public function columns()
    {
        return ['code_pengaduan', 'user_id', 'tanggal_pengaduan', 'jenis_pengaduan_id'];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
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
                $stsGet = [-1 , 0];
        }
        // Apply status filters
        if ($roleId == 4) {
            $q->whereNotIn('status', [0, 10]);
        } elseif ($stsGet !== 'all') {
            $q->whereIn('status', $stsGet);
        }
        return $q;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul) . '.view']); 
            $this->getPengaduanById($id);
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

    public function comment($id)
    {
        can_any([strtolower($this->modul) . '.view']);

        $record = $this->model::with(['comments.user'])->findOrFail($id);

        $detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            // 'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenisPengaduan->name ?? 'Tidak diketahui',
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
            'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
            'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
            //  'status_ex' => [
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

        $this->loadUploadedFiles();
    }
}
