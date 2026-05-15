<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'start_date',
        'end_date',
        'no_of_days',
        'location',
        'country_id',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'no_of_days'   => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'user_programs')
    //         ->withPivot(['role', 'order_no', 'updated_by'])
    //         ->withTimestamps();
    // }

    // public function updater(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'updated_by', 'user_id');
    // }
}
