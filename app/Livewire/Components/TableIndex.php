<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use Livewire\WithPagination;

class TableIndex extends Component
{
    use WithPagination;

    // Props dari parent
    public $columns = [];
    public $data;
    public $actions = [];

    public $title = 'Data Table';
    public $perPage = 10;

    // Listener agar bisa refresh data dari luar
    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        return view('livewire.components.table-index');
    }
}
