<?php

namespace App\Livewire\Modules\Users;

use App\Livewire\Root;
use App\Models\Combo;
use Spatie\Permission\Models\Permission;

class PermissionManagement extends Root
{
    public $title = "Permiision";   
    public $views = "modules.users.permission";

    public $model = Permission::class;
    public $modul = 'permissions';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [
        'name' => '', 
    ];

    

    public $rules = [
        'form.name' => 'required|string|max:255', 
    ];

    protected $messages = [
        'form.name.required' => 'Permission Name wajib diisi', 
    ];
 
    public function columns()
    {
        return ['name', ];
    }
 
     

   
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Name' => $record->name,
            'guard_name' => $record->guard_name, 
            'Dibuat Pada' => $record->created_at->format('d/m/Y H:i'),
            'Diupdate Pada' => $record->updated_at->format('d/m/Y H:i'),
        ];
        
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

    // METHOD UNTUK TUTUP DETAIL MODAL
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailData = [];
        $this->detailTitle = '';
    }
     
}