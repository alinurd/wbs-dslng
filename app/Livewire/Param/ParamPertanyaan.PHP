<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Combo;

class ParamPertanyaan extends Root
{
    public $title = "Pertanyaan";
    public $views = "parameter.pertanyaan";
    public $model = Combo::class;
    public $modul = 'pertanyaan';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [
        'kelompok' => 'pertanyaan',
        'data_id' => null,
        'data_en' => null,
        'is_active' => true,
        'param_it' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'kelompok' => 'pertanyaan',
        'data_id' => '',
        'is_active' => '',
        'param_it' => '',
    ];

    public $rules = [
        'form.kelompok' => 'required|string|max:255',
        'form.data_id' => 'required|string|max:255',
        'form.data_en' => 'required|string|max:255',
        'form.is_active' => 'boolean',
        'form.param_it' => 'boolean',
    ];

    protected $messages = [
        // 'form.data_id.required' => 'Data Indonesia wajib diisi',
        // 'form.data_en.required' => 'Data English wajib diisi',
    ];
 
    public function columns()
    {
        return ['kelompok', 'data_id', 'data_en'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'kelompok', 'v' => 'pertanyaan'],
            // ['f' => 'is_active', 'v' => 1],
        ];
    }

   
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kelompok' => $record->kelompok,
            'Data Indonesia' => $record->data_id,
            'Data English' => $record->data_en,
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
            __('table.data.created_at') => $record->created_at->format('d/m/Y H:i'),
            __('table.updated_at') => $record->updated_at->format('d/m/Y H:i'),
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