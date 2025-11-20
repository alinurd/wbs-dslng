<?php
namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\LogApproval;
use App\Models\Pengaduan;
use App\Traits\HasChat;
use Livewire\WithFileUploads;

class FAQ extends Root
{
    use WithFileUploads, HasChat;

    public $modul = 'dashboard';
    public $model = Pengaduan::class;
    public $logModel = LogApproval::class;
    public $views = 'modules.faq';
    public $title = "FAQ";
    public $dataFAQ =[];
    

  public function mount()
    {
        parent::mount(); 
         $this->dataFAQ = $this->getDataFAQ();
    }
}