<?php

namespace App\Livewire\Dynamic;

use App\Livewire\Base\AppBaseForm;
use App\Livewire\Dynamic\Traits\WithFormBuilder;

class DynamicForm extends AppBaseForm
{
    use WithFormBuilder;

    public $model;          // e.g. 'posts'
    public $category = null;
    public $customFields = [];
    public $fields = [];
    public $metaFields = [];
    public $view = 'livewire.dynamic.form'; // default view

    public function mount($model, $category = null, $customFields = [], $view = null)
    {
        $this->model = $model;
        $this->category = $category;
        $this->customFields = $customFields;

        if ($view) $this->view = $view;

        $this->loadMetaFields();
        $this->buildFields();
    }

    public function loadMetaFields()
    {
        $query = \DB::table('form_meta')->where('model', $this->model);
        if ($this->category) {
            $query->where('category', $this->category);
        }

        $this->metaFields = $query->orderBy('order')->get()->toArray();
    }

    public function buildFields()
    {
        $this->fields = $this->mergeFields($this->metaFields, $this->customFields);
        $this->initFormData($this->fields);
    }

    public function save()
    {
        $rules = $this->generateValidationRules($this->fields);
        $this->validate($rules);

        // Simpan data (sementara hanya dump)
        $this->alertSuccess('Form berhasil disimpan!');
        // Nanti bisa disambungkan ke modelClass tertentu
    }

    public function render()
    {
        return view($this->view, [
            'fields' => $this->fields,
            'formData' => $this->formData,
            'permissions' => $this->permissions,
        ]);
    }
}
