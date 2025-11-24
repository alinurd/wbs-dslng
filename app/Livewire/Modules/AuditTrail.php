<?php

namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Audit as AuditModel;

class AuditTrail extends Root
{
    public $title = "Audit";
    public $views = 'modules.audit';
    public $model = AuditModel::class;
    public $modul = 'audit';
    public $kel = 'combo';
    
    public $form = [
      
    ];

    public $rules = [
        
    ];

    protected $messages = [
      
    ];
 
     public $filters = [
        'username' => '',
        'user_id' => '',
        'table_name' => '',
        'action' => '',
    ];
   
public function columns(): array
{
    return ['table_name', 'user_id', 'action'];
}

public function query()
{
    $query = ($this->model)::with([ 'user']);
    
    $this->applySearch($query);
    $this->applyFilters($query);
    
    return $query;
}

protected function applySearch($query): void
{
    if (!$this->search || !method_exists($this, 'columns')) {
        return;
    }
    
    $columns = $this->columns();
    
    if (!is_array($columns) || empty($columns)) {
        return;
    }
    
    $query->where(function ($subQuery) use ($columns) {
        foreach ($columns as $column) {
            if ($column === 'user_id') {
                $this->applyUserSearch($subQuery);
            } else {
                $subQuery->orWhere($column, 'like', "%{$this->search}%");
            }
        }
    });
}

protected function applyUserSearch($query): void
{
    $query->orWhereHas('user', function ($userQuery) {
        $userQuery->where('name', 'like', "%{$this->search}%")
                 ->orWhere('username', 'like', "%{$this->search}%");
    });
}

protected function applyFilters($query): void
{
    if (!is_array($this->filters)) {
        return;
    }
    
    foreach ($this->filters as $key => $value) {
        if ($this->isValidFilterValue($value)) {
            $query->where($key, 'like', "%{$value}%");
        }
    }
}

protected function isValidFilterValue($value): bool
{
    return $value !== '' && $value !== null;
}


    public function getNamaUser($record)
    {
        return $record->user->name ?? $record->user->name ?? 'N/A';
    }
    
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kelompok' => $record->kelompok,
            'Data Indonesia' => $record->data_id,
            'Data English' => $record->data_en, 
            'Waktu' => $record->param_int. ' Jam '.' - '. $record->param_int_1. ' Hari',
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
    
     
}