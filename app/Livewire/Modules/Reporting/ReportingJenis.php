<?php

namespace App\Livewire\Modules\Reporting;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Owner;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class ReportingJenis extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'r_jenis';
    public $model = Pengaduan::class;
    public $views = 'modules.reporting.jenis-aduan';
    public $title = "Laporan Berdasarkan Jenis Pelanggaran";

    // Properties untuk detail (dari HasChat sudah include chat properties)
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload di sidebar (spesifik untuk Tracking)
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];
    public $previewData = [];

    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
        $this->dataPreview();
     }

    public function filterDefault()
    {
        return [
            // ['f' => 'user_id', 'v' => auth()->id()],
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
            if ($key == 'bulan' && !empty($val)) {
                $q->whereMonth('tanggal_pengaduan', $val);
            }
            if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                $q->where('jenis_pengaduan_id', $val);
            }
            if ($key == 'saluran_id' && !empty($val)) {
                $q->where('saluran_aduan_id', $val);
            }
            if ($key == 'fwd_id' && !empty($val)) {
                $q->where('fwd_to', $val);
            }
            if ($key == 'status' && !empty($val)) {
                $q->where('status', $val);
            }
        }
    }
        return $q;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul) . '.view']);

        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
        ];

        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

public function formatFilterKey($key)
{
    $keyMap = [
        'bulan' => 'Bulan',
        'tahun' => 'Tahun',
        'jenis_pengaduan_id' => 'Jenis Pelanggaran',
        'saluran_id' => 'Saluran Aduan',
        'fwd_id' => 'WBS Forward',
        'search' => 'Pencarian'
    ];
    
    return $keyMap[$key] ?? str_replace('_', ' ', ucwords($key, '_'));
}


public function dataPreview()
{
    try {
        $tahun = $this->filters['tahun'] ?? date('Y');
        $bulan = $this->filters['bulan'] ?? date('m');
        $jenisPengaduan = Combo::where('kelompok', 'jenis')->where('is_active', 1)->get();
        $data = Pengaduan::selectRaw('jenis_pengaduan_id, COUNT(*) as total')
            ->whereYear('tanggal_pengaduan', $tahun)
            ->whereMonth('tanggal_pengaduan', $bulan)
            ->groupBy('jenis_pengaduan_id')
            ->get();
        $dataKeyed = $data->keyBy('jenis_pengaduan_id');
        $detailHari = Pengaduan::selectRaw('jenis_pengaduan_id, DAY(tanggal_pengaduan) as hari, COUNT(*) as jumlah')
            ->whereYear('tanggal_pengaduan', $tahun)
            ->whereMonth('tanggal_pengaduan', $bulan)
            ->groupBy('jenis_pengaduan_id', 'hari')
            ->get();
            
        $detailHariGrouped = $detailHari->groupBy('jenis_pengaduan_id');
        $rekapPengaduan = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'dataRekap' => $jenisPengaduan->map(function($jenis) use ($dataKeyed, $detailHariGrouped) {
                $jenisId = $jenis->id;
                $totalBulan = $dataKeyed[$jenisId]->total ?? 0;
                $detailPerHari = $detailHariGrouped[$jenisId] ?? collect();
                $detailHarian = [];
                for ($day = 1; $day <= 31; $day++) {
                    $dayData = $detailPerHari->where('hari', $day)->first();
                    $jumlahHari = $dayData ? $dayData->jumlah : 0;
                    if ($jumlahHari > 0) {
                        $detailHarian[$day] = $jumlahHari;
                    }
                }
                
                return [
                    'id' => $jenisId,
                    'nama_jenis' => $jenis->data_id,
                    'total' => $totalBulan,
                    'detail_harian' => $detailHarian,
                    'total_harian' => array_sum($detailHarian)
                ];
            })
        ]; 
        $this->previewData = $rekapPengaduan;
        $this->previewTotal = \count($rekapPengaduan);
        $this->previewMonth = $this->getPeriodInfo();
        
    } catch (\Exception $e) {
        logger("Preview Error: " . $e->getMessage());
        $this->notify('error', "Preview gagal: " . $e->getMessage());
    }
}

public function previewJenis()
{
    try {
       
 
        $this->showPreviewModal = true;
        
        $totalLaporan = collect($this->previewData['dataRekap'])->sum('total');
        $this->notify('success', "Preview data berhasil ({$totalLaporan} laporan ditemukan)");
        
    } catch (\Exception $e) {
        logger("Preview Error: " . $e->getMessage());
        $this->notify('error', "Preview gagal: " . $e->getMessage());
    }
}


public function exportToExcelJenis()
{ 
    $rekap = $this->previewData['dataRekap'] ?? [];

     if (empty($this->previewData)) {
        $this->notify('error', 'Belum ada data preview! Klik Preview dulu sebelum export.');
        return;
    }

    if (empty($this->previewData['dataRekap'])) {
        $this->notify('error', 'previewData ada, tapi dataRekap kosong → query preview gagal.');
        return;
    }
     
    $data = [];
    foreach ($rekap as $index => $item) {
        $row = [
            'No' => $index + 1,
            'Jenis Pelanggaran' => $item['nama_jenis'],
        ];
 
        for ($d = 1; $d <= 31; $d++) {
            $row[$d] = $item['detail_harian'][$d] ?? 0;
        }
 
        $row['Jumlah'] = $item['total'];

        $data[] = $row;
    } 
    return $this->exportExcel(
        $data,
    'exports.pengaduan-rekap-jenis',
    'rekap-pengaduan-jenis-' . date('Y-m-d-H-i-s') . '.xls',
    [
        'periodInfo' => $this->getPeriodInfo(),
            'filterData' => $this->getFilterData(),
    ]
);
}


public function getPeriodInfo()
{
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    $bulan = $this->filters['bulan'] ?? request('bulan', $currentMonth);
    $tahun = $this->filters['tahun'] ?? request('tahun', $currentYear);     
    $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

    $monthName = $months[$bulan] ?? 'Semua Bulan';
    
    return $monthName . ' ' . $tahun;
}

public function getFilterData()
{
    $filterInfo = [];
    
    $filterLabels = [
        'tahun' => 'Tahun',
        'bulan' => 'Bulan',
    ];
    
    if (!empty($this->search)) {
        $filterInfo['Kata Kunci'] = $this->search;
    }
    
    if (!empty($this->filters) && is_array($this->filters)) {
        foreach ($this->filters as $key => $value) {
            if (!empty($value) && $value !== '' && $value !== null) {
                $label = $filterLabels[$key] ?? $this->formatFilterKey($key);
                $formattedValue = $this->formatFilterValue($key, $value);
                $filterInfo[$label] = $formattedValue;
            }
        }
    }
    
    $queryParams = request()->query();
    $commonFilterKeys = ['search', 'status', 'jenis_pengaduan_id', 'tahun', 'bulan_id', 'saluran_id', 'fwd_id'];
    
    foreach ($commonFilterKeys as $key) {
        if (isset($queryParams[$key]) && !empty($queryParams[$key]) && !isset($filterInfo[$filterLabels[$key] ?? $key])) {
            $label = $filterLabels[$key] ?? $this->formatFilterKey($key);
            $formattedValue = $this->formatFilterValue($key, $queryParams[$key]);
            $filterInfo[$label] = $formattedValue;
        }
    }
     
    if (empty($filterInfo)) {
        $filterInfo['Periode'] = 'Semua Data';
    }
    
    return $filterInfo;
}


}
