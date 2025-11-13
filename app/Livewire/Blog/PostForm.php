<?php

namespace App\Livewire\Blog;

use App\Livewire\Dynamic\DynamicForm;
use App\Models\Post;

class PostForm extends DynamicForm
{
    public $modelClass = Post::class;

    public $customFields = [
        [
            'field_name' => 'seo_description',
            'label'      => 'SEO Description',
            'type'       => 'textarea',
            'rules'      => 'nullable|max:160',
            'order'      => 99
        ]
    ];

    public function mount($recordId = null)
    {
        parent::mount(
            model: 'posts',
            category: 'artikel',
            customFields: $this->customFields
        );

        if ($recordId) {
            $this->loadRecord($recordId);
        }
    }

    public function save()
    {
        parent::save();  
        $this->alertSuccess('Artikel berhasil disimpan.');
    }
}
