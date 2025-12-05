<?php

namespace App\Livewire\Modules\Reporting;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Owner;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class ReportingFull extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'r_full';
    public $model = Pengaduan::class;
    public $views = 'modules.reporting.full';
    public $title = "Laporan Pengaduan Lengkap";

    // Properties untuk detail (dari HasChat sudah include chat properties)
    public $detailData = [];
    public $detailTitle = '';

    // Properties untuk file upload di sidebar (spesifik untuk Tracking)
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];

    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
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
            // 'Perihal' => $record->perihal,
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



}
