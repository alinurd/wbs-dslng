<?php

namespace App\Livewire\Components;

use Livewire\Component;

class AppModal extends Component
{
    public $show = false;
    public $title = '';
    public $size = 'md'; // sm | md | lg | xl
    public $confirmText = 'Simpan';
    public $cancelText = 'Batal';
    public $disableConfirm = false;

    // event callback ke parent
    public $onConfirm = null;
    public $onCancel = null;

    protected $listeners = [
        'openModal' => 'open',
        'closeModal' => 'close'
    ];

    public function open($params = [])
    {
        $this->title = $params['title'] ?? $this->title;
        $this->size = $params['size'] ?? $this->size;
        $this->confirmText = $params['confirmText'] ?? $this->confirmText;
        $this->cancelText = $params['cancelText'] ?? $this->cancelText;
        $this->onConfirm = $params['onConfirm'] ?? null;
        $this->onCancel = $params['onCancel'] ?? null;

        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function confirm()
    {
        if ($this->onConfirm) {
            $this->dispatch($this->onConfirm);
        }
        $this->close();
    }

    public function cancel()
    {
        if ($this->onCancel) {
            $this->dispatch($this->onCancel);
        }
        $this->close();
    }

    public function render()
    {
        return view('livewire.components.app-modal');
    }
}
