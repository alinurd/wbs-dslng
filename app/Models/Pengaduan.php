<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code_pengaduan',
        'perihal',
        'nama_terlapor',
        'jenis_pengaduan_id',
        'saluran_aduan_id',
        'direktorat',
        'email_pelapor',
        'telepon_pelapor',
        'waktu_kejadian',
        'tanggal_pengaduan',
        'uraian',
        'alamat_kejadian',
        'lampiran',
        'status',
        'user_id',
        'fwd_to',
        'ditutup_pada'
    ];

    protected $casts = [
        'waktu_kejadian' => 'datetime',
        'tanggal_pengaduan' => 'datetime',
        'ditutup_pada' => 'datetime',
        'lampiran' => 'array'
    ];

    // Relasi dengan combo untuk jenis pengaduan
    public function jenisPengaduan()
    {
        return $this->belongsTo(Combo::class, 'jenis_pengaduan_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi dengan combo untuk saluran aduan
    public function saluranAduan()
    {
        return $this->belongsTo(Combo::class, 'saluran_aduan_id');
    }

    // Relasi dengan owner untuk direktorat
    public function direktoratRelasi()
    {
        return $this->belongsTo(Owner::class, 'direktorat');
    }

    // PERBAIKAN: Gunakan App\Models\Comment
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class, 'pengaduan_id');
    }

    // Method untuk menambah komentar
    public function addComment($message, $userId = null)
    {
        return $this->comments()->create([
            'user_id' => $userId ?? auth()->id(),
            'message' => $message,
        ]);
    }
}