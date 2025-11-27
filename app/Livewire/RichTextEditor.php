<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class RichTextEditor extends Component
{
    public $content = '';
    public $editorId;
    public $placeholder = 'Ketik sesuatu...';
    public $height = '300px';
    public $toolbar = 'full';

    public function mount($content = '', $placeholder = 'Ketik sesuatu...', $height = '300px', $toolbar = 'full')
    {
        $this->content = $content;
        $this->placeholder = $placeholder;
        $this->height = $height;
        $this->toolbar = $toolbar;
        $this->editorId = 'quill-editor-' . uniqid();
    }

    public function updatedContent($value)
    {
        $this->dispatch('contentUpdated', $value);
    }

    #[On('setContent')]
    public function setContent($content)
    {
        $this->content = $content;
    }

    #[On('clear-editor')]
    public function clearEditor()
    {
        $this->content = '';
    }

    public function render()
    {
        return view('livewire.rich-text-editor');
    }
}