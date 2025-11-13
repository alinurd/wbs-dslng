<?php

namespace App\Livewire\Base;

use App\Livewire\Base\AppBaseComponent;

class AppBaseList extends AppBaseComponent
{
    public $items = [];
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function loadItems()
    {
        // Override di child class
    }
}
