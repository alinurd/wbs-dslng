<?php 
namespace App\Livewire\Roles;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Editor extends Component
{
    public $role;
    public $permissions;
    public $selectedPermissions = [];
    public $groupedPermissions = [];
    
    // Search functionality
    public $search = '';

    public function mount($id)
    {
        $this->role = Role::findOrFail($id);
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $this->permissions = Permission::when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->get();

        $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray();
        $this->groupPermissions();
    }

    public function groupPermissions()
    {
        $grouped = $this->permissions->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });

        // Convert Eloquent Collection ke array biasa untuk menghindari serialization issues
        $this->groupedPermissions = collect($grouped->toArray())->map(function($permissions, $module) {
            return [
                'module' => $module,
                'permissions' => $permissions,
                'permission_names' => collect($permissions)->pluck('name')->toArray()
            ];
        })->sortBy(function($group) {
            // Custom sorting untuk mengatur urutan module
            $order = [
                'dashboard' => -1,
                'pengaduan' => 0,
                'parameter' => 1,
                'user-management' => 2,
                'menu-management' => 3,
                'blog' => 4
            ];
            return $order[$group['module']] ?? 999;
        })->values()->toArray();
    }

    public function togglePermission($permissionName)
    {
        try {
            DB::transaction(function() use ($permissionName) {
                if (in_array($permissionName, $this->selectedPermissions)) {
                    $this->role->revokePermissionTo($permissionName);
                    $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionName]);
                } else {
                    $this->role->givePermissionTo($permissionName);
                    $this->selectedPermissions[] = $permissionName;
                }
            });

            // Reload permissions untuk mendapatkan data terbaru
            $this->loadPermissions();
            
            // Dispatch event untuk refresh menu atau komponen lain
            $this->dispatch('permissionUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to update permission: ' . $e->getMessage());
        }
    }

    public function toggleModule($moduleName, $action = null)
    {
        try {
            DB::transaction(function() use ($moduleName, $action) {
                $modulePermissions = $this->permissions->filter(function($permission) use ($moduleName) {
                    return str_starts_with($permission->name, $moduleName . '.');
                });

                if ($action === 'select-all') {
                    // Select all permissions in module
                    foreach ($modulePermissions as $permission) {
                        if (!in_array($permission->name, $this->selectedPermissions)) {
                            $this->role->givePermissionTo($permission->name);
                            $this->selectedPermissions[] = $permission->name;
                        }
                    }
                } elseif ($action === 'deselect-all') {
                    // Deselect all permissions in module
                    foreach ($modulePermissions as $permission) {
                        if (in_array($permission->name, $this->selectedPermissions)) {
                            $this->role->revokePermissionTo($permission->name);
                            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permission->name]);
                        }
                    }
                }
            });

            // Reload permissions untuk mendapatkan data terbaru
            $this->loadPermissions();
            
            $this->dispatch('permissionUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to update module permissions: ' . $e->getMessage());
        }
    }

    public function updatedSearch()
    {
        $this->loadPermissions();
    }

    public function render()
    {
        return view('livewire.roles.editor', [
            'groupedPermissionsData' => $this->groupedPermissions
        ]);
    }
}