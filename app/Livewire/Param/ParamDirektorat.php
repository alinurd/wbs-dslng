<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Owner;

class ParamDirektorat extends Root
{
    public $title = "Direktorat";
    public $views = "parameter.owner";
    public $model = Owner::class;
    public $modul = 'combo';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [ 
        'owner_name_1' => null,
        'owner_name' => null,
        'parent_id' => null,
        'is_active' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'owner_name_1' => '',
        'owner_name' => '',
        'is_active' => '',
    ];

      public $rules = [
        'form.owner_name_1' => 'required|string|max:255',
        'form.owner_name' => 'required|string|max:255',
        // 'form.data_id' => 'required|string|max:255',
        'form.is_active' => 'boolean',
    ];


    protected $messages = [
       
        'owner_name.required' => 'Data Id wajib diisi!',
        'owner_name_1.required' => 'Data En wajib diisi!',
        // 'parent_id.required' => 'Data En wajib diisi!',
        'is_active.required' => 'Status wajib diisi!',
    ];
 
    public function columns()
    {
        return ['owner_name_1', 'owner_name', 'parent_id'];
    }
 
    // public function filterDefault()
    // {
    //     return [
    //         ['f' => 'kelompok', 'v' => 'aduan'],
    //         // ['f' => 'is_active', 'v' => 1],
    //     ];
    // }

   
    public function query()
    {
        $query = parent::query();

        if (is_array($this->filters)) {
            foreach ($this->filters as $key => $val) {
                if ($val !== '' && $val !== null) {
                    if ($key === 'is_active') {
                        $query->where($key, $val);
                    } else {
                        $query->where($key, 'like', "%$val%");
                    }
                }
            }
        }

        return $query;
    }

    
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::with('parent')->findOrFail($id);

        $this->detailData = [
             'Parent' => $record->parent->owner_name,
             'Indonesia' => $record->owner_name_1,
            'English' => $record->owner_name,
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
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
    
    // public function saving($payload)
    // {
    //     // Custom logic sebelum menyimpan data
    //     return $payload;
    // }
 
    // public function save()
    // {
    //     $this->validate($this->rules);
        
    //     $payload = collect($this->form)
    //         ->only(array_keys($this->formDefault))
    //         ->toArray();
            
    //     $payload = $this->saving($payload);
        
    //     if ($this->updateMode) {
    //         $record = $this->model::findOrFail($this->form['id']);
    //         $record->update($payload);
    //         session()->flash('message', 'Data berhasil diperbarui.');
    //     } else {
    //         $this->model::create($payload);
    //         session()->flash('message', 'Data berhasil ditambahkan.');
    //     }
        
    //     $this->closeModal();
    //     $this->resetPage();
    // }
}