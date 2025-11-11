<?php 
namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleManagement extends Component
{
    public $roles, $name, $roleId;
    public $updateMode = false;

    public function render()
    {
        $this->roles = Role::all();
        return view('livewire.role-management');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->roleId = '';
    }

    public function store()
    {
        $this->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $this->name]);
        session()->flash('message', 'Role berhasil dibuat!');
        $this->resetInput();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate(['name' => 'required']);
        $role = Role::find($this->roleId);
        $role->update(['name' => $this->name]);
        $this->updateMode = false;
        session()->flash('message', 'Role berhasil diperbarui!');
        $this->resetInput();
    }

    public function delete($id)
    {
        Role::find($id)->delete();
        session()->flash('message', 'Role berhasil dihapus!');
    }
}
