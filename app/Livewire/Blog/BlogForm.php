<?php

namespace App\Livewire\Blog;

use App\Models\Menu;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class BlogForm extends Component
{
    public $menuId, $name, $slug, $icon, $route, $parent_id, $order = 0, $is_active = true;
    public $parents;

    public function mount($id = null)
    {
        $this->parents = Menu::whereNull('parent_id')->get();

        if ($id) {
            $menu = Menu::findOrFail($id);
            $this->menuId = $menu->id;
            $this->fill($menu->toArray());
        }
    }

    protected $rules = [
        'name' => 'required',
        'slug' => 'required|unique:menus,slug',
    ];

    public function save()
    {
        $this->validate();

        $menu = Menu::updateOrCreate(['id' => $this->menuId], [
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'route' => $this->route,
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ]);

        // otomatis buat permission dasar
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        foreach ($actions as $action) {
            Permission::firstOrCreate(['name' => "{$menu->slug}.{$action}"]);
        }

        session()->flash('success', 'Menu berhasil disimpan!');
        return redirect()->route('menus.index');
    }

    public function render()
    {
        return view('livewire.menus.form');
    }
}
