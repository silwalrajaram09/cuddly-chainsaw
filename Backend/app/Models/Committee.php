<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'updated_by',
    ];
    public function parent()
    {
        return $this->belongsTo(Committee::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Committee::class, 'parent_id')
                    ->with('children'); // recursive eager load
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function members()
    {
        return $this->belongsToMany(User::class, 'user_committee', 'committee_id', 'user_id')
                    ->withPivot('order_no')
                    ->orderByPivot('order_no');
    }
}