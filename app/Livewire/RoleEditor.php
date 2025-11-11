<?php 
namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleEditor extends Component
{
    public $roleId;
    public $name;
    public $show_data = 'All'; // contoh field tambahan
    public $modules = [];

    public function mount($roleId = null)
    {
        if ($roleId) {
            $role = Role::findOrFail($roleId);
            $this->roleId = $role->id;
            $this->name = $role->name;

            // ambil semua permission per module
            $this->modules = Permission::all()
                ->groupBy('module')
                ->toArray();
        } else {
            $this->modules = Permission::all()
                ->groupBy('module')
                ->toArray();
        }
    }

    public function render()
    {
        $role = Role::with('permissions')->find($this->roleId);
        $assigned = $role ? $role->permissions->pluck('name')->toArray() : [];

        return view('livewire.role-editor', [
            'modules' => $this->modules,
            'assigned' => $assigned,
        ]);
    }

    public function togglePermission($permName)
    {
        $role = Role::findOrFail($this->roleId);
        if ($role->hasPermissionTo($permName)) {
            $role->revokePermissionTo($permName);
        } else {
            $role->givePermissionTo($permName);
        }
    }

    public function save()
    {
        $this->validate(['name' => 'required']);
        $role = Role::updateOrCreate(['id' => $this->roleId], [
            'name' => $this->name,
        ]);
        session()->flash('message', 'Role berhasil disimpan!');
    }
}
