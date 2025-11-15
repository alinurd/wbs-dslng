<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Combo;

class ParamAduan extends Root
{
    public $title = "Saluran Aduan";
    public $views = "parameter.index-manual";
    public $model = Combo::class;
    public $modul = 'combo';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [
        'kelompok' => 'aduan',
        'data_id' => null,
        'data_en' => null,
        'is_active' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'kelompok' => 'aduan',
        'data_id' => '',
        'is_active' => '',
    ];

    public $rules = [
        'form.kelompok' => 'required|string|max:255',
        'form.data_id' => 'required|string|max:255',
        'form.data_en' => 'required|string|max:255',
        'form.is_active' => 'boolean',
    ];

    protected $messages = [
        'form.data_id.required' => 'Data Indonesia wajib diisi',
        'form.data_en.required' => 'Data English wajib diisi',
    ];
 
    public function columns()
    {
        return ['kelompok', 'data_id', 'data_en'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'kelompok', 'v' => 'aduan'],
            // ['f' => 'is_active', 'v' => 1],
        ];
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