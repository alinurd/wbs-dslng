<?php

namespace App\Livewire\Roles;

use App\Livewire\Root;
use Spatie\Permission\Models\Role;

class Index extends Root
{
    public $title = "Roles Users";
    public $views = "roles.index";
    public $model = Role::class;
    public $modul = 'roles';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [
        'name' => '',
        'guard_name' => 'web',
        'is_active' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'name' => '',
         'is_active' => '',
    ];

    public $rules = [
        'form.name' => 'required|string|max:50',
        'form.is_active' => 'boolean',
    ];

    protected $messages = [
        'form.name.required' => 'Role wajib diisi',
    ];
 
    public function columns()
    {
        return ['name'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'guard_name', 'v' => 'web'],
            // ['f' => 'is_active', 'v' => 1],
        ];
    }

   
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'name' => $record->name,
            'Data Indonesia' => $record->data_id,
            'Data English' => $record->data_en,
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
            'Dibuat Pada' => $record->created_at->format('d/m/Y H:i'),
            'Diupdate Pada' => $record->updated_at->format('d/m/Y H:i'),
        ];
        
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailData = [];
        $this->detailTitle = '';
    }
}