<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    
    protected $fillable = [];

   

    public function for()
    {
        return $this->belongsTo(User::class);
    }
    public function to()
    {
        return $this->belongsTo(User::class);
    }
}