<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_photo',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // public function author(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'author_id', 'user_id');
    // }

    // public function updater(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'updated_by', 'user_id');
    // }

    //     public function scopePublished(Builder $q): Builder
    // {
    //     return $q->where('is_published', true)
    //              ->whereNotNull('published_at')
    //              ->where('published_at', '<=', now());
    // }
}
