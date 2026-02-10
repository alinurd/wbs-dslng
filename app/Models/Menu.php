<?php
// app/Models/Menu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // HAPUS BARIS INI: use HasPermissions;

    protected $fillable = [
        'name', 'slug', 'icon', 'route', 'parent_id', 'order', 'is_active', 'default', 'name_en'
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function permissions()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'menu_permission', 'menu_id', 'permission_id');
    }

    // Scope untuk menu yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // Scope untuk menu parent
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    // Helper method untuk mengecek apakah user memiliki akses ke menu
    public function userHasAccess($user)
    {
        // Jika menu tidak memiliki permission requirements, return true
        if ($this->permissions->isEmpty()) {
            return true;
        }

        // Cek apakah user memiliki salah satu permission yang required
        return $user->hasAnyPermission($this->permissions->pluck('name')->toArray());
    }
}