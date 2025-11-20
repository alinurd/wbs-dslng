<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Combo;

class ParamFAQ extends Root
{
    public $title = "Status Aduan";
    public $views = "parameter.faq";
    public $model = Combo::class;
    public $modul = 'combo';
    public $kel = 'combo';
    public $faqDropdown = [];
    // Form configuration
    public $form = [
        'kelompok' => 'p_faq',
        'data_id' => '',
        'data_en' => '',
        'is_active' => true,
        'param_int' => '',
    ];

    public $filters = [
        'kelompok' => 'p_faq',
        'data_id' => '',
        'is_active' => '',
        // 'param_int' => 0,
    ];

    public $rules = [
        'form.kelompok' => 'required|string|max:255',
        'form.data_id' => 'required|string|max:255',
        'form.data_en' => 'required|string|max:255',
        'form.param_int' => 'required',
    ];

    protected $messages = [
        'form.data_id.required' => 'Data Indonesia wajib diisi',
        'form.data_en.required' => 'Data English wajib diisi',
        'form.param_int.required' => 'Pertanyaan wajib diisi', 
    ];
    public function mount()
    {
         $this->faqDropdown = $this->model::where('kelompok', 'pertanyaan')
            ->select('data_id', 'data_en', 'data', 'id')
            ->where('is_active', true)
            ->where('param_int', true)
            ->orderBy('data_id')
            ->get();
             parent::mount();
    }
 
    public function columns()
    {
        return ['kelompok', 'data_id', 'data_en','param_int'];
    }
 
    public function filterDefault()
    {
        return [
            ['f' => 'kelompok', 'v' => 'p_faq'],
            // ['f' => 'is_active', 'v' => 1],
        ];
    }

   
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kelompok' => $record->kelompok,
            'Pertanyaan' => $this->getComboById($record->param_int),
            'Data English' => $record->data_en,
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