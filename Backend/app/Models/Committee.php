<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Parent relationship
    public function parent()
    {
        return $this->belongsTo(Committee::class, 'parent_id');
    }

    // Children relationship with recursive eager loading
    public function children()
    {
        return $this->hasMany(Committee::class, 'parent_id')
            ->with('children');
    }

    // User who last updated this committee
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    // Members through user_committees pivot table
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'user_committees',
            'committee_id',
            'user_id'
        )
            ->withPivot('order_no', 'updated_by')
            ->withTimestamps()
            ->orderBy('order_no');
    }

    // Helper: Get full committee path (e.g., "Parent > Child > Sub-child")
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    // Helper: Check if committee has children
    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    // Helper: Get total descendants count
    public function getDescendantsCountAttribute()
    {
        $count = 0;
        foreach ($this->children as $child) {
            $count += 1 + $child->descendants_count;
        }
        return $count;
    }

    // Helper: Get members count
    public function getMembersCountAttribute()
    {
        return $this->members()->count();
    }
}