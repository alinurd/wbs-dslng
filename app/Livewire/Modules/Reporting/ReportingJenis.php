<?php

namespace App\Livewire\Modules\Reporting;

use App\Livewire\Root;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class ReportingJenis extends Root
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
            if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                $q->where('jenis_pengaduan_id', $val);
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
}
