<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPriceRule extends Model
{
    protected $fillable = [
        'event_id',
        'registration_segment_id',
        'label',
        'min_age',
        'max_age',
        'price',
        'order',
        'rule_type',
        'member_status',
        'participant_type',
        'gender',
        'church_membership',
        'date_from',
        'date_to',
        'min_participants',
        'max_participants',
        'location',
        'discount_code',
        'discount_percentage',
        'discount_fixed',
        'is_active',
        'priority',
        'conditions',
    ];

    protected $casts = [
        'min_age'              => 'integer',
        'max_age'              => 'integer',
        'price'                => 'decimal:2',
        'order'                => 'integer',
        'is_active'            => 'boolean',
        'priority'             => 'integer',
        'conditions'           => 'array',
        'discount_percentage'  => 'decimal:2',
        'discount_fixed'       => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Rule Type constants
    // ─────────────────────────────────────────────────────────────────────────
    const RULE_TYPE_AGE_RANGE          = 'age_range';
    const RULE_TYPE_MEMBER_STATUS      = 'member_status';
    const RULE_TYPE_PARTICIPANT_TYPE   = 'participant_type';
    const RULE_TYPE_REGISTRATION_DATE  = 'registration_date';
    const RULE_TYPE_GROUP_SIZE         = 'group_size';
    const RULE_TYPE_LOCATION           = 'location';
    const RULE_TYPE_DISCOUNT_CODE      = 'discount_code';
    const RULE_TYPE_BULK_DISCOUNT      = 'bulk_discount';
    const RULE_TYPE_EARLY_BIRD         = 'early_bird';
    const RULE_TYPE_LAST_MINUTE        = 'last_minute';
    const RULE_TYPE_GENDER             = 'gender';
    const RULE_TYPE_CHURCH_MEMBERSHIP  = 'church_membership';

    // ─────────────────────────────────────────────────────────────────────────
    // Member status options (used by member_status and church_membership rules)
    // ─────────────────────────────────────────────────────────────────────────
    const MEMBER_ACTIVE    = 'ativo';
    const MEMBER_VISITOR   = 'visitante';
    const MEMBER_BAPTIZED  = 'batizado';
    const MEMBER_WORKER    = 'obreiro';
    const MEMBER_DEACON    = 'diacono';
    const MEMBER_PASTOR    = 'pastor';

    public static function getMemberStatusOptions(): array
    {
        return [
            self::MEMBER_ACTIVE   => 'Membro Ativo',
            self::MEMBER_BAPTIZED => 'Batizado',
            self::MEMBER_WORKER   => 'Obreiro',
            self::MEMBER_DEACON   => 'Diácono',
            self::MEMBER_PASTOR   => 'Pastor/Ministro',
            self::MEMBER_VISITOR  => 'Visitante',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Available rule types (display)
    // ─────────────────────────────────────────────────────────────────────────
    public static function getRuleTypes(): array
    {
        return [
            self::RULE_TYPE_AGE_RANGE         => 'Faixa Etária',
            self::RULE_TYPE_MEMBER_STATUS     => 'Status de Membro',
            self::RULE_TYPE_CHURCH_MEMBERSHIP => 'Membresia Batista',
            self::RULE_TYPE_PARTICIPANT_TYPE  => 'Tipo de Participante',
            self::RULE_TYPE_GENDER            => 'Gênero',
            self::RULE_TYPE_REGISTRATION_DATE => 'Data da Inscrição',
            self::RULE_TYPE_GROUP_SIZE        => 'Tamanho do Grupo',
            self::RULE_TYPE_LOCATION          => 'Localização',
            self::RULE_TYPE_DISCOUNT_CODE     => 'Código Promocional',
            self::RULE_TYPE_BULK_DISCOUNT     => 'Desconto por Volume',
            self::RULE_TYPE_EARLY_BIRD        => 'Early Bird (Antecipado)',
            self::RULE_TYPE_LAST_MINUTE       => 'Last Minute (Última Hora)',
        ];
    }

    /**
     * For each rule type, which data it uses and whether it is in the default form or needs an extra field.
     */
    public static function getRuleRequiredData(): array
    {
        return [
            self::RULE_TYPE_AGE_RANGE => [
                'label'      => 'Idade',
                'source'     => 'default',
                'field_hint' => null,
                'icon'       => 'user',
                'color'      => 'blue',
            ],
            self::RULE_TYPE_MEMBER_STATUS => [
                'label'      => 'Status de membro',
                'source'     => 'form_field',
                'field_hint' => 'member_status',
                'icon'       => 'id-card',
                'color'      => 'green',
            ],
            self::RULE_TYPE_CHURCH_MEMBERSHIP => [
                'label'      => 'Tipo de membresia batista',
                'source'     => 'form_field',
                'field_hint' => 'church_membership',
                'icon'       => 'church',
                'color'      => 'indigo',
            ],
            self::RULE_TYPE_PARTICIPANT_TYPE => [
                'label'      => 'Tipo de participante',
                'source'     => 'form_field',
                'field_hint' => 'participant_type',
                'icon'       => 'users',
                'color'      => 'purple',
            ],
            self::RULE_TYPE_GENDER => [
                'label'      => 'Gênero do participante',
                'source'     => 'form_field',
                'field_hint' => 'gender (ativar campo Gênero no formulário)',
                'icon'       => 'venus-mars',
                'color'      => 'pink',
            ],
            self::RULE_TYPE_DISCOUNT_CODE => [
                'label'      => 'Código promocional',
                'source'     => 'form_field',
                'field_hint' => 'discount_code ou codigo_promocional',
                'icon'       => 'tag',
                'color'      => 'amber',
            ],
            self::RULE_TYPE_GROUP_SIZE => [
                'label'      => 'Quantidade de participantes',
                'source'     => 'default',
                'field_hint' => null,
                'icon'       => 'users-group',
                'color'      => 'teal',
            ],
            self::RULE_TYPE_LOCATION => [
                'label'      => 'Localização',
                'source'     => 'form_field',
                'field_hint' => 'location',
                'icon'       => 'location-dot',
                'color'      => 'red',
            ],
            self::RULE_TYPE_EARLY_BIRD => [
                'label'      => 'Data da inscrição (antecipado)',
                'source'     => 'default',
                'field_hint' => null,
                'icon'       => 'clock',
                'color'      => 'sky',
            ],
            self::RULE_TYPE_LAST_MINUTE => [
                'label'      => 'Data da inscrição (último momento)',
                'source'     => 'default',
                'field_hint' => null,
                'icon'       => 'fire',
                'color'      => 'orange',
            ],
            self::RULE_TYPE_REGISTRATION_DATE => [
                'label'      => 'Intervalo de data da inscrição',
                'source'     => 'default',
                'field_hint' => null,
                'icon'       => 'calendar-days',
                'color'      => 'slate',
            ],
            self::RULE_TYPE_BULK_DISCOUNT => [
                'label'      => 'Volume de participantes',
                'source'     => 'default',
                'field_hint' => null,
                'icon'       => 'layer-group',
                'color'      => 'violet',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function registrationSegment(): BelongsTo
    {
        return $this->belongsTo(EventRegistrationSegment::class, 'registration_segment_id');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────
    public function scopeForSegment($query, $segmentId)
    {
        return $query->where('registration_segment_id', $segmentId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('registration_segment_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'asc');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Matching logic
    // ─────────────────────────────────────────────────────────────────────────
    public function matchesAge(int $age): bool
    {
        $minMatch = $this->min_age === null || $age >= $this->min_age;
        $maxMatch = $this->max_age === null || $age <= $this->max_age;
        return $minMatch && $maxMatch;
    }

    public function matchesParticipant(array $participantData, array $registrationData = []): bool
    {
        if (!$this->is_active) return false;

        switch ($this->effective_rule_type) {
            case self::RULE_TYPE_AGE_RANGE:
                return $this->matchesAge($participantData['age'] ?? null);

            case self::RULE_TYPE_MEMBER_STATUS:
                return ($participantData['member_status'] ?? null) === $this->member_status;

            case self::RULE_TYPE_CHURCH_MEMBERSHIP:
                return ($participantData['church_membership'] ?? null) === $this->church_membership;

            case self::RULE_TYPE_PARTICIPANT_TYPE:
                return ($participantData['participant_type'] ?? null) === $this->participant_type;

            case self::RULE_TYPE_GENDER:
                return ($participantData['gender'] ?? null) === $this->gender;

            case self::RULE_TYPE_REGISTRATION_DATE:
                $registrationDate = $registrationData['created_at'] ?? now();
                return $this->matchesDateRange($registrationDate);

            case self::RULE_TYPE_GROUP_SIZE:
                $participantCount = $registrationData['participant_count'] ?? 1;
                return $this->matchesGroupSize($participantCount);

            case self::RULE_TYPE_LOCATION:
                return ($participantData['location'] ?? null) === $this->location;

            case self::RULE_TYPE_DISCOUNT_CODE:
                return ($registrationData['discount_code'] ?? null) === $this->discount_code;

            case self::RULE_TYPE_EARLY_BIRD:
            case self::RULE_TYPE_LAST_MINUTE:
                $registrationDate = $registrationData['created_at'] ?? now();
                return $this->matchesDateRange($registrationDate);

            case self::RULE_TYPE_BULK_DISCOUNT:
                $participantCount = $registrationData['participant_count'] ?? 1;
                return $this->matchesGroupSize($participantCount);

            default:
                return false;
        }
    }

    protected function matchesDateRange($date): bool
    {
        if (!$date) return false;
        $checkDate = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        if ($this->date_from && $checkDate->lt($this->date_from)) return false;
        if ($this->date_to && $checkDate->gt($this->date_to)) return false;
        return true;
    }

    protected function matchesGroupSize(int $count): bool
    {
        if ($this->min_participants !== null && $count < $this->min_participants) return false;
        if ($this->max_participants !== null && $count > $this->max_participants) return false;
        return true;
    }

    public function calculatePrice(float $basePrice): float
    {
        $finalPrice = $basePrice;
        if ($this->discount_percentage) {
            $finalPrice -= $finalPrice * ($this->discount_percentage / 100);
        }
        if ($this->discount_fixed) {
            $finalPrice -= $this->discount_fixed;
        }
        if ($this->price !== null && $this->price >= 0) {
            $finalPrice = $this->price;
        }
        return max(0, $finalPrice);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────────────────────────────────
    public function getRuleTypeDisplayAttribute(): string
    {
        $type = $this->effective_rule_type;
        return self::getRuleTypes()[$type] ?? (string) ($type ?? 'Padrão');
    }

    public function getEffectiveRuleTypeAttribute(): string
    {
        if ($this->rule_type) return $this->rule_type;

        // Infer from data (Legacy Data Support)
        if ($this->min_age !== null || $this->max_age !== null) return self::RULE_TYPE_AGE_RANGE;
        if ($this->member_status !== null) return self::RULE_TYPE_MEMBER_STATUS;
        if ($this->participant_type !== null) return self::RULE_TYPE_PARTICIPANT_TYPE;
        if ($this->discount_code !== null) return self::RULE_TYPE_DISCOUNT_CODE;
        if ($this->date_from !== null || $this->date_to !== null) return self::RULE_TYPE_REGISTRATION_DATE;

        return 'standard';
    }
}
