<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
     protected $fillable = [
        'name',
        'kelompok',
        'data',
        'data_id',
        'data_en',
        'param_int',
        'param_str',
        'is_active',
    ];
}
