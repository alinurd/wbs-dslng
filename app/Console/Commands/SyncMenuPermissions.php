<?php

namespace App\Console\Commands;

use App\Models\Menu;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class SyncMenuPermissions extends Command
{
    protected $signature = 'app:sync-menu-permissions';
    protected $description = 'Sinkronisasi permission berdasarkan data menu';

    public function handle(): void
    {
        $actions = ['view', 'create', 'edit', 'delete', 'index'];

        $menus = Menu::all();

        foreach ($menus as $menu) {
            foreach ($actions as $action) {
                $permissionName = "{$menu->slug}.{$action}";
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        $this->info('✅ Menu permissions synchronized successfully!');
    }
}
