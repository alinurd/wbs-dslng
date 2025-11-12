<?php

namespace App\Livewire\Combo;

use App\Models\Combo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filters = [];

    protected $listeners = ['filter-updated' => 'applyFilters'];

    public function applyFilters($filters)
    {
        $this->filters = $filters;
        $this->resetPage(); // reset ke halaman 1
    }

    public function render()
    {
        $query = Combo::query()
            ->select('id', 'kelompok', 'data', 'is_active');

        // terapkan filter
        foreach ($this->filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, 'like', "%$value%");
            }
        }

        $dataList = $query->paginate(10);

        $permissions = module_permissions('combo');

        return view('livewire.combo.index-tes', [
            'dataList' => $dataList,
            'title' => 'Combo Data Table',
            'permissions' => $permissions['can'],
        ]);
    }
}
