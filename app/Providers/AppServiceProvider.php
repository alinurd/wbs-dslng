<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Blade directive untuk cek menu access
        Blade::if('canmenu', function ($routeName) {
            if (auth()->guest()) return false;
            
            $permissionBase = str_replace('.', '-', $routeName);
            return auth()->user()->can($permissionBase . '.view');
        });

        // Share menus data dengan semua views
        View::composer('*', function ($view) {
            // Helper function untuk cek permission menu
            $hasMenuAccess = function ($menu) {
                if ($menu->default) return true; // Menu default selalu tampil
                
                if ($menu->route) {
                    // Cek permission berdasarkan route name
                    $routeName = $menu->route;
                    $permissionBase = str_replace('.', '-', $routeName);
                    
                    // Cek view permission untuk route tersebut
                    return auth()->check() && auth()->user()->can($permissionBase . '.view');
                }
                
                // Untuk menu parent tanpa route, tampilkan jika ada children yang accessible
                return true;
            };

            // Get menus dengan filter permission
            $menus = Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)->orderBy('order');
                }])
                ->get()
                ->filter(function($menu) use ($hasMenuAccess) {
                    return $hasMenuAccess($menu);
                })
                ->map(function($menu) use ($hasMenuAccess) {
                    // Filter children berdasarkan permission juga
                    $menu->children = $menu->children->filter(function($child) use ($hasMenuAccess) {
                        return $hasMenuAccess($child);
                    });
                    return $menu;
                });

            $view->with('menus', $menus);
        });
    }
}