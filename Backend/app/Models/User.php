<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'full_name',
        'slug',
        'email',
        'designation',
        'password',
        'address',
        'profile_description',
        'fb_link',
        'youtube_link',
        'tiktok_link',
        'photo_path',
        'type',
        'started_date',
        'ended_date',
        'member_type',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'started_date' => 'date',
        'ended_date' => 'date',
    ];

    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }

    // Fixed: committees relationship matching user_committees table
    public function committees(): BelongsToMany
    {
        return $this->belongsToMany(
            Committee::class,
            'user_committees',
            'user_id',      // foreign pivot key on user_committees
            'committee_id'  // related pivot key on user_committees
        )
            ->withPivot('order_no', 'updated_by')
            ->withTimestamps();
    }

    // Fixed: programs relationship matching user_programs table
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(
            Program::class,
            'user_programs',
            'user_id',      // foreign pivot key on user_programs
            'program_id'    // related pivot key on user_programs
        )
            ->withTimestamps();
    }

    // Fixed: posts relationship
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id', 'user_id');
    }

    // Scope for active members only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for lifetime members
    public function scopeLifetime($query)
    {
        return $query->where('member_type', 'lifetime');
    }

    // Get full name accessor
    public function getNameAttribute()
    {
        return $this->full_name;
    }
}