<?php 
namespace App\Livewire\Roles;

use App\Models\Menu;
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

    // Untuk expanded modules
    public $expandedModules = [];

    public function mount($id)
    {
        $this->role = Role::findOrFail($id);
        $this->loadPermissions();
        
        // Expand semua module secara default
        $this->expandedModules = $this->getAllModuleNames();
    }

    public function loadPermissions()
    {
        $this->permissions = Permission::when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->get();

        $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray();
        $this->groupPermissions();
    }

    public function getMenuName($slug)
    {
        $m = Menu::where('slug', $slug)->get()->first();
        return ($m['name'] ?? $slug);
    }

    public function groupPermissions()
    {
        $grouped = $this->permissions->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0]; // Module name
        });

        $this->groupedPermissions = collect($grouped->toArray())->map(function($permissions, $module) {
            // Group permissions by parent (module.submodule.action)
            $subModules = [];
            
            foreach ($permissions as $permission) {
                $parts = explode('.', $permission['name']);
                
                if (count($parts) >= 2) {
                    $subModule = $parts[0]; // Parent module
                    $action = end($parts); // Last part as action
                    
                    if (!isset($subModules[$subModule])) {
                        $subModules[$subModule] = [
                            'name' => $subModule,
                            'display_name' => ucfirst(str_replace('-', ' ', $subModule)),
                            'permissions' => []
                        ];
                    }
                    
                    $subModules[$subModule]['permissions'][] = [
                        'name' => $permission['name'],
                        'action' => $action,
                        'display_action' => ucfirst($action)
                    ];
                }
            }

            return [
                'module' => $module,
                'display_name' => ucfirst(str_replace('-', ' ', $module)),
                'sub_modules' => array_values($subModules),
                'permission_names' => collect($permissions)->pluck('name')->toArray()
            ];
        })->sortBy(function($group) {
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

    public function getAllModuleNames()
    {
        return collect($this->groupedPermissions)->pluck('module')->toArray();
    }

    public function toggleModuleExpansion($module)
    {
        if (in_array($module, $this->expandedModules)) {
            $this->expandedModules = array_diff($this->expandedModules, [$module]);
        } else {
            $this->expandedModules[] = $module;
        }
    }

    public function isModuleExpanded($module)
    {
        return in_array($module, $this->expandedModules);
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

            $this->loadPermissions();
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
                    foreach ($modulePermissions as $permission) {
                        if (!in_array($permission->name, $this->selectedPermissions)) {
                            $this->role->givePermissionTo($permission->name);
                            $this->selectedPermissions[] = $permission->name;
                        }
                    }
                } elseif ($action === 'deselect-all') {
                    foreach ($modulePermissions as $permission) {
                        if (in_array($permission->name, $this->selectedPermissions)) {
                            $this->role->revokePermissionTo($permission->name);
                            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permission->name]);
                        }
                    }
                }
            });

            $this->loadPermissions();
            $this->dispatch('permissionUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to update module permissions: ' . $e->getMessage());
        }
    }

    public function toggleSubModule($subModuleName, $action = null)
    {
        try {
            DB::transaction(function() use ($subModuleName, $action) {
                $subModulePermissions = $this->permissions->filter(function($permission) use ($subModuleName) {
                    return str_starts_with($permission->name, $subModuleName . '.');
                });

                if ($action === 'select-all') {
                    foreach ($subModulePermissions as $permission) {
                        if (!in_array($permission->name, $this->selectedPermissions)) {
                            $this->role->givePermissionTo($permission->name);
                            $this->selectedPermissions[] = $permission->name;
                        }
                    }
                } elseif ($action === 'deselect-all') {
                    foreach ($subModulePermissions as $permission) {
                        if (in_array($permission->name, $this->selectedPermissions)) {
                            $this->role->revokePermissionTo($permission->name);
                            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permission->name]);
                        }
                    }
                }
            });

            $this->loadPermissions();
            $this->dispatch('permissionUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to update sub-module permissions: ' . $e->getMessage());
        }
    }

    public function updatedSearch()
    {
        $this->loadPermissions();
    }

    public function render()
    {
        return view('livewire.roles.editor');
    }
}