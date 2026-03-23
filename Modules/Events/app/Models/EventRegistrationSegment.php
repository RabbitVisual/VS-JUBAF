<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRegistrationSegment extends Model
{
    protected $fillable = [
        'event_id',
        'label',
        'description',
        'gender',
        'min_age',
        'max_age',
        'quantity',
        'price',
        'price_rule_type',
        'price_rule_types',
        'form_fields',
        'documents_requested',
        'ask_phone',
        'order',
        'required_fields',
    ];

    protected $casts = [
        'min_age'            => 'integer',
        'max_age'            => 'integer',
        'quantity'           => 'integer',
        'price'              => 'float',
        'form_fields'        => 'array',
        'price_rule_types'   => 'array',
        'documents_requested' => 'array',
        'required_fields'    => 'array',
        'ask_phone'          => 'boolean',
        'order'              => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Gender constants
    // ─────────────────────────────────────────────────────────────────────────
    const GENDER_ALL    = 'all';
    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    public static function getGenderOptions(): array
    {
        return [
            self::GENDER_ALL    => 'Todos',
            self::GENDER_MALE   => 'Apenas Homens',
            self::GENDER_FEMALE => 'Apenas Mulheres',
        ];
    }

    /**
     * Document type keys available for documents_requested.
     */
    public static function getDocumentTypes(): array
    {
        return [
            'cpf'           => 'CPF',
            'rg'            => 'RG',
            'titulo_eleitor' => 'Título de Eleitor',
            'passaporte'    => 'Passaporte',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'registration_segment_id');
    }

    /**
     * Price rules configured inline for this segment.
     */
    public function priceRules(): HasMany
    {
        return $this->hasMany(EventPriceRule::class, 'registration_segment_id')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Get price rule types for this segment (array). Supports legacy price_rule_type.
     */
    public function getPriceRuleTypes(): array
    {
        if (!empty($this->price_rule_types) && is_array($this->price_rule_types)) {
            return $this->price_rule_types;
        }
        if (!empty($this->price_rule_type)) {
            return [$this->price_rule_type];
        }
        return [];
    }

    /**
     * Get the effective required_fields for this segment.
     * If null, inherits from the event; otherwise applies segment-level override.
     */
    public function getEffectiveRequiredFields(?Event $event = null): array
    {
        $base = $event ? $event->getEffectiveRequiredFields() : Event::defaultRequiredFields();
        $override = is_array($this->required_fields) ? $this->required_fields : [];
        return array_merge($base, $override);
    }

    /**
     * Get gender display name.
     */
    public function getGenderDisplayAttribute(): string
    {
        return self::getGenderOptions()[$this->gender ?? self::GENDER_ALL] ?? 'Todos';
    }

    /**
     * Check if age (in years) is within this segment's range.
     */
    public function matchesAge(int $age): bool
    {
        $minMatch = $this->min_age === null || $age >= $this->min_age;
        $maxMatch = $this->max_age === null || $age <= $this->max_age;
        return $minMatch && $maxMatch;
    }

    /**
     * Check if gender matches this segment restriction.
     */
    public function matchesGender(?string $gender): bool
    {
        if ($this->gender === self::GENDER_ALL || !$this->gender) {
            return true;
        }
        if (!$gender) {
            return true; // If no gender provided, don't restrict
        }
        return $this->gender === $gender;
    }
}
