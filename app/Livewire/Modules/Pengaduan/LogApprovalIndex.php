<?php

namespace App\Livewire\Modules\Pengaduan;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\JenisPengaduan;
use App\Models\LogApproval;
use App\Models\Pengaduan;
use App\Models\SaluranAduan;
use App\Traits\HasChat;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class LogApprovalIndex extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'p_tracking';
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.log-approval';
    public $title = "Lacak Aduan";
    public $code_pengaduan;

    // Properties untuk detail pengaduan
    public $detailPengaduan = [];
    public $logApprovalData = [];
    public $detailTitle = '';

    // Properties untuk file upload di sidebar
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];

    public function mount($code_pengaduan = null)
{ 
    
    if ($this->code_pengaduan) {
        $this->loadPengaduanDetail();
    }
    
    $this->loadDropdownData();
}


   public function loadPengaduanDetail()
    {
         $pengaduan = Pengaduan::with([
            'jenisPengaduan', 
            'saluranAduan', 
            'pelapor',
            'logApprovals'
        ])->where('code_pengaduan', $this->code_pengaduan)->first();

        if (!$pengaduan) {
            abort(404, 'Pengaduan tidak ditemukan');
        }
       $statusInfo = $this->getStatusInfo($pengaduan->status, $pengaduan->sts_final);
        $this->detailPengaduan = [
            'id' => $pengaduan->id,
            'code_pengaduan' => $pengaduan->code_pengaduan,
            'perihal' => $pengaduan->perihal,
            'nama_terlapor' => $pengaduan->nama_terlapor,
            'jenis_pengaduan' => $pengaduan->jenisPengaduan->data_id ?? 'Tidak diketahui',
            'saluran_aduan' => $pengaduan->saluranAduan->data_id ?? 'Tidak diketahui',
            'email_pelapor' => $pengaduan->email_pelapor,
            'telepon_pelapor' => $pengaduan->telepon_pelapor,
            'waktu_kejadian' => $pengaduan->waktu_kejadian?->format('d/m/Y H:i'),
            'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan?->format('d/m/Y H:i'),
            'uraian' => $pengaduan->uraian,
            'alamat_kejadian' => $pengaduan->alamat_kejadian,
            'status' => $statusInfo['text'],
            'status_color' => $statusInfo['color'], 
            'progress' => $this->progressDashboard($pengaduan->status, $pengaduan->sts_final),
            'lampiran' =>  $pengaduan->lampiran ?? json_decode($pengaduan->lampiran, true) ?? [],
            'created_at' => $pengaduan->created_at?->format('d/m/Y H:i'),
            'updated_at' => $pengaduan->updated_at?->format('d/m/Y H:i'),
        ];

 
        $this->logApprovalData = $pengaduan->logApprovals->sortByDesc('id')->map(function($item, $index) {

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
            'role' => $this->getUserRole($item->user_id), 
            'status' => $item->status_text,
            'infoSts' => $this->getStatusInfo($item->status_id, 0)
        ];
        })->toArray();

        $this->detailTitle = "Detail Pengaduan - " . $pengaduan->code_pengaduan;
    }

      
     
    protected function getUserRole($userId)
    {
        // Anda bisa menyesuaikan dengan logic role user yang sesuai
        $roleMap = [
            5 => 'WBS Eksternal',
            10 => 'WBS Internal',
            12 => 'WBS Forward',
            13 => 'CCO'
        ];

        return $roleMap[$userId] ?? 'User';
    }



    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function filterDefault()
    {
        return [
            ['f' => 'user_id', 'v' => auth()->id()],
        ];
    }

    public function query()
    {
        $q = ($this->model)::query();

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
                    $q->whereRaw('YEAR(tanggal_pengaduan) = ?', [$val]);
                }
                if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                    $q->where('jenis_pengaduan_id', $val);
                }
            }
        }

        return $q;
    }

    public function comment($id)
    {
        can_any([strtolower($this->modul) . '.view']);

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

    // Refresh data
    public function refreshData()
    {
        if ($this->code_pengaduan) {
            $this->loadPengaduanDetail();
        }
        $this->dispatch('show-toast', type: 'success', message: 'Data diperbarui');
    }
}