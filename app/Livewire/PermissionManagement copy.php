<?php 
namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionManagement_old extends Component
{
    public $permissions, $name, $permissionId;
    public $updateMode = false;

    public function render()
    {
        $this->permissions = Permission::all();
        return view('livewire.permission-management');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->permissionId = '';
    }

    public function store()
    {
        $this->validate(['name' => 'required|unique:permissions,name']);
        Permission::create(['name' => $this->name]);
        session()->flash('message', 'Permission berhasil dibuat!');
        $this->resetInput();
    }

    public function edit($id)
    {
        $perm = Permission::findOrFail($id);
        $this->permissionId = $perm->id;
        $this->name = $perm->name;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate(['name' => 'required']);
        $perm = Permission::find($this->permissionId);
        $perm->update(['name' => $this->name]);
        $this->updateMode = false;
        session()->flash('message', 'Permission berhasil diperbarui!');
        $this->resetInput();
    }

    public function delete($id)
    {
        Permission::find($id)->delete();
        session()->flash('message', 'Permission berhasil dihapus!');
    }
}
