<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\LogApproval;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class News extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'news';
    public $model = Pengaduan::class;
    public $logModel = LogApproval::class;
    public $views = 'modules.news';
    public $title = "News";
    public $dataFAQ =[];
    public $newCategory =[];
    
public $form = [
        'categry' => 'p_faq',
        'title_id' => '',
        'title_en' => '',
        'content_id' => '',
        'content_en' => '',
        'files' => '', 
        'image' => '', 
        'is_active' => true, 
    ];

  public function mount()
    {
        parent::mount(); 
        $this->newCategory = Combo::where('kelompok', 'pertanyaan')
            ->select('data_id', 'data_en', 'data', 'id')
            ->where('is_active', true)
            ->where('param_int', true)
            ->orderBy('data_id')
            ->get();
         $this->dataFAQ = $this->getDataFAQ();
    }
}