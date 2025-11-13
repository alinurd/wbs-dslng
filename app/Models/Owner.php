<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
            protected $guarded = [];

            public function parent()
{
    return $this->belongsTo(Owner::class, 'parent_id');
}

}
