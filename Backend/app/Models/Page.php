<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_photo',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // public function updater(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'updated_by', 'user_id');
    // }
}
