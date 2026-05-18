<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 */
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
        'start_date' => 'date',
        'end_date' => 'date',
        'no_of_days' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Fixed: members relationship matching user_programs table
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_programs',
            'program_id',   // foreign pivot key on user_programs
            'user_id'       // related pivot key on user_programs
        )
            ->withTimestamps();
    }

    // Alias for users relationship
    public function users(): BelongsToMany
    {
        return $this->members();
    }

    // Program status accessor
    public function getStatusAttribute()
    {
        $now = now();
        $startDate = $this->start_date;
        $endDate = $this->end_date;

        if ($startDate > $now) {
            return 'upcoming';
        }

        if ($endDate && $endDate < $now) {
            return 'completed';
        }

        return 'ongoing';
    }

    // Get program duration in days
    public function getDurationAttribute()
    {
        if ($this->no_of_days) {
            return $this->no_of_days;
        }

        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }

        return null;
    }

    // Scope: active programs (ongoing or upcoming)
    public function scopeActive($query)
    {
        return $query->where('end_date', '>=', now())
            ->orWhereNull('end_date');
    }

    // Scope: upcoming programs
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    // Scope: ongoing programs
    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->where('end_date', '>=', now())
                    ->orWhereNull('end_date');
            });
    }

    // Scope: completed programs
    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Scope: filter by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}