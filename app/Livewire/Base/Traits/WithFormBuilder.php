<?php

namespace App\Livewire\Dynamic\Traits;

trait WithFormBuilder
{
    public function mergeFields($metaFields, $customFields)
    {
        $metaArray = collect($metaFields)->map(fn($f) => (array) $f)->toArray();
        return array_values(array_merge($metaArray, $customFields));
    }

    public function initFormData($fields)
    {
        foreach ($fields as $f) {
            $name = $f['field_name'] ?? null;
            if ($name) $this->formData[$name] = $f['default'] ?? null;
        }
    }

    public function generateValidationRules($fields)
    {
        $rules = [];
        foreach ($fields as $f) {
            if (!empty($f['rules'])) {
                $rules["formData.{$f['field_name']}"] = $f['rules'];
            }
        }
        return $rules;
    }

    public function inputType($type)
    {
        $map = [
            'string' => 'text',
            'number' => 'number',
            'text' => 'text',
            'textarea' => 'textarea',
            'select' => 'select',
            'file' => 'file',
            'boolean' => 'checkbox',
        ];
        return $map[$type] ?? 'text';
    }
}
