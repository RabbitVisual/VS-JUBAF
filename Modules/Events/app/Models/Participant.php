<?php

namespace Modules\Events\App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    protected $fillable = [
        'registration_id',
        'registration_segment_id',
        'name',
        'email',
        'birth_date',
        'document',
        'phone',
        'custom_responses',
        'checked_in',
        'checked_in_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'custom_responses' => 'array',
        'checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    /**
     * Get the registration
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'registration_id');
    }

    /**
     * Get the registration segment (when event uses segments)
     */
    public function registrationSegment(): BelongsTo
    {
        return $this->belongsTo(EventRegistrationSegment::class, 'registration_segment_id');
    }

    /**
     * Get age from birth_date
     */
    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->birth_date)->age;
    }

    /**
     * Calculate price based on age and event price rules
     */
    public function calculatePrice(): float
    {
        $event = $this->registration->event;
        $age = $this->age;

        foreach ($event->priceRules as $rule) {
            if ($rule->matchesAge($age)) {
                return (float) $rule->price;
            }
        }

        return 0.0; // Default price if no rule matches
    }

    /**
     * Scope for checked in participants
     */
    public function scopeCheckedIn($query)
    {
        return $query->where('checked_in', true);
    }

    /**
     * Scope for not checked in participants
     */
    public function scopeNotCheckedIn($query)
    {
        return $query->where('checked_in', false);
    }
}
