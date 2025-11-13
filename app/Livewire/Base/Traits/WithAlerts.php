<?php

namespace App\Livewire\Base\Traits;

trait WithAlerts
{
    public function alertSuccess($message)
    {
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function alertError($message)
    {
        $this->dispatch('notify', type: 'error', message: $message);
    }
}
