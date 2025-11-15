<?php

namespace App\Livewire;

use App\Livewire\Root;

class CommentModal extends Root  // Ganti nama class
{
    public $show = false;
    public $data = [];
    public $trackingId;
    public $messages = [];
    public $newMessage = ''; // Tambahkan ini

    protected $listeners = ['openDetailModal' => 'openModal'];

    public function openModal($data)
    {
        $this->data = $data;
        $this->trackingId = $data['id'] ?? null;
        $this->show = true;
        $this->loadMessages();
    }

    public function closeModal()
    {
        $this->show = false;
        $this->data = [];
        $this->messages = [];
        $this->newMessage = '';
        $this->trackingId = null;
    }

    public function render()
    {
        return view('livewire.components.comment'); // Update view name
    }
}