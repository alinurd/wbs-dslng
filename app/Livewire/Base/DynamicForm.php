<?php

namespace App\Livewire\Base;

use App\Livewire\Base\AppBaseComponent;
use App\Livewire\Base\Traits\WithFormBuilder;

class DynamicForm extends AppBaseComponent
{
    use WithFormBuilder;

    public $title = 'Form Dinamis';
    public $customView = null; // custom blade opsional

    public function render()
    {
        return view($this->customView ?? 'livewire.base.dynamic-form', [
            'schema' => $this->schema,
            'permissions' => $this->permissions,
        ]);
    }
}
