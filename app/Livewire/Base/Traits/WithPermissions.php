<?php

namespace App\Livewire\Base\Traits;

trait WithPermissions
{
    public function can($action)
    {
        return $this->permissions["can_{$action}"] ?? false;
    }

    public function role()
    {
        return $this->permissions['role'] ?? 'guest';
    }
}
