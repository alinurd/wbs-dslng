<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
     protected $fillable = [
        'name',
        'kelompok',
        'data',
        'param_int',
        'param_str',
        'is_active',
    ];
}
