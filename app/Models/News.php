<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news';
    
    protected $fillable = [
        'code_news',
        'category',
        'title_id',
        'title_en', 
        'content_id',
        'content_en',
        'image',
        'files',
        'is_active',
        'views',
        'publish_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'files' => 'array',
        'is_active' => 'boolean',
        'publish_date' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'views' => 0,
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function categoryData()
    {
        return $this->belongsTo(Combo::class, 'category', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('publish_date', '<=', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'en' ? $this->title_en : $this->title_id;
    }

    public function getContentAttribute()
    {
        return app()->getLocale() === 'en' ? $this->content_en : $this->content_id;
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getFilesListAttribute()
    {
        if (empty($this->files) || !is_array($this->files)) {
            return [];
        }

        return array_map(function ($file) {
            return [
                'name' => $file['name'] ?? basename($file['path']),
                'path' => asset('storage/' . $file['path']),
                'size' => $file['size'] ?? 0,
                'mime_type' => $file['mime_type'] ?? 'application/octet-stream',
            ];
        }, $this->files);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function isPublished()
    {
        return $this->publish_date && $this->publish_date <= now();
    }
}