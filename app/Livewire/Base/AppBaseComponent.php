<?php

namespace App\Livewire\Base;

use App\Livewire\Base\Traits\WithAlerts;
use App\Livewire\Base\Traits\WithPermissions;
use Livewire\Component;

class AppBaseComponent extends Component
{
    use WithPermissions, WithAlerts;

    public $user;
    public $permissions = [];

    public function mount()
    {
        $this->user = auth()->user();
        $this->permissions = $this->getUserPermissions();
    }

    protected function getUserPermissions()
    {
        if (!$this->user) {
            return [
                'role' => 'guest',
                'can_view' => false,
                'can_edit' => false,
                'can_delete' => false,
            ];
        }

        // Sementara hardcoded (nanti diganti RBAC)
        return [
            'role' => $this->user->role ?? 'user',
            'can_view' => true,
            'can_edit' => true,
            'can_delete' => false,
        ];
    }
}
