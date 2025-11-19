<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogApproval extends Model
{
    protected $table = 'log_approval';
    
    protected $fillable = [
        'pengaduan_id',
        'user_id', 
        'status_id',
        'status_text',
        'status',
        'catatan',
        'file',
        'color',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // File is stored as JSON string, so we'll handle it manually
    ];

    // Relationship ke Pengaduan
    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    // Relationship ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk file (convert JSON string to array)
    public function getFileAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    // Mutator untuk file (convert array to JSON string)
    public function setFileAttribute($value)
    {
        $this->attributes['file'] = json_encode($value ?? []);
    }
}