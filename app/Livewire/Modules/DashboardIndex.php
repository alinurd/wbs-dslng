<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\JenisPengaduan;
use App\Models\LogApproval;
use App\Models\Pengaduan;
use App\Models\SaluranAduan;
use App\Models\User;
use App\Traits\HasChat;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class DashboardIndex extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'dashboard';
    public $model = Pengaduan::class;
    public $views = 'modules.dashboard';
    public $title = "Dashboard";
    public $catatan = '';
    public $file_upload;
    
    // Properties untuk detail
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];

    // Properties untuk data dashboard
    public $stats = [];
    public $pengaduan_terbaru = [];
    public $log_approval = [];
    public $progress_bulanan = [];
    public $chartData = [];

    public function mount()
    {
        parent::mount(); 
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->loadStats();
        $this->loadPengaduanTerbaru();
        $this->loadLogApproval();
        $this->loadProgressBulanan();
    }

   protected function loadStats()
{
    $currentYear = date('Y');
     
    $baseQuery = Pengaduan::whereYear('tanggal_pengaduan', $currentYear);
    
    if ($this->pelapor) {
        $baseQuery->where('user_id', $this->userInfo['user']['id'] ?? null);
    }

    $totalPengaduan = $baseQuery->count();
    $dalamProses = (clone $baseQuery)->where('status','!=', 0)->where('sts_final', 0)->count();
    $selesai = (clone $baseQuery)->where('sts_final', 1)->count();
    $menunggu = (clone $baseQuery)->where('status', 0)->where('sts_final', 0)->count();

    $this->stats = [
        'total_pengaduan' => $totalPengaduan,
        'dalam_proses' => $dalamProses,
        'selesai' => $selesai,
        'menunggu' => $menunggu
    ];
}

public function getDataByUser()
{
    return $this->buildBaseQuery()->limit(5)->get();
}



   protected function loadPengaduanTerbaru()
{
   $pengaduan = Pengaduan::with(['jenisPengaduan', 'pelapor', 'logApprovals'])
        ->orderBy('created_at', 'desc');

    if ($this->pelapor) {
        $pengaduan->where('user_id', $this->userInfo['user']['id'] ?? null);
    }

    $pengaduan = $pengaduan->limit(5)->get();
       
    $this->pengaduan_terbaru = $pengaduan->map(function($item, $index) {
        $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
                $counts = $this->countComentFileByPengaduan($item->id);
                
        return [
            'id' => $item->id,
            'code_pengaduan' => $item->code_pengaduan,
            'no' => $index + 1,
            'judul' => $item->perihal ?? 'Tidak ada judul',
            'progress' => $this->progressDashboard($item->status, $item->sts_final),
            'tanggal' => $item->created_at?->format('d/m/Y H:i') ?? '-',
            'status' => $statusInfo['text'],
            'status_color' => $statusInfo['color'],
            'jenis_pengaduan' => $item->jenisPengaduan->data_id ?? '-',
            'pelapor' => $item->user->name ?? 'Unknown',
    
            'countComment' => $counts['comments'] . ' komentar',
            'countFile' => $counts['files'] . ' file',
            'countAktivitas' => $counts['aktivitas'] . ' aktivitas',
        ];
    })->toArray();
}

    protected function loadLogApproval()
{
   
    $query = LogApproval::with(['pengaduan.jenisPengaduan', 'user'])
        ->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                  ->from('log_approval')
                  ->groupBy('pengaduan_id');
        })
        ->orderBy('created_at', 'desc')
        ->limit(5);

    // Jika pelapor, filter by user_id melalui relasi pengaduan
    if ($this->pelapor && isset($this->userInfo['user']['id'])) {
        $query->whereHas('pengaduan', function($q) {
            $q->where('user_id', $this->userInfo['user']['id']);
        });
    }

    $latestLogs = $query->get();
    

    $this->log_approval = $latestLogs->map(function($item) {
        $catatan = $item->catatan ?: 'Tidak ada catatan';
        
        $truncatedCatatan = strlen($catatan) > 60 
            ? substr($catatan, 0, 60) . '...' 
            : $catatan;
        
        $counts = $this->countComentFileByPengaduan($item->pengaduan_id);
         
        return [
            'id' => $item->id,
            'pengaduan_id' => $item->pengaduan_id,
            'code' => '#' . ($item->pengaduan->code_pengaduan ?? $item->pengaduan_id),
            'waktu' => $this->getTimeAgo($item->created_at),
            'catatan' => $truncatedCatatan,
            'catatan_full' => $catatan, 
            'countComment' => $counts['comments'] . ' komentar',
            'countFile' => $counts['files'] . ' file',
            'file' => $item->file ?? json_decode($item->file, true) ?? [],
            'status_color' => $item->color ?? 'blue',
            'user_name' => $item->user->name ?? 'Unknown',
            'status' => $item->status_text
        ];
    })->toArray();

    if (empty($this->log_approval)) {
        $this->loadRecentPengaduanAsLog();
    }
}


    protected function loadRecentPengaduanAsLog()
    {
        $recentPengaduan = Pengaduan::with(['jenisPengaduan', 'pelapor', 'logApprovals'])
        ->orderBy('created_at', 'desc');

    if ($this->pelapor) {
        $recentPengaduan->where('user_id', $this->userInfo['user']['id'] ?? null);
    }

    $recentPengaduan = $recentPengaduan->limit(3)->get();

       

        $this->log_approval = $recentPengaduan->map(function($item) {
            $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
            
            return [
                'id' => $item->id,
                'pengaduan_id' => $item->id,
                'judul' => 'Update ' . ($item->jenisPengaduan->nama_jenis ?? 'Pengaduan') . ' #' . $item->code_pengaduan,
                'waktu' => $this->getTimeAgo($item->updated_at),
                'deskripsi' =>  ($item->perihal ?? ''),
                'komentar' => '0 komentar',
                'file' => !empty($item->lampiran) && $item->lampiran != '[]',
                'status_color' => $statusInfo['color'],
                'user_name' => 'System',
                'status' => $statusInfo['text']
            ];
        })->toArray();
    }

    protected function loadProgressBulanan_old()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalBulanan = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->whereMonth('tanggal_pengaduan', $currentMonth)
            ->count();

        if ($totalBulanan > 0) {
            $menunggu = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
                ->whereMonth('tanggal_pengaduan', $currentMonth)
                ->where('status', 0)
                ->where('sts_final', 0)
                ->count();

            $dalamProses = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
                ->whereMonth('tanggal_pengaduan', $currentMonth)
                ->where('status', 1)
                ->where('sts_final', 0)
                ->count();

            $selesai = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
                ->whereMonth('tanggal_pengaduan', $currentMonth)
                ->where('sts_final', 1)
                ->count();

            $this->progress_bulanan = [
                [
                    'label' => 'Menunggu',
                    'jumlah' => $menunggu,
                    'persentase' => $totalBulanan > 0 ? round(($menunggu / $totalBulanan) * 100) : 0,
                    'color' => 'gray'
                ],
                [
                    'label' => 'Dalam Proses',
                    'jumlah' => $dalamProses,
                    'persentase' => $totalBulanan > 0 ? round(($dalamProses / $totalBulanan) * 100) : 0,
                    'color' => 'yellow'
                ],
                [
                    'label' => 'Selesai',
                    'jumlah' => $selesai,
                    'persentase' => $totalBulanan > 0 ? round(($selesai / $totalBulanan) * 100) : 0,
                    'color' => 'green'
                ]
            ];
        } else {
            $this->progress_bulanan = [
                ['label' => 'Menunggu', 'jumlah' => 0, 'persentase' => 0, 'color' => 'gray'],
                ['label' => 'Dalam Proses', 'jumlah' => 0, 'persentase' => 0, 'color' => 'yellow'],
                ['label' => 'Selesai', 'jumlah' => 0, 'persentase' => 0, 'color' => 'green']
            ];
        }
    }


    protected function loadProgressBulanan()
{
    $currentMonth = date('m');
    $currentYear = date('Y');
    $query = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
        ->whereMonth('tanggal_pengaduan', $currentMonth);
    if ($this->pelapor) {
        $query->where('user_id', $this->userInfo['user']['id'] ?? null);
    }
    $menunggu = (clone $query)->where('status', 0)->where('sts_final', 0)->count();
    $dalamProses = (clone $query)->where('status', 1)->where('sts_final', 0)->count();
    $selesai = (clone $query)->where('sts_final', 1)->count();

    $totalBulanan = $menunggu + $dalamProses + $selesai;

    if ($totalBulanan > 0) {
        $this->progress_bulanan = [
            [
                'label' => 'Menunggu',
                'jumlah' => $menunggu,
                'persentase' => round(($menunggu / $totalBulanan) * 100),
                'color' => 'gray'
            ],
            [
                'label' => 'Dalam Proses',
                'jumlah' => $dalamProses,
                'persentase' => round(($dalamProses / $totalBulanan) * 100),
                'color' => 'yellow'
            ],
            [
                'label' => 'Selesai',
                'jumlah' => $selesai,
                'persentase' => round(($selesai / $totalBulanan) * 100),
                'color' => 'green'
            ]
        ];
    } else {
        $this->progress_bulanan = [
            ['label' => 'Menunggu', 'jumlah' => 0, 'persentase' => 0, 'color' => 'gray'],
            ['label' => 'Dalam Proses', 'jumlah' => 0, 'persentase' => 0, 'color' => 'yellow'],
            ['label' => 'Selesai', 'jumlah' => 0, 'persentase' => 0, 'color' => 'green']
        ];
    }
}
    
    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('show-toast', type: 'success', message: 'Dashboard data diperbarui');
    }
}