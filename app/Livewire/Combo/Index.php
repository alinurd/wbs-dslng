<?php

namespace App\Livewire\Combo;

use App\Models\Combo;
 use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $dataList = Combo::select('id', 'kelompok', 'data', 'is_active')->paginate(10);

                $permissions = module_permissions('combo');

         return view('livewire.combo.index-tes', compact('dataList'))->with([
             'dataList' => $dataList,
            'title' => 'Combo Data Table',
            'currentLocale' => 'en',
            'permissions' => $permissions['can'],
        ]);
    }
}
