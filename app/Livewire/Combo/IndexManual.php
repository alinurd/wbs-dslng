<?php

namespace App\Livewire\Combo;

use App\Livewire\Root;
use App\Models\Combo;

class IndexManual extends Root
{

public $title = "Combo";
public $views = "combo.index-manual";
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
        public $model = Combo::class;

    public $form = [
        'kelompok' => '',
        'data' => '',
        'param_int' =>0,
        'param_str' => '',
        'is_active' => true,
    ];

    public $filters = [
        'kelompok' => '',
        'status' => '',
    ];

    public function rules()
    {
        return [
            'form.kelompok' => 'required|string|max:255',
            'form.data' => 'required|string|max:255',
            'form.param_int' => 'nullable|numeric',
            'form.param_str' => 'nullable|string|max:255',
            'form.is_active' => 'boolean',
        ];
    }

    public function columns()
    {
        return ['kelompok', 'data', 'param_str'];
    }
}
