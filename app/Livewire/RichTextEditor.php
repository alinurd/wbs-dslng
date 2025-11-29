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

    protected $listeners = [
        'refreshEditor' => 'refreshContent',
        'resetEditor' => 'resetContent'
    ];

    public function mount($model, $content = '', $placeholder = 'Ketik sesuatu...', $height = '300px', $toolbar = 'full')
    {
        $this->model = $model;
        $this->content = $content ?? '';
        $this->placeholder = $placeholder;
        $this->height = $height;
        $this->toolbar = $toolbar;
        $this->editorId = 'quill-editor-' . uniqid();
    }

    public function updatedContent($value)
    {
        // Update parent component property
        $this->dispatch('editorContentUpdated', 
            model: $this->model, 
            content: $value
        );
    }

    public function refreshContent($content)
    {
        $this->content = $content ?? '';
        
        // Dispatch event to update Quill editor via Alpine.js
        $this->dispatch('refreshQuill', 
            content: $this->content,
            editorId: $this->editorId
        );
    }

    public function resetContent()
    {
        $this->content = '';
        $this->dispatch('refreshQuill', 
            content: '',
            editorId: $this->editorId
        );
    }

    public function render()
    {
        return view('livewire.rich-text-editor');
    }
}