<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
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
        $this->loadChartData();
    }

    protected function loadStats()
    {
        $currentYear = date('Y');

        $this->stats = [
            'total_pengaduan' => Pengaduan::whereYear('tanggal_pengaduan', $currentYear)->count(),
            'dalam_proses' => Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
                ->where('status','!=', 0) // 1 = dalam proses
                ->where('sts_final', 0) // 1 = dalam proses
                ->count(),
            'menunggu' => Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
                ->where('status', 0) // 1 = dalam proses
                ->count(),
            'selesai' => Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
                ->where('sts_final', 1) // 1 = selesai/final
                ->count(), 
        ];
    }

    protected function loadPengaduanTerbaru()
    {
        $pengaduan = Pengaduan::with(['jenisPengaduan', 'pelapor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->pengaduan_terbaru = $pengaduan->map(function($item, $index) {
            // Determine status color based on status and sts_final
            $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
            
            // Calculate progress based on status
            $progress = $this->calculateProgressDash($item->status, $item->sts_final);

            return [
                'id' => $item->id,
                'code_pengaduan' => $item->code_pengaduan,
                'no' => $index + 1,
                'judul' => $item->perihal ?? 'Tidak ada judul',
                'progress' => $progress,
                'tanggal' => $item->tanggal_pengaduan?->format('d/m/Y H:i') ?? '-',
                'status' => $statusInfo['text'],
                'status_color' => $statusInfo['color'],
                'jenis_pengaduan' => $item->jenisPengaduan->nama_jenis ?? '-',
                'pelapor' => $item->pelapor->name ?? 'Unknown'
            ];
        })->toArray();
    }

    protected function getStatusInfo($status, $sts_final)
    {
        if ($sts_final == 1) {
            return ['text' => 'Selesai', 'color' => 'green'];
        }

        switch ($status) {
            case 0:
                return ['text' => 'Menunggu', 'color' => 'gray'];
            case 1:
                return ['text' => 'Dalam Proses', 'color' => 'yellow'];
            case 2:
                return ['text' => 'Diteruskan', 'color' => 'blue'];
            case 3:
                return ['text' => 'Ditolak', 'color' => 'red'];
            default:
                return ['text' => 'Unknown', 'color' => 'gray'];
        }
    }

    public function calculateProgressDash($status, $sts_final)
    {
        if ($sts_final == 1) {
            return 100;
        }

        switch ($status) {
            case 0: return 10;  // Menunggu
            case 1: return 50;  // Dalam Proses
            case 2: return 75;  // Diteruskan
            case 3: return 100; // Ditolak (final)
            default: return 0;
        }
    }

    protected function loadLogApproval()
    {
        // Use log_approval table for approval logs
        $approvals = LogApproval::with(['pengaduan.jenisPengaduan', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->log_approval = $approvals->map(function($item) {
            return [
                'id' => $item->id,
                'judul' => 'Approval #' . $item->pengaduan->code_pengaduan ?? $item->pengaduan_id,
                'waktu' => $this->getTimeAgo($item->created_at),
                'deskripsi' => $item->status_text . ' - ' . ($item->catatan ?: 'Tidak ada catatan'),
                'status' => $item->status,
                'file' => !empty($item->file),
                'status_color' => $item->color ?? 'blue'
            ];
        })->toArray();

        // If no approval data, use recent pengaduan as fallback
        if (empty($this->log_approval)) {
            $recentPengaduan = Pengaduan::with(['jenisPengaduan'])
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();

            $this->log_approval = $recentPengaduan->map(function($item) {
                $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
                
                return [
                    'id' => $item->id,
                    'judul' => 'Update ' . ($item->jenisPengaduan->nama_jenis ?? 'Pengaduan') . ' #' . $item->code_pengaduan,
                    'waktu' => $this->getTimeAgo($item->updated_at),
                    'deskripsi' => 'Status: ' . $statusInfo['text'],
                    // 'komentar' => '0 komentar',
                    'file' => !empty($item->lampiran),
                    'status_color' => $statusInfo['color']
                ];
            })->toArray();
        }
    }

    protected function loadProgressBulanan()
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

    protected function loadChartData()
    {
        $currentYear = date('Y');
        
        // Get monthly data for current year
        $monthlyData = Pengaduan::select(
                DB::raw('MONTH(tanggal_pengaduan) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN sts_final = 1 THEN 1 ELSE 0 END) as completed")
            )
            ->whereYear('tanggal_pengaduan', $currentYear)
            ->groupBy(DB::raw('MONTH(tanggal_pengaduan)'))
            ->orderBy('month')
            ->get();

        $months = [];
        $totals = [];
        $completed = [];

        // Initialize all months with 0
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;
            
            $monthData = $monthlyData->firstWhere('month', $i);
            $totals[] = $monthData ? $monthData->total : 0;
            $completed[] = $monthData ? $monthData->completed : 0;
        }

        $this->chartData = [
            'months' => $months,
            'totals' => $totals,
            'completed' => $completed
        ];
    }

    protected function getTimeAgo($datetime)
    {
        if (!$datetime) return 'Baru saja';
        
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' menit lalu';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' jam lalu';
        } else {
            $days = floor($diff / 86400);
            return $days . ' hari lalu';
        }
    }

    public function query()
    {
        $q = ($this->model)::with(['jenisPengaduan', 'user']);
        
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

    // Refresh dashboard data
    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('show-toast', type: 'success', message: 'Dashboard data diperbarui');
    }

    // Get status distribution for chart
    public function getStatusDistribution()
    {
        $currentYear = date('Y');
        
        return Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                $statusText = $this->getStatusInfo($item->status, 0)['text'];
                return [$statusText => $item->count];
            })
            ->toArray();
    }
}