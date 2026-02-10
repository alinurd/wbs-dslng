<?php

namespace App\Livewire\Menus;

use App\Livewire\Root;
use App\Models\Menu;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
class Form extends Root
{
    public $menuId;
    public $name;
    public $name_en;
    public $slug;
    public $icon;
    public $route;
    public $parent_id;
    public $order = 0;
    public $is_active = true;
    public $parents;

    public function mount($id = null)
    {
        $this->parents = Menu::whereNull('parent_id')->get();
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
        if ($id) {
            $menu = Menu::findOrFail($id);
            $this->menuId = $menu->id;
            $this->fill($menu->toArray());
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer|min:-1',
            'is_active' => 'boolean',
        ];

        if ($this->menuId) {
            $rules['slug'] = 'required|string|max:255|unique:menus,slug,' . $this->menuId;
        }

        return $rules;
    }

    public function save()
    {
        $validated = $this->validate();

        $menu = Menu::updateOrCreate(
            ['id' => $this->menuId],
            $validated
        );

        // Create basic permissions automatically
        $this->createPermissions($menu->slug);

        $this->notify('success', 'Menu berhasil disimpan!');
        return redirect()->route('menus.index');
    }

    protected function createPermissions($slug)
    {
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        
        foreach ($actions as $action) {
            Permission::firstOrCreate([
                'name' => "{$slug}.{$action}",
                'guard_name' => 'web'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.menus.form');
    }
}