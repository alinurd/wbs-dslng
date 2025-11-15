<?php

namespace App\Models;  // Pastikan namespace Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'message'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan Pengaduan
    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}