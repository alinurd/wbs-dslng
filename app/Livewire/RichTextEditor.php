<?php

namespace App\Livewire;

use Livewire\Component;

class RichTextEditor extends Component
{
    public $model;
    public $content = '';
    public $editorId;
    public $placeholder = 'Ketik sesuatu...';
    public $height = '300px';
    public $toolbar = 'full';

    public function mount($model, $content = '', $placeholder = 'Ketik sesuatu...', $height = '300px', $toolbar = 'full')
    {
        $this->model = $model;
        $this->content = $content;
        $this->placeholder = $placeholder;
        $this->height = $height;
        $this->toolbar = $toolbar;
        $this->editorId = 'quill-editor-' . uniqid();
    }

    public function render()
    {
        return view('livewire.rich-text-editor');
    }
}