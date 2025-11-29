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
    
    // Properties untuk filter chart
    public $tahunFilter;

    public function mount()
    {
        parent::mount(); 
        $this->tahunFilter = date('Y');
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
                'judul' => $item->jenisPengaduan->data_id ?? 'Tidak ada judul',
                'progress' => $this->progressDashboard($item->status, $item->sts_final),
                'tanggal' => $item->created_at?->format('d/m/Y H:i') ?? '-',
                'status' => $statusInfo['text'],
                'status_color' => $statusInfo['color'],
                'jenis_pengaduan' => $item->jenisPengaduan->data_id ?? '-',
                'pelapor' => $item->pelapor->name ?? 'Unknown',
                'countComment' => $counts['aktivitas'] . ' aktivitas',
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
                'judul' => 'Update ' . ($item->jenisPengaduan->data_id ?? 'Pengaduan') . ' #' . $item->code_pengaduan,
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

    /**
     * Load data untuk chart berdasarkan struktur database yang benar
     */
    protected function loadChartData()
    {
        $this->chartData = [
            'status_aduan' => $this->getStatusAduanChart(),
            'jenis_pelanggaran' => $this->getJenisPelanggaranChart(),
            'pergerakan_tahunan' => $this->getPergerakanTahunanChart(),
            'saluran_aduan' => $this->getSaluranAduanChart(),
            'direktorat' => $this->getDirektoratChart()
        ];
    }

    /**
     * Chart 1: Status Aduan (Pie/Donut Chart)
     * Sesuai dengan field status dan sts_final di database
     */
    protected function getStatusAduanChart()
    {
        $currentYear = $this->tahunFilter;
        
        $query = Pengaduan::whereYear('tanggal_pengaduan', $currentYear);
        
        if ($this->pelapor) {
            $query->where('user_id', $this->userInfo['user']['id'] ?? null);
        }

        // Query sesuai dengan struktur status yang ada
        $data = $query->selectRaw('
            COUNT(*) as total,
            CASE 
                WHEN status = 0 AND sts_final = 0 THEN "Menunggu"
                WHEN status = 1 AND sts_final = 0 THEN "Dalam Proses" 
                WHEN sts_final = 1 THEN "Selesai"
                ELSE "Status Lain"
            END as status_label
        ')
        ->groupBy('status_label')
        ->get();

        $labels = [];
        $values = [];
        $colors = ['#FF6384', '#36A2EB', '#4BC0C0', '#FFCD56'];

        foreach ($data as $item) {
            $labels[] = $item->status_label;
            $values[] = $item->total;
        }

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'hoverBackgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'usePointStyle' => true,
                            'padding' => 20
                        ]
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Status Aduan Tahun ' . $currentYear,
                        'font' => ['size' => 16]
                    ]
                ]
            ]
        ];
    }

    /**
     * Chart 2: Jenis Pelanggaran (Bar Chart)
     * Menggunakan relasi jenisPengaduan yang benar
     */
    protected function getJenisPelanggaranChart()
    {
        $currentYear = $this->tahunFilter;
        
        $query = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->with('jenisPengaduan')
            ->select('jenis_pengaduan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_pengaduan_id')
            ->orderBy('total', 'desc')
            ->limit(8);
            
        if ($this->pelapor) {
            $query->where('user_id', $this->userInfo['user']['id'] ?? null);
        }

        $data = $query->get();

        $labels = [];
        $values = [];
        $backgroundColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#7CFFB2', '#FD7F6F'
        ];

        foreach ($data as $item) {
            $labels[] = $item->jenisPengaduan->data_id ?? 'Tidak Diketahui';
            $values[] = $item->total;
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Jumlah Aduan',
                    'data' => $values,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => false
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Jenis Pelanggaran Tahun ' . $currentYear,
                        'font' => ['size' => 16]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Chart 3: Pergerakan Tahunan (Line Chart)
     * Berdasarkan tanggal_pengaduan
     */
    protected function getPergerakanTahunanChart()
    {
        $currentYear = $this->tahunFilter;
        
        $query = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->selectRaw('MONTH(tanggal_pengaduan) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan');
            
        if ($this->pelapor) {
            $query->where('user_id', $this->userInfo['user']['id'] ?? null);
        }

        $data = $query->get();

        // Inisialisasi data untuk semua bulan
        $monthlyData = array_fill(1, 12, 0);
        $monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        foreach ($data as $item) {
            $monthlyData[$item->bulan] = $item->total;
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $monthNames,
                'datasets' => [[
                    'label' => 'Jumlah Aduan',
                    'data' => array_values($monthlyData),
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#36A2EB',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top'
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Pergerakan Aduan Tahun ' . $currentYear,
                        'font' => ['size' => 16]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Chart 4: Saluran Aduan (Pie Chart)
     * Menggunakan relasi saluranAduan yang benar
     */
    protected function getSaluranAduanChart()
    {
        $currentYear = $this->tahunFilter;
        
        $query = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->with('saluranAduan')
            ->select('saluran_aduan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('saluran_aduan_id');
            
        if ($this->pelapor) {
            $query->where('user_id', $this->userInfo['user']['id'] ?? null);
        }

        $data = $query->get();

        $labels = [];
        $values = [];
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

        foreach ($data as $item) {
            $labels[] = $item->saluranAduan->data_id ?? 'Tidak Diketahui';
            $values[] = $item->total;
        }

        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'hoverBackgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'usePointStyle' => true,
                            'padding' => 20
                        ]
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Saluran Aduan Tahun ' . $currentYear,
                        'font' => ['size' => 16]
                    ]
                ]
            ]
        ];
    }

    /**
     * Chart 5: Direktorat (Horizontal Bar Chart)
     * Berdasarkan field direktorat
     */
    protected function getDirektoratChart()
    {
        $currentYear = $this->tahunFilter;
        
        $query = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->select('direktorat', DB::raw('COUNT(*) as total'))
            ->groupBy('direktorat')
            ->orderBy('total', 'desc')
            ->limit(10);
            
        if ($this->pelapor) {
            $query->where('user_id', $this->userInfo['user']['id'] ?? null);
        }

        $data = $query->get();

        $labels = [];
        $values = [];
        $backgroundColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#7CFFB2', '#FD7F6F', '#B2B2B2', '#6A0DAD'
        ];

        foreach ($data as $item) {
            $labels[] = $item->direktorat ?: 'Tidak Diketahui';
            $values[] = $item->total;
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Jumlah Aduan',
                    'data' => $values,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => false
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Aduan per Direktorat Tahun ' . $currentYear,
                        'font' => ['size' => 16]
                    ]
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Method untuk filter chart by tahun
     */
    public function updatedTahunFilter()
    {
        $this->loadChartData();
    }

    /**
     * Get available years for filter
     */
    public function getTahunOptions()
    {
        $query = Pengaduan::selectRaw('YEAR(tanggal_pengaduan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc');
            
        if ($this->pelapor) {
            $query->where('user_id', $this->userInfo['user']['id'] ?? null);
        }

        return $query->pluck('tahun')->toArray();
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('show-toast', type: 'success', message: 'Dashboard data diperbarui');
    }
}