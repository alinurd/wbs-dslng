<?php

namespace App\Livewire\Param;

use App\Livewire\Root;
use App\Models\Owner;

class ParamDirektorat extends Root
{
    public $title = "Direktorat";
    public $views = "parameter.owner";
    public $model = Owner::class;
    public $modul = 'combo';
    public $kel = 'combo';
    
    // Form configuration
    public $form = [ 
        'owner_name_1' => null,
        'owner_name' => null,
        'parent_id' => null,
        'is_active' => true,
    ];

    // Filters - SESUAI DENGAN STRUKTUR ROOT
    public $filters = [
        'owner_name_1' => '',
        'owner_name' => '',
        'is_active' => '',
    ];

      public $rules = [
        'form.owner_name_1' => 'required|string|max:255',
        'form.owner_name' => 'required|string|max:255',
        // 'form.data_id' => 'required|string|max:255',
        'form.is_active' => 'boolean',
    ];


    protected $messages = [
       
        'owner_name.required' => 'Data Id wajib diisi!',
        'owner_name_1.required' => 'Data En wajib diisi!',
        // 'parent_id.required' => 'Data En wajib diisi!',
        'is_active.required' => 'Status wajib diisi!',
    ];
 
    public function columns()
    {
        return ['owner_name_1', 'owner_name', 'parent_id'];
    }
 
    // public function filterDefault()
    // {
    //     return [
    //         ['f' => 'kelompok', 'v' => 'aduan'],
    //         // ['f' => 'is_active', 'v' => 1],
    //     ];
    // }

   
    public function query()
    {
        $query = parent::query();

        if (is_array($this->filters)) {
            foreach ($this->filters as $key => $val) {
                if ($val !== '' && $val !== null) {
                    if ($key === 'is_active') {
                        $query->where($key, $val);
                    } else {
                        $query->where($key, 'like', "%$val%");
                    }
                }
            }
        }

        return $query;
    }

    /**
 * Get trilevel hierarchy for a specific owner
 */
public function getHierarchy($ownerId)
{
    $owner = Owner::with(['parent', 'children.children'])->findOrFail($ownerId);
    
    $hierarchy = $owner->getHierarchyForDisplay();
    
    return $hierarchy;
}

/**
 * Get all children for a specific parent
 */
public function getChildrenByParent($parentId)
{
    if ($parentId == 0) {
        // Get root level owners (no parent)
        $children = Owner::where('parent_id', 0)
                        ->where('is_active', 1)
                        ->get();
    } else {
        // Get children of specific parent
        $children = Owner::where('parent_id', $parentId)
                        ->where('is_active', 1)
                        ->with('children') // Include grandchildren if needed
                        ->get();
    }
    
    return $children;
}

/**
 * View detail with hierarchy
 */
public function view($id)
{
    can_any([strtolower($this->modul).'.view']);
    
    $record = Owner::with(['parent', 'children'])->findOrFail($id);
    
    // Pastikan data hierarchy dalam format yang benar
    $hierarchyData = [];
    
    if ($record->parent) {
        $hierarchyData['parent'] = [$record->parent->owner_name];
        
        if ($record->parent->parent) {
            $hierarchyData['grandparent'] = [$record->parent->parent->owner_name];
        }
    }
    
    $hierarchyData['current'] = [$record->owner_name];
    
    if ($record->children->isNotEmpty()) {
        $hierarchyData['children'] = $record->children->pluck('owner_name')->toArray();
    }
    
    $this->detailData = [
        'Struktur Hirarki' => $hierarchyData,
        'Nama Indonesia' => $record->owner_name,
        'Nama English' => $record->owner_name_1,
        'Kode Owner' => $record->owner_code ?? '-',
        'Level Number' => $record->level_no ?? '-',
        'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
        'Status Lapor' => $record->sts_lapor ? 'Aktif' : 'Nonaktif',
        'Status Lapor Kerja' => $record->sts_lapor_kerja ? 'Aktif' : 'Nonaktif',
        'Jumlah Children' => $record->children->count(),
        'Daftar Children' => $record->children->pluck('owner_name')->toArray(),
        'Deskripsi' => $record->description ?? '-',
        'Dibuat Pada' => $record->created_at->format('d/m/Y H:i'),
        'Diupdate Pada' => $record->updated_at->format('d/m/Y H:i'),
    ];
    
    $this->detailTitle = "Detail " . $this->title;
    $this->showDetailModal = true;
}


/**
 * Get tree structure for dropdown/select
 */
public function getTreeForSelect()
{
    $owners = Owner::where('is_active', 1)
                  ->with('children')
                  ->where('parent_id', 0)
                  ->get();
    
    $tree = [];
    foreach ($owners as $owner) {
        $tree = array_merge($tree, $this->buildTree($owner));
    }
    
    return $tree;
}

private function buildTree($owner, $level = 0)
{
    $prefix = str_repeat('-- ', $level);
    $tree = [
        $owner->id => $prefix . $owner->owner_name
    ];
    
    foreach ($owner->children as $child) {
        $tree = array_merge($tree, $this->buildTree($child, $level + 1));
    }
    
    return $tree;
}

/**
 * Check if owner has children
 */
public function hasChildren($ownerId)
{
    return Owner::where('parent_id', $ownerId)
               ->where('is_active', 1)
               ->exists();
}


    // METHOD UNTUK TUTUP DETAIL MODAL
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailData = [];
        $this->detailTitle = '';
    }
    
    // public function saving($payload)
    // {
    //     // Custom logic sebelum menyimpan data
    //     return $payload;
    // }
 
    // public function save()
    // {
    //     $this->validate($this->rules);
        
    //     $payload = collect($this->form)
    //         ->only(array_keys($this->formDefault))
    //         ->toArray();
            
    //     $payload = $this->saving($payload);
        
    //     if ($this->updateMode) {
    //         $record = $this->model::findOrFail($this->form['id']);
    //         $record->update($payload);
    //         session()->flash('message', 'Data berhasil diperbarui.');
    //     } else {
    //         $this->model::create($payload);
    //         session()->flash('message', 'Data berhasil ditambahkan.');
    //     }
        
    //     $this->closeModal();
    //     $this->resetPage();
    // }
}