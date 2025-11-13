<?php

namespace App\Livewire\Modules\Blog\Category;

use App\Livewire\Base\DynamicForm;

class CategoryForm extends DynamicForm
{
    public function mount()
    {
        $schema = [
            ['name' => 'nama', 'label' => 'Nama Siswa', 'type' => 'text', 'rules' => 'required'],
            ['name' => 'kelas', 'label' => 'Kelas', 'type' => 'select', 'options' => ['1' => 'I', '2' => 'II', '3' => 'III']],
            ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea'],
        ];
        $this->loadSchema($schema);

        // $this->customView = 'livewire.modules.siswa.form';
        // $this->loadSchema([...]);


    }

    
    public function saveForm()
    {
        parent::saveForm();
        // Custom save logic (DB insert/update)
        // Sementara console log dulu
        logger($this->formData);
    }
}
