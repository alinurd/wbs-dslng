<?php 
namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Editor extends Component
{
    public $role;
    public $permissions;
    public $selectedPermissions = [];

    public function mount($id)
    {
        $this->role = Role::findOrFail($id);
        $this->permissions = Permission::all();
        $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray();
    }

    public function togglePermission($permissionName)
    {
        if (in_array($permissionName, $this->selectedPermissions)) {
            $this->role->revokePermissionTo($permissionName);
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionName]);
        } else {
            $this->role->givePermissionTo($permissionName);
            $this->selectedPermissions[] = $permissionName;
        }
    }

    public function render()
    {
        return view('livewire.roles.editor');
    }
}
