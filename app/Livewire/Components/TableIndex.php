<?php

namespace App\Livewire\Components;

use Livewire\Component;

class TableIndex extends Component
{
    public $columns = [];
     public $title = 'Data Table';

    protected $dataList;
    protected $permissions;

    public function mount($dataList,$permissions)
    {
        // dd($permissions);
        $this->dataList = $dataList;
        $this->permissions = $permissions;
    }

    public function render()
    {
        return view('livewire.components.table-index', [
            'dataList' => $this->dataList,
            'permissions' => $this->permissions,
        ]);
    }
}
