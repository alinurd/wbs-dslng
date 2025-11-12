<?php

namespace App\Livewire\Components;

use Livewire\Component;

class TableIndex extends Component
{
    public $columns = [];
    public $title = 'Data Table';
    public $filters = [];
    public $filterValues = []; // menyimpan nilai filter
    public $showFilterModal = false;
    public $permissions = [];

    // Route sumber data DataTables
    public $dataUrl;

    public function mount($columns, $permissions, $dataUrl, $filters = [])
    {
        $this->columns = $columns;
        $this->permissions = $permissions;
        $this->filters = collect($filters);
        $this->dataUrl = $dataUrl;
    }

    /** Buka modal filter */
    public function openFilterModal()
    {
        $this->showFilterModal = true;
    }

    /** Tutup modal filter */
    public function closeFilterModal()
    {
        $this->showFilterModal = false;
    }

    /** Reset semua filter */
    public function resetFilters()
    {
        $this->filterValues = [];
        $this->dispatch('refresh-datatable');
    }

    /** Terapkan filter */
    public function applyFilters()
    {
        $this->dispatch('apply-filters', $this->filterValues);
        $this->showFilterModal = false;
    }

    public function render()
    {
        return view('livewire.components.table-index', [
            'permissions' => $this->permissions,
            'filters' => $this->filters,
        ]);
    }
}
