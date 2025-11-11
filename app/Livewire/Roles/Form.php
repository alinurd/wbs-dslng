<?php 
namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class Form extends Component
{
    public $roleId;
    public $name;

    public function mount($id = null)
    {
        if ($id) {
            $role = Role::findOrFail($id);
            $this->roleId = $role->id;
            $this->name = $role->name;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::updateOrCreate(['id' => $this->roleId], ['name' => $this->name]);
        session()->flash('message', 'Role berhasil disimpan!');
        return redirect()->route('roles.index');
    }

    public function render()
    {
        return view('livewire.roles.form');
    }
}
