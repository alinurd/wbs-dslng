<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    
    // Tambahkan semua kolom yang bisa diisi
    protected $fillable = [
        'sender_id',
        'to', 
        'type',
        'ref_id',
        'type_text',
        'is_read',   
        'title',
        'message',
        'created_at',
        'updated_at'
    ]; 
    // Cast is_read ke boolean
    protected $casts = [ 
        'type' => 'integer'
    ];
    
    // Relationship ke user pengirim
    public function sender()
    {
        return $this->belongsTo(User::class, 'for');
    }
    
    // Relationship ke user penerima
    public function receiver()
    {
        return $this->belongsTo(User::class, 'to');
    }
}