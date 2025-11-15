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
    
    // Form configuration
    public $form = [
        'kelompok' => 'aduan',
        'data_id' => null,
        'data_en' => null,
        'is_active' => true,
    ];

    // Filters
    public $filters = [
        'kelompok' => 'aduan',
        'data_id' => '',
    ];
 
    public $rules = [
        'form.kelompok' => 'required|string|max:255',
        'form.data_id' => 'required|string|max:255',
        'form.data_en' => 'required|string|max:255',
        'form.is_active' => 'boolean',
    ];

      protected $messages = [
        'form.data_id.required' => 'Data Indonesia wajib diisi',
        'form.data_en.required' => 'Data English  wajib diisi',
      ];
    

    // Columns untuk search
    public function columns()
    {
        return ['kelompok', 'data_id', 'data_en'];
    }

    // Filter default jika diperlukan
    public function filterDefault()
    {
        return [
            ['f' => 'kelompok', 'v' => 'aduan'],
            ['f' => 'is_active', 'v' => 1],
        ];
    }

    // Custom logic sebelum save jika diperlukan
    public function saving($payload)
    {
        // Custom logic sebelum menyimpan data
        return $payload;
    }

    // Override save method jika perlu custom logic
    public function save()
    {
        // Custom validation atau logic
        $this->validate($this->rules);
        
        $payload = collect($this->form)
            ->only(array_keys($this->formDefault))
            ->toArray();
            
        // Custom logic sebelum save
        $payload = $this->saving($payload);
        
        if ($this->updateMode) {
            $record = $this->model::findOrFail($this->form['id']);
            $record->update($payload);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            $this->model::create($payload);
            session()->flash('message', 'Data berhasil ditambahkan.');
        }
        
        $this->closeModal();
        $this->resetPage();
    }
}