<?php

namespace App\Livewire\Blog;

use App\Livewire\Base\AppBaseList;
use App\Models\Post;

class PostList extends AppBaseList
{
    public $modelClass = Post::class;

    public function render()
    {
        return view('livewire.blog.post-list', [
            'data' => $this->getRecords(),
        ]);
    }
}
