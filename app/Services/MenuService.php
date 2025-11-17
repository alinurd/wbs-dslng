<?php
// app/Services/MenuService.php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    public function getAuthenticatedMenus()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }

        // Get all active parent menus with their children and permissions
        $menus = Menu::with(['children' => function($query) {
                $query->active()->orderBy('order')->with('permissions');
            }, 'permissions'])
            ->active()
            ->parent()
            ->orderBy('order')
            ->get();

        // Filter menus based on permissions
        return $menus->filter(function($menu) use ($user) {
            return $this->hasAccessToMenu($menu, $user);
        });
    }

    private function hasAccessToMenu(Menu $menu, $user)
    {
        // Jika menu memiliki children, cek apakah ada children yang accessible
        if ($menu->children->isNotEmpty()) {
            $accessibleChildren = $menu->children->filter(function($child) use ($user) {
                return $this->hasAccessToSingleMenu($child, $user);
            });
            
            return $accessibleChildren->isNotEmpty();
        }

        // Untuk menu tanpa children
        return $this->hasAccessToSingleMenu($menu, $user);
    }

    private function hasAccessToSingleMenu(Menu $menu, $user)
    {
        // Jika menu tidak memiliki permission requirements, tampilkan
        if ($menu->permissions->isEmpty()) {
            return true;
        }

        // Cek apakah user memiliki salah satu permission yang required
        return $user->hasAnyPermission($menu->permissions->pluck('name')->toArray());
    }

    public function assignPermissionToMenu($menuId, $permissionNames)
    {
        $menu = Menu::findOrFail($menuId);
        
        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permissions[] = $permission->id;
            }
        }

        $menu->permissions()->sync($permissions);
    }
}