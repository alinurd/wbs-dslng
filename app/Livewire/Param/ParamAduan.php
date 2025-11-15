<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Combo;

class ParamAduan extends Root
{

public $title = "Saluran Aduan";
public $views = "parameter.index-manual";
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $model = Combo::class;
    public $kelompok = 'aduan';
    public $modul = 'combo';
public $is_active = false;
    public $form = [
        'kelompok' => 'aduan',
        'data_id' => null,
        'data_en' => null,
        'is_active' => false,
    ];

    public $filters = [
        'kelompok' => 'aduan',
        // 'is_active' => 1,
    ];

    public function rules()
    {
        return [
            'form.kelompok' => 'required|string|max:255',
            'form.data_id' => 'required|string|max:255',
            'form.data_en' => 'required|string|max:255',
            'form.is_active' => 'boolean',
        ];
    }

   
    public function columns()
    {
        return ['kelompok', 'data_id', 'data_en'];
    }
}
