<?php

namespace App\Providers;

use App\Models\Menu;
use App\Services\EmailService;
use App\Services\MenuService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MenuService::class, function ($app) {
            return new MenuService();
        });
        
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService();
        });
    }
    
    public function boot()
    {
        // Set locale at boot time
        $this->setLocale();
        
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
                if (!$user->email_verified_at ) {
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
                    $menu->children = $menu->children->filter(fn($child) => $hasMenuAccess($child));
                    return $menu;
                })
                // Filter parent menu sesuai akses
                ->filter(fn($menu) => $hasMenuAccess($menu));

            // Bagikan ke semua view
            $view->with([
                'menus' => $menus,
                'user' => $user,
                'userRole' => $user ? $user->roles()->get()->pluck('name', 'id')->toArray() : [],
                'module_permissions' => module_permissions('dashboard'),
                'currentLocale' => App::getLocale(),
            ]);
        });
    }
    
    /**
     * Set application locale
     */
    protected function setLocale()
    {
        // Use middleware for locale instead, or handle in boot method
        // This is a safer approach
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
    }
    
    /**
     * Change language method (should be in a controller or middleware)
     * This method shouldn't be in service provider
     */
    public function changeLanguage($lang)
    {
        // This method should be moved to a controller or middleware
        // Service providers are not meant for request handling
        Session::put('locale', $lang);
        App::setLocale($lang);
    }
}