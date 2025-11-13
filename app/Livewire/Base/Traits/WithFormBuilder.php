<?php

namespace App\Livewire\Base\Traits;

trait WithFormBuilder
{
    public $schema = [];
    public $formData = [];

    public function loadSchema($config)
    {
        $this->schema = is_string($config) ? json_decode($config, true) : $config;
    }

    public function updatedFormData($field, $value)
    {
        // Hook logic per field jika diperlukan
    }

    public function saveForm()
    {
        $this->validate($this->rules());
        $this->emit('formSaved', $this->formData);
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->schema as $field) {
            if (!empty($field['rules'])) {
                $rules["formData.{$field['name']}"] = $field['rules'];
            }
        }
        return $rules;
    }
}
