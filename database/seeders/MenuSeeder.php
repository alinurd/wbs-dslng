<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'fa-solid fa-home',
                'route' => 'dashboard',
                'order' => 1,
            ],
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'icon' => 'fa-solid fa-users-cog',
                'route' => null,
                'order' => 2,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(['slug' => $menu['slug']], $menu);
        }

        // Tambahkan submenu untuk User Management
        $parent = Menu::where('slug', 'user-management')->first();

        if ($parent) {
            $submenus = [
                [
                    'name' => 'Users',
                    'slug' => 'users',
                    'icon' => 'fa-solid fa-user',
                    'route' => 'users.index',
                    'parent_id' => $parent->id,
                    'order' => 1,
                ],
                [
                    'name' => 'Roles',
                    'slug' => 'roles',
                    'icon' => 'fa-solid fa-user-shield',
                    'route' => 'roles.index',
                    'parent_id' => $parent->id,
                    'order' => 2,
                ],
                [
                    'name' => 'Permissions',
                    'slug' => 'permissions',
                    'icon' => 'fa-solid fa-key',
                    'route' => 'permissions.index',
                    'parent_id' => $parent->id,
                    'order' => 3,
                ],
            ];

            foreach ($submenus as $submenu) {
                Menu::firstOrCreate(['slug' => $submenu['slug']], $submenu);
            }
        }
    }
}
