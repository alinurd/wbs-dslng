<?php

namespace App\Livewire\Components;

use Illuminate\Support\Collection;
use Livewire\Component;

class TableIndex extends Component
{
    public $columns = [];
    public $title = 'Data Table';
    public $filters = [];
    public $filterValues = []; // untuk menyimpan input user
        public $showFilterModal = false;
    protected $dataList;
    protected $permissions;

    public function mount($dataList, $permissions, $filters = [])
    {
        $this->dataList = $dataList;
        $this->permissions = $permissions;
        $this->filters = collect($filters); // pastikan jadi koleksi agar mudah dikelola
    }

    public function updatedFilterValues()
    {
        $this->dispatch('filter-updated', $this->filterValues);
    }

    public function render()
    {
        return view('livewire.components.table-index', [
            'dataList' => $this->dataList,
            'permissions' => $this->permissions,
            'filters' => $this->filters,
                        'showFilterModal' => $this->showFilterModal,
        ]);
    }
}
