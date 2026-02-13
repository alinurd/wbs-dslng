<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Combo;

class ParamNotif extends Root
{
    public $title = "notif-param";
    public $views = "parameter.notif-param";
    public $model = Combo::class;
    public $modul = 'combo';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [
        'kelompok' => 'notif-param',
        'data_id' => null,
        'data_en' => null,
        'param_int' => 0,
        'param_int_1' => 0,
        'is_active' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'kelompok' => 'notif-param',
        'data_id' => '',
        'is_active' => '', 
    ];

    public $rules = [
        'form.kelompok' => 'required|string|max:255',
        'form.data_id' => 'required|string|max:255',
        'form.data_en' => 'required|string|max:255',
        'form.param_int' => 'required|numeric',
        'form.param_int_1' => 'required|numeric',
        'form.is_active' => 'boolean',
    ];

    // protected $messages = [
    //     'form.data_id.required' => 'Data Indonesia wajib diisi',
    //     'form.data_en.required' => 'Data English wajib diisi',
    //     'form.param_int.required' => 'Data Jam wajib diisi',
    //     'form.param_int_1.required' => 'Data Hari wajib diisi',
    // ];
 
    public function columns()
    {
        return ['kelompok', 'data_id', 'data_en', 'param_int_1', 'param_int'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'kelompok', 'v' => 'notif-param'],
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
            __('table.data.date') => $record->param_int. ' '.__('table.data.date').' - '. $record->param_int_1. ' '.__('table.data.day'),
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
           __('table.data.created_at') => $record->created_at->format('d/m/Y H:i'),
            // 'Diupdate Pada' => $record->updated_at->format('d/m/Y H:i'),
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