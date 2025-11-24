<?php

namespace App\Providers;

use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MenuService::class, function ($app) {
            return new MenuService();
        });
    }
    
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
            $user = auth()->user();

            $hasMenuAccess = function ($menu) use ($user) {
                if (!$user) return false;
                if (!$user->email_verified_at && $user->role_id == 1) {
                            return false;
                        }
 
                // Menu default 2 selalu tampil untuk semua role
                if ($menu->default == 2) {
                    return true;
                }

                // Menu default 1 hanya untuk role 1
                if ($menu->default == 1 && $user->role_id == 1) {
                    return true;
                }

                // Menu dengan route: cek permission
                $permissionBase = str_replace('.', '-', $menu->slug);
                // dd($permissionBase);
                if ($menu->route) {
                    return $user->can($permissionBase . '.view');
                }
                // Parent menu tanpa route: tampil jika ada children yang bisa diakses
                return $menu->children->isNotEmpty();
            };

            // Ambil menu parent
            $menus = Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)->orderBy('order');
                }])
                ->get()
                ->map(function ($menu) use ($hasMenuAccess) {
                    // Filter children
                    $menu->children = $menu ->children->filter(fn($child) => $hasMenuAccess($child));
                    return $menu;
                })
                // Filter parent menu sesuai akses
                ->filter(fn($menu) => $hasMenuAccess($menu));
// dd($menus);
            // Bagikan ke semua view
            $view->with([
                'menus' => $menus,
               'user' => $user,
               'userRole' => $user?$user->roles()->get()->pluck('name', 'id')->toArray():[],
                'module_permissions' => module_permissions('dashboard'),
            ]);
        });
    }
}
