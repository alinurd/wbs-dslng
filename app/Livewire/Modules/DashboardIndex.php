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
    }

    protected function loadStats()
    {
        $currentYear = date('Y');

        $totalPengaduan = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)->count();
        $dalamProses = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->where('status','!=', 0)
            ->where('sts_final', 0)
            ->count();
        $selesai = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->where('sts_final', 1)
            ->count();
        $menunggu = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->where('status', 0)
            ->where('sts_final', 0)
            ->count();

        $this->stats = [
            'total_pengaduan' => $totalPengaduan,
            'dalam_proses' => $dalamProses,
            'selesai' => $selesai,
            'menunggu' => $menunggu
        ];
        // \dd($this->stats);
    }

    protected function loadPengaduanTerbaru()
    {
        $pengaduan = Pengaduan::with(['jenisPengaduan', 'pelapor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->pengaduan_terbaru = $pengaduan->map(function($item, $index) {
            $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
            
            return [
                'id' => $item->id,
                'code_pengaduan' => $item->code_pengaduan,
                'no' => $index + 1,
                'judul' => $item->perihal ?? 'Tidak ada judul',
                'progress' => $this->progressDashboard($item->status, $item->sts_final),
                'tanggal' => $item->created_at?->format('d/m/Y H:i') ?? '-',
                'status' => $statusInfo['text'],
                'status_color' => $statusInfo['color'],
                'jenis_pengaduan' => $item->jenisPengaduan->nama_jenis ?? '-',
                'pelapor' => $item->user->name ?? 'Unknown'
            ];
        })->toArray();
    }

    protected function loadLogApproval()
    {
        // Get the latest log approval for each unique pengaduan
        $latestLogs = LogApproval::with(['pengaduan.jenisPengaduan', 'user'])
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('log_approval')
                      ->groupBy('pengaduan_id');
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->log_approval = $latestLogs->map(function($item) {
            return [
                'id' => $item->id,
                'pengaduan_id' => $item->pengaduan_id,
                'judul' => 'Approval #' . ($item->pengaduan->code_pengaduan ?? $item->pengaduan_id),
                'waktu' => $this->getTimeAgo($item->created_at),
                'deskripsi' => $item->status_text . ' - ' . ($item->catatan ?: 'Tidak ada catatan'),
                'komentar' => $item->catatan ? '1 komentar' : '0 komentar',
                'file' => !empty($item->file) && $item->file != '[]',
                'status_color' => $item->color ?? 'blue',
                'user_name' => $item->user->name ?? 'Unknown',
                'status' => $item->status_text
            ];
        })->toArray();

        // If no approval data, use recent pengaduan as fallback
        if (empty($this->log_approval)) {
            $this->loadRecentPengaduanAsLog();
        }
    }

    protected function loadRecentPengaduanAsLog()
    {
        $recentPengaduan = Pengaduan::with(['jenisPengaduan'])
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        $this->log_approval = $recentPengaduan->map(function($item) {
            $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
            
            return [
                'id' => $item->id,
                'pengaduan_id' => $item->id,
                'judul' => 'Update ' . ($item->jenisPengaduan->nama_jenis ?? 'Pengaduan') . ' #' . $item->code_pengaduan,
                'waktu' => $this->getTimeAgo($item->updated_at),
                'deskripsi' => 'Status: ' . $statusInfo['text'] . ' - ' . ($item->perihal ?? ''),
                'komentar' => '0 komentar',
                'file' => !empty($item->lampiran) && $item->lampiran != '[]',
                'status_color' => $statusInfo['color'],
                'user_name' => 'System',
                'status' => $statusInfo['text']
            ];
        })->toArray();
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
            case 4:
                return ['text' => 'Diterima', 'color' => 'green'];
            case 5:
                return ['text' => 'Diproses', 'color' => 'yellow'];
            case 6:
                return ['text' => 'Ditindaklanjuti', 'color' => 'blue'];
            case 7:
                return ['text' => 'Ditutup', 'color' => 'green'];
            case 8:
                return ['text' => 'Perlu Klarifikasi', 'color' => 'orange'];
            default:
                return ['text' => 'Unknown', 'color' => 'gray'];
        }
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
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' hari lalu';
        } else {
            return date('d/m/Y', $time);
        }
    }

    // Refresh dashboard data
    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('show-toast', type: 'success', message: 'Dashboard data diperbarui');
    }
}