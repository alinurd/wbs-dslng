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
        // Load detail pengaduan berdasarkan code_pengaduan
        $pengaduan = Pengaduan::with([
            'jenisPengaduan', 
            'saluranAduan', 
            'pelapor',
            'logApprovals.user'
        ])->where('code_pengaduan', $this->code_pengaduan)->first();

        if (!$pengaduan) {
            abort(404, 'Pengaduan tidak ditemukan');
        }

        // Load detail pengaduan
        $this->detailPengaduan = [
            'id' => $pengaduan->id,
            'code_pengaduan' => $pengaduan->code_pengaduan,
            'perihal' => $pengaduan->perihal,
            'nama_terlapor' => $pengaduan->nama_terlapor,
            'jenis_pengaduan' => $pengaduan->jenisPengaduan->data_en ?? 'Tidak diketahui',
            'saluran_aduan' => $pengaduan->saluranAduan->nama_saluran ?? 'Tidak diketahui',
            'email_pelapor' => $pengaduan->email_pelapor,
            'telepon_pelapor' => $pengaduan->telepon_pelapor,
            'waktu_kejadian' => $pengaduan->waktu_kejadian?->format('d/m/Y H:i'),
            'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan?->format('d/m/Y H:i'),
            'uraian' => $pengaduan->uraian,
            'alamat_kejadian' => $pengaduan->alamat_kejadian,
            'status' => $this->getStatusText($pengaduan->status, $pengaduan->sts_final),
            'status_color' => $this->getStatusColor($pengaduan->status, $pengaduan->sts_final),
            'progress' => $this->progressDashboard($pengaduan->status, $pengaduan->sts_final),
            'lampiran' =>  [],
            'created_at' => $pengaduan->created_at?->format('d/m/Y H:i'),
            'updated_at' => $pengaduan->updated_at?->format('d/m/Y H:i'),
        ];

        // Load log approval data
        $this->logApprovalData = $pengaduan->logApprovals->map(function($log, $index) {
            return [
                'step' => $index + 1,
                'role' => $this->getUserRole($log->user_id),
                'nama' => $log->user->name ?? 'Unknown',
                'status' => $this->getLogStatus($log->status_text),
                'status_text' => $log->status_text,
                'waktu' => $log->created_at?->format('d/m/Y H:i'),
                'catatan' => $log->catatan,
                'file' =>  [],
                'warna' => $log->color ?? $this->getColorByStatus($log->status_text),
                'user_id' => $log->user_id
            ];
        })->toArray();

        $this->detailTitle = "Detail Pengaduan - " . $pengaduan->code_pengaduan;
    }

    protected function getStatusText($status, $sts_final)
    {
        if ($sts_final == 1) {
            return 'Selesai';
        }

        $statusMap = [
            0 => 'Menunggu',
            1 => 'Dalam Proses',
            2 => 'Diteruskan',
            3 => 'Ditolak',
            4 => 'Diterima',
            5 => 'Diproses',
            6 => 'Ditindaklanjuti',
            7 => 'Ditutup',
            8 => 'Perlu Klarifikasi'
        ];

        return $statusMap[$status] ?? 'Unknown';
    }

    protected function getStatusColor($status, $sts_final)
    {
        if ($sts_final == 1) {
            return 'green';
        }

        $colorMap = [
            0 => 'gray',
            1 => 'yellow',
            2 => 'blue',
            3 => 'red',
            4 => 'green',
            5 => 'yellow',
            6 => 'blue',
            7 => 'green',
            8 => 'orange'
        ];

        return $colorMap[$status] ?? 'gray';
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

    protected function getLogStatus($statusText)
    {
        $statusMap = [
            'Complete [Ex]' => 'completed',
            'Not Complete [Ex]' => 'rejected',
            'Complete [In]' => 'completed',
            'Not Complete [In]' => 'rejected',
            'Forward' => 'completed',
            'Sufficient' => 'completed',
            'Need further clarification' => 'in_progress',
            'Read' => 'completed'
        ];

        return $statusMap[$statusText] ?? 'pending';
    }

    protected function getColorByStatus($statusText)
    {
        $colors = [
            'Complete [Ex]' => 'green',
            'Not Complete [Ex]' => 'red',
            'Complete [In]' => 'green',
            'Not Complete [In]' => 'red',
            'Forward' => 'blue',
            'Sufficient' => 'green',
            'Need further clarification' => 'yellow',
            'Read' => 'blue'
        ];
        
        return $colors[$statusText] ?? 'gray';
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