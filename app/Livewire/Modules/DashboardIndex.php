<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Combo; 
use App\Models\LogApproval;
use App\Models\Owner;
use App\Models\Pengaduan; 
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
    
    // Properties untuk filter
    public $tahunFilter;
    public $jenisPengaduanFilter;
    public $direktoratFilter;
    public $statusFilter;
    public $fwdToFilter;
    public $codePengaduanFilter;

    // Properties untuk dropdown data
    public $jenisPengaduanList = [];
    public $stsPengaduanList = [];
    public $bulanList = [];
    public $tahunPengaduanList = [];
    public $pengaduanAll = [];
    public $saluranList = [];
    public $fwdList = [];
    public $direktoratList = [];
public $selectedPengaduanId = "";
    public function mount()
    {
        parent::mount(); 
        $this->tahunFilter = date('Y');
        $this->loadDashboardData();
    }


    
    public function loadDashboardData()
    {
        $this->loadDropdownData(); // Load dropdown data first
        $this->loadStats();
        $this->loadPengaduanTerbaru();
        $this->loadLogApproval();
        $this->loadProgressBulanan();
        $this->loadChartData();
    }

    /**
     * Build base query dengan semua filter
     */
 /**
 * Build base query dengan semua filter - PASTIKAN INI SUDAH LENGKAP
 */
 

    protected function loadStats()
    {
        $query = $this->buildBaseQuery();
        
        $totalPengaduan = $query->count();
        $dalamProses = (clone $query)->where('status','!=', 0)->Where('status', '!=', 3)->where('sts_final', 0)->count();
        $selesai = (clone $query)->where('status', 3)->count();
        // $selesai = (clone $query)->where('sts_final', 1)->orWhere('status', 3)->count();
        $menunggu = (clone $query)->where('status', 0)->where('sts_final', 0)->count();

        $this->stats = [
            'total_pengaduan' => $totalPengaduan,
            'dalam_proses' => $dalamProses,
            'selesai' => $selesai,
            'menunggu' => $menunggu
        ];
    }

    protected function loadPengaduanTerbaru()
    {
        $query = $this->buildBaseQuery()
            ->with(['jenisPengaduan', 'pelapor', 'logApprovals'])
            ->orderBy('created_at', 'desc')
            ->limit(5);

        $pengaduan = $query->get();
           
        $this->pengaduan_terbaru = $pengaduan->map(function($item, $index) {
            $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
            $counts = $this->countComentFileByPengaduan($item->id);
                    
        $field = 'data_' . $this->locale;
        $fieldJenis = $item->jenisPengaduan->$field ?? $item->jenisPengaduan->data_id;
        
            return [
                'id' => $item->id,
                'code_pengaduan' => $item->code_pengaduan,
                'no' => $index + 1,
                'judul' => $fieldJenis ?? 'Tidak ada judul',
                'progress' => $this->progressDashboard($item->status, $item->sts_final),
                'tanggal' => $item->created_at?->format('d/m/Y H:i') ?? '-',
                'status' => $statusInfo['text'],
                'status_color' => $statusInfo['color'],
                'jenis_pengaduan' => $fieldJenis ?? '-',
                'pelapor' => $item->pelapor->name ?? 'Unknown',
                'countComment' => $counts['aktivitas'].' ' . trans_choice('global.activity', $counts['aktivitas']),
                'countFile' => $counts['files'] . ' file',
                'countAktivitas' => $counts['aktivitas'] .' ' . trans_choice('global.activity', $counts['aktivitas']),
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
            ->limit(4);

        // Apply filters to log approval through pengaduan relation
        if ($this->hasActiveFilters() || ($this->pelapor && isset($this->userInfo['user']['id']))) {
            $query->whereHas('pengaduan', function($q) {
                if ($this->pelapor && isset($this->userInfo['user']['id'])) {
                    $q->where('user_id', $this->userInfo['user']['id']);
                }
                // Apply other filters
                if ($this->tahunFilter) {
                    $q->whereYear('tanggal_pengaduan', $this->tahunFilter);
                }
                if ($this->jenisPengaduanFilter) {
                    $q->where('jenis_pengaduan_id', $this->jenisPengaduanFilter);
                }
                if ($this->direktoratFilter) {
                    $q->where('direktorat', $this->direktoratFilter);
                }
                if ($this->codePengaduanFilter) {
                    $q->where('code_pengaduan', 'like', '%' . $this->codePengaduanFilter . '%');
                }
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
        $query = $this->buildBaseQuery()
            ->with(['jenisPengaduan', 'pelapor', 'logApprovals'])
            ->orderBy('created_at', 'desc');

        $recentPengaduan = $query->get();

        $this->log_approval = $recentPengaduan->map(function($item) {
            $statusInfo = $this->getStatusInfo($item->status, $item->sts_final);
                    $counts = $this->countComentFileByPengaduan($item->pengaduan_id);

            return [
                'id' => $item->id,
                'pengaduan_id' => $item->id,
                'code' => $item->code_pengaduan,
                'judul' => 'Update ' . ($item->jenisPengaduan->data_id ?? 'Pengaduan') . ' #' . $item->code_pengaduan,
                'waktu' => $this->getTimeAgo($item->updated_at),
                'deskripsi' =>  ($item->perihal ?? ''),
                
                'countComment' => $counts['comments'] . ' komentar',
                'countFile' => $counts['files'] . ' file',
                'file' => !empty($item->lampiran) && $item->lampiran != '[]',
                'status_color' => $statusInfo['color'],
                'user_name' => ($item->user->name ?? $item->pelapor->name ),
                // 'user_name' => 'System',
                'status' => $statusInfo['text']
            ];
        })->toArray();
    }

    protected function loadProgressBulanan()
    {
        // $currentMonth = date('m');
        // $currentYear = $this->tahunFilter ?: date('Y');
        
        $query = $this->buildBaseQuery();

        $menunggu = (clone $query)->where('status', 0)->where('sts_final', 0)->count();
        $dalamProses = (clone $query)->where('status','>', 0)->where('sts_final', 0)->count();
        $selesai = (clone $query)->where('sts_final', 1)->count();

        $totalBulanan = $menunggu + $dalamProses + $selesai;
// dd($totalBulanan);
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
 * Load data untuk chart dengan filter
 */
protected function loadChartData()
{
    $this->chartData = [
        'status_aduan_detail' => $this->getStatusDetailChart(),
        'status_aduan' => $this->getStatusAduanChart(),
        'jenis_pelanggaran' => $this->getJenisPelanggaranChart(),
        'pergerakan_tahunan' => $this->getPergerakanTahunanChart(),
        'saluran_aduan' => $this->getSaluranAduanChart(),
        'direktorat' => $this->getDirektoratChart()
    ];
}
    /**
     * /**
 * Chart 1: Status Aduan (Pie/Donut Chart) - DIPERBAIKI
 */
protected function getStatusAduanChart()
{
    $query = $this->buildBaseQuery();

    $data = $query->selectRaw('
        COUNT(*) as total,
        CASE 
            WHEN status = 0 AND sts_final = 0 THEN "Menunggu"
            WHEN status > 0 AND sts_final = 0 AND status !=3 THEN "Dalam Proses" 
            WHEN sts_final = 1 OR status=3 THEN "Selesai"
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

    // Jika tidak ada data, buat data kosong
    if (empty($labels)) {
        $labels = ['Menunggu', 'Dalam Proses', 'Selesai'];
        $values = [0, 0, 0];
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
                    // 'text' => 'Status Aduan ',
                    'font' => ['size' => 16]
                ]
            ]
        ]
    ];
}

protected function getStatusDetailChart()
{
    $query = $this->buildBaseQuery();

    if ($this->statusFilter) {
        $query->where('status', $this->statusFilter);
    }
 
    $allStatuses = DB::table('combos')
        ->where('kelompok', 'sts-aduan')
        ->where('is_active', 1)
        ->orderBy('param_int')
        ->get();
 
    $statusCounts = $query->select('status', DB::raw('COUNT(*) as total'))
        ->groupBy('status')
        ->get()
        ->keyBy('status');

    $labels = [];
    $values = [];
    $colors = [];
 
    $colorPalette = [
       '#FFCE56',
    '#10B981', '#a556ff', '#4b87c0', '#9966FF',
        '#FF9F40', '#8B5CF6', '#36A2EB', '#F59E0B', '#EF4444',
        '#3B82F6', '#f65cd2'
    ];

    $colorIndex = 0;

    
    foreach ($allStatuses as $status) {
        $statusId = $status->param_int;
        $field = 'data_' . $this->locale;
        $statusName = $status->$field ?? $status->data_id;

        // $statusName = $status->data_en;
         
        if (isset($statusCounts[$statusId]) && $statusCounts[$statusId]->total > 0) { 
            $labels[] = $statusName;
            $values[] = $statusCounts[$statusId]->total;
            $colors[] = $colorPalette[$colorIndex % count($colorPalette)];
            $colorIndex++;
        } else {
             $labels[] =  $statusName;
            $values[] = 0;
            $colors[] = '#E5E7EB'; 
        }
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
                        'padding' => 20,
                        'boxWidth' => 12,
                        'font' => [
                            'size' => 11
                        ]
                    ]
                ],
                'title' => [
                    'display' => true,
                    // 'text' => 'Detail Status Aduan',
                    'font' => ['size' => 16]
                ],
                 
            ]
        ]
    ];
}
 
 
/**
 * Build base query dengan semua filter - DIPASTIKAN KONSISTEN
 */
protected function buildBaseQuery()
{
    $query = Pengaduan::query();

     if ($this->tahunFilter) {
        $query->whereYear('tanggal_pengaduan', $this->tahunFilter);
    }

     if ($this->jenisPengaduanFilter) {
        $query->where('jenis_pengaduan_id', $this->jenisPengaduanFilter);
    }

     if ($this->direktoratFilter) {
         if (is_numeric($this->direktoratFilter)) {
             $query->where('direktorat', $this->direktoratFilter);
        } else {
             $query->where('direktorat', 'like', '%' . $this->direktoratFilter . '%');
        }
    }

    // Filter status
    if ($this->statusFilter) {
        // \dd($this->statusFilter);
        $s=Combo::where('kelompok', 'sts-aduan')
            ->where('id', $this->statusFilter)
            ->first();
            $query->where('status', $s['param_int']);
        // switch ($this->statusFilter) {
        //     case 'menunggu':
        //         $query->where('status', 0)->where('sts_final', 0);
        //         break;
        //     case 'dalam_proses':
        //         $query->where('status', '>', 0)->where('sts_final', 0);
        //         break;
        //     case 'selesai':
        //         $query->where('sts_final', 1);
        //         break;
        //   default: 
        //        if (is_numeric($this->statusFilter)) {
        //             $query->where('status', $this->statusFilter);
        //         }
        //         break;
        // }
    }
 
    if ($this->fwdToFilter) {
        $query->where('fwd_to', $this->fwdToFilter);
    }
 
    if ($this->codePengaduanFilter) {
        $query->where('code_pengaduan', 'like', '%' . $this->codePengaduanFilter . '%');
    }
 
    if ($this->pelapor && isset($this->userInfo['user']['id'])) {
        $query->where('user_id', $this->userInfo['user']['id']);
    }

    // Debug query (optional, bisa dihapus setelah testing)
    // \Log::info('Dashboard Query: ', [
    //     'tahun' => $this->tahunFilter,
    //     'jenis' => $this->jenisPengaduanFilter,
    //     'direktorat' => $this->direktoratFilter,
    //     'status' => $this->statusFilter,
    //     'sql' => $query->toSql(),
    //     'bindings' => $query->getBindings()
    // ]);

    return $query;
}

/**
 * Chart 2: Jenis Pelanggaran (Bar Chart) - DIPERBAIKI
 */
protected function getJenisPelanggaranChart()
{
    $query = $this->buildBaseQuery()
        ->with('jenisPengaduan')
        ->select('jenis_pengaduan_id', DB::raw('COUNT(*) as total'))
        ->groupBy('jenis_pengaduan_id')
        ->orderBy('total', 'desc');

    $data = $query->get();
    //  \Log::info('Dashboard Query: ', [
    //     'tahun' => $this->tahunFilter,
    //     'jenis' => $this->jenisPengaduanFilter,
    //     'direktorat' => $this->direktoratFilter,
    //     'status' => $this->statusFilter,
    //     'sql' => $query->toSql(),
    //     'data' => $data
    // ]);
    $labels = [];
    $values = [];
    $backgroundColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#7CFFB2', '#FD7F6F'
    ];

    foreach ($data as $item) {
        $field = 'data_' . $this->locale;
        $statusName = $item->jenisPengaduan->$field ?? $item->jenisPengaduan->data_id;
        $labels[] = $statusName ?? 'Tidak Diketahui';
        $values[] = $item->total;
    }

    // Jika tidak ada data, buat data kosong
    if (empty($labels)) {
        $labels = ['Tidak ada data'];
        $values = [0];
        $backgroundColors = ['#B0B0B0'];
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
                    // 'text' => 'Jenis Pelanggaran ',
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
 * Chart 3: Pergerakan Tahunan (Line Chart) - DIPERBAIKI
 */
protected function getPergerakanTahunanChart()
{
    $query = $this->buildBaseQuery()
        ->selectRaw('MONTH(tanggal_pengaduan) as bulan, COUNT(*) as total')
        ->groupBy('bulan')
        ->orderBy('bulan');

    $data = $query->get();

    // Inisialisasi data untuk semua bulan
    $monthlyData = array_fill(1, 12, 0);
    $monthNames = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    foreach ($data as $item) {
        if ($item->bulan >= 1 && $item->bulan <= 12) {
            $monthlyData[$item->bulan] = $item->total;
        }
    }

    // Buat judul yang sesuai dengan filter
    $title = 'Trend Bulanan ';
    if ($this->tahunFilter) {
        $title .= 'Tahun ' . $this->tahunFilter;
    } else {
        // Jika tidak ada filter tahun, ambil tahun dari data yang ada
        $availableYears = $this->getTahunOptions();
        if (!empty($availableYears)) {
            $title .= 'Semua Tahun';
        } else {
            $title .= 'Tidak Ada Data';
        }
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
                    'display' => false,
                    'position' => 'top'
                ],
                'title' => [
                    'display' => false,
                    'text' => $title,
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
 * Chart 4: Saluran Aduan (Pie Chart) - DIPERBAIKI
 */
protected function getSaluranAduanChart()
{
    $query = $this->buildBaseQuery()
        ->with('saluranAduan')
        ->select('saluran_aduan_id', DB::raw('COUNT(*) as total'))
        ->groupBy('saluran_aduan_id');

    $data = $query->get();

    $labels = [];
    $values = [];
    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

    foreach ($data as $item) {
        
        // $field = 'data_' . $this->locale;
        // $fieldName = $item->saluranAduan->$field ?? $item->jenisPengaduan->data_id;
        $labels[] = $item->saluranAduan->data_id ?? 'Tidak Diketahui';
        $values[] = $item->total;
    }

    // Jika tidak ada data, buat data kosong
    if (empty($labels)) {
        $labels = ['Tidak ada data'];
        $values = [0];
        $colors = ['#B0B0B0'];
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
                    'display' => false,
                    'text' => 'Saluran Aduan ',
                    'font' => ['size' => 16]
                ]
            ]
        ]
    ];
}

/**
 * Chart 5: Direktorat (Horizontal Bar Chart) - DIPERBAIKI
 */
protected function getDirektoratChart()
{
    $query = $this->buildBaseQuery()
        ->select('direktorat', DB::raw('COUNT(*) as total'))
        ->groupBy('direktorat')
        ->orderBy('total', 'desc');

    $data = $query->get();

    $labels = [];
    $values = [];
    $backgroundColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#7CFFB2', '#FD7F6F', '#B2B2B2', '#6A0DAD'
    ];

    foreach ($data as $item) {
        $labels[] = $this->getDirektoratName($item->direktorat) ?: 'Tidak Diketahui';
        $values[] = $item->total;
    }

    // Jika tidak ada data, buat data kosong
    if (empty($labels)) {
        $labels = ['Tidak ada data'];
        $values = [0];
        $backgroundColors = ['#B0B0B0'];
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
                    'display' => false,
                    'text' => 'Aduan per Direktorat ',
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
     * Load dropdown data
     */
     

    /**
     * Method untuk mendapatkan opsi filter (gunakan data yang sudah di-load)
     */
    public function getTahunOptions()
    {
        return $this->tahunPengaduanList;
    }

    public function getJenisPengaduanOptions()
    {
        return $this->jenisPengaduanList->pluck('data_id', 'id')->toArray();
    }

    public function getDirektoratOptions()
    {
        return collect($this->direktoratList)->pluck('owner_name', 'owner_name')->toArray();
    }

    public function getFwdToOptions()
    {
        return collect($this->fwdList)->pluck('data_id', 'id')->toArray();
    }

    /**
     * Check jika ada filter aktif
     */
    public function hasActiveFilters()
    {
        return !empty($this->tahunFilter) || 
               !empty($this->jenisPengaduanFilter) || 
               !empty($this->direktoratFilter) || 
               !empty($this->statusFilter) || 
               !empty($this->fwdToFilter) || 
               !empty($this->codePengaduanFilter);
    }

    /**
     * Get description untuk filter yang aktif
     */
    public function getFilterDescription($forChart = false)
    {
        $descriptions = [];

        if ($this->tahunFilter) {
            $descriptions[] = 'Tahun ' . $this->tahunFilter;
        }

        if ($this->jenisPengaduanFilter && isset($this->getJenisPengaduanOptions()[$this->jenisPengaduanFilter])) {
            if ($forChart) {
                $descriptions[] = $this->getJenisPengaduanOptions()[$this->jenisPengaduanFilter];
            } else {
                $descriptions[] = 'Jenis: ' . $this->getJenisPengaduanOptions()[$this->jenisPengaduanFilter];
            }
        }

        if ($this->direktoratFilter) {
            if ($forChart) {
                $descriptions[] = $this->getDirektoratName($this->direktoratFilter);
            } else {
                $descriptions[] = 'Direktorat: ' . $this->getDirektoratName($this->direktoratFilter);
            }
        }

        if ($this->statusFilter) {
            $statusText = ucfirst(str_replace('_', ' ', $this->statusFilter));
            if ($forChart) {
                $descriptions[] = $statusText;
            } else {
                $descriptions[] = 'Status: ' . $statusText;
            }
        }

        if ($this->codePengaduanFilter) {
            if (!$forChart) {
                $descriptions[] = 'Code: ' . $this->codePengaduanFilter;
            }
        }

        if (empty($descriptions)) {
            return $forChart ? '' : __('global.semua').' Data';
        }

        return $forChart ? '(' . implode(', ', $descriptions) . ')' : implode(' • ', $descriptions);
    }

    /**
     * Reset semua filter
     */
    public function resetFilters()
    {
        $this->tahunFilter = date('Y');
        $this->jenisPengaduanFilter = null;
        $this->direktoratFilter = null;
        $this->statusFilter = null;
        $this->fwdToFilter = null;
        $this->codePengaduanFilter = null;
        
        $this->loadDashboardData();
        $this->dispatch('show-toast', type: 'success', message: 'Filter berhasil direset');
    }

    /**
     * Updated hooks untuk setiap filter
     */
    public function updatedTahunFilter()
    {
        $this->loadDashboardData();
    }

    public function updatedJenisPengaduanFilter()
    {
        $this->loadDashboardData();
    }

    public function updatedDirektoratFilter()
    {
        $this->loadDashboardData();
    }

    public function updatedStatusFilter()
    {
        $this->loadDashboardData();
    }

    public function updatedFwdToFilter()
    {
        $this->loadDashboardData();
    }

    public function updatedCodePengaduanFilter()
    {
        $this->loadDashboardData();
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->notify('success', 'Dashboard data diperbarui');
 
    }


    public function runComment()
{
     

    $this->comment(\auth()->user()->id);
}

      public function comment($id)
    {
        // can_any([strtolower($this->modul) . '.view']);
        $this->selectedPengaduanId='';
 
    
         
        $detailTitle = "General Information " ;

      $this->trackingId = $id;
      $this->type = 4;
        $this->codePengaduan = '';
        $this->showComment = true;
        
        if (!empty($detailData)) {
        }
        $this->detailData =[];
        
        if (!empty($detailTitle)) {
            $this->detailTitle = $detailTitle;
        }

        $this->loadChatData();

        $this->uploadFile();
    }
    public function loadChatData()
    {
        if (!$this->trackingId) return;

        $this->loadMessages();
        $this->loadChatStats();
    }
 
}