<?php

namespace App\Livewire\Base;

use App\Livewire\Base\AppBaseComponent;

class AppBaseForm extends AppBaseComponent
{
    public $formData = [];
    public $mode = 'create'; // create | edit | view
    public $recordId = null;

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function loadData($id)
    {
        $this->recordId = $id;
    }

    public function save()
    {
        $this->validate($this->rules());
        $this->alertSuccess('Data berhasil disimpan');
    }

    protected function rules()
    {
        return [];
    }
}
