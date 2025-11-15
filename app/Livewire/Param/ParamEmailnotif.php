<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Combo;

class ParamEmailnotif extends Root
{
    public $title = "Email Notifikasi";
    public $views = "parameter.email-notif";
    public $model = Combo::class;
    public $modul = 'combo';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [
        'kelompok' => 'email-notif',
        // 'data_id' => null,
        'data' => null,
        'is_active' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'kelompok' => 'email-notif',
        // 'data_id' => '',
        'is_active' => '',
    ];

    public $rules = [
        'form.kelompok' => 'required|string|max:255',
        // 'form.data_id' => 'required|string|max:255',
        'form.data' => 'required|string|email',
        'form.is_active' => 'boolean',
    ];

    protected $messages = [
        // 'form.data_id.required' => 'Data Indonesia wajib diisi',
        'form.data.required' => 'Data email wajib diisi',
        'form.data.email' => 'format email tidak sesuai',
    ];
 
    public function columns()
    {
        // return ['kelompok', 'data_id', 'data'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'kelompok', 'v' => 'email-notif'],
            // ['f' => 'is_active', 'v' => 1],
        ];
    }

   
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kelompok' => $record->kelompok,
            // 'Data Indonesia' => $record->data_id,
            'Email' => $record->data,
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