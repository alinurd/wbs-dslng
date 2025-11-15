<?php
namespace App\Livewire\Modules\Pengaduan;


use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Pengaduan;

class Tracking extends Root
{

    
    public $modul = 'p_tracking'; // Ubah ke 'pengaduan'
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.tracking';

    
    public $title = "Lacak Aduan"; 
     

    public $data = []; 
     
 
    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan','status'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'user_id', 'v' => auth()->id()],
            // ['f' => 'is_active', 'v' => 1],
        ];
    }

      public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
    }

     
    public function query()
{
    $q = ($this->model)::query();
    
    if (is_array($this->filters)) {
        foreach ($this->filters as $key => $val) {
            if ($key == 'tahun' && !empty($val)) {
                $q->whereRaw('YEAR(tanggal_pengaduan) = ?', [$val]);
            }
            if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                $q->where('jenis_pengaduan_id', [$val]);
            }
        }
        $q->where('user_id', auth()->id());
    }

    return $q;
}
    
   
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status  ? 'Aktif' : 'Nonaktif',
        ];
        
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

 
   
       
}