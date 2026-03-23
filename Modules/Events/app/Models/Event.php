<?php

namespace Modules\Events\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_path',
        'logo_path',
        'start_date',
        'end_date',
        'location',
        'location_data',
        'capacity',
        'status',
        'visibility',
        'form_fields',
        'schedule',
        'event_type_id',
        'ministry_id',
        'ministry_plan_id',
        'setlist_id',
        'created_by',
        'is_featured',
        'ticket_template_id',
        'options',
        'requires_diretoria_approval',
        'treasury_campaign_id',
        // Extended fields (v3)
        'target_audience',
        'min_age_restriction',
        'max_age_restriction',
        'dress_code',
        'registration_deadline',
        'max_per_registration',
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_whatsapp',
        'recurrence_type',
        'default_required_fields',
        'theme_config',
    ];

    protected $casts = [
        'start_date'             => 'datetime',
        'end_date'               => 'datetime',
        'registration_deadline'  => 'datetime',
        'location_data'          => 'array',
        'form_fields'            => 'array',
        'schedule'               => 'array',
        'target_audience'        => 'array',
        'default_required_fields' => 'array',
        'capacity'               => 'integer',
        'min_age_restriction'    => 'integer',
        'max_age_restriction'    => 'integer',
        'max_per_registration'   => 'integer',
        'is_featured'            => 'boolean',
        'options'                => 'array',
        'requires_diretoria_approval' => 'boolean',
        'theme_config'           => 'array',
    ];

    /**
     * Get theme config attribute ensuring default structure.
     */
    public function getThemeConfigAttribute($value)
    {
        $config = $value ? json_decode($value, true) : [];
        if (!is_array($config)) {
            $config = [];
        }

        return [
            'theme'           => $config['theme'] ?? 'modern',
            'primary_color'   => $config['primary_color'] ?? '#4F46E5',
            'secondary_color' => $config['secondary_color'] ?? '#111827',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Status
    // ─────────────────────────────────────────────────────────────────────────
    const STATUS_DRAFT            = 'draft';
    const STATUS_PUBLISHED        = 'published';
    const STATUS_CLOSED           = 'closed';
    const STATUS_WAITING_APPROVAL = 'waiting_approval';

    // ─────────────────────────────────────────────────────────────────────────
    // Visibility
    // ─────────────────────────────────────────────────────────────────────────
    const VISIBILITY_PUBLIC  = 'public';
    const VISIBILITY_MEMBERS = 'members';
    const VISIBILITY_BOTH    = 'both';

    // ─────────────────────────────────────────────────────────────────────────
    // Target Audience
    // ─────────────────────────────────────────────────────────────────────────
    const AUDIENCE_CHILDREN  = 'children';
    const AUDIENCE_TEENS     = 'teens';
    const AUDIENCE_YOUTH     = 'youth';
    const AUDIENCE_ADULTS    = 'adults';
    const AUDIENCE_ELDERLY   = 'elderly';
    const AUDIENCE_FAMILIES  = 'families';
    const AUDIENCE_COUPLES   = 'couples';
    const AUDIENCE_WOMEN     = 'women';
    const AUDIENCE_MEN       = 'men';
    const AUDIENCE_LEADERS   = 'leaders';
    const AUDIENCE_EVERYONE  = 'everyone';

    public static function getAudienceOptions(): array
    {
        return [
            self::AUDIENCE_EVERYONE  => 'Todos',
            self::AUDIENCE_CHILDREN  => 'Crianças (0-12)',
            self::AUDIENCE_TEENS     => 'Adolescentes (13-17)',
            self::AUDIENCE_YOUTH     => 'Jovens (18-35)',
            self::AUDIENCE_ADULTS    => 'Adultos',
            self::AUDIENCE_ELDERLY   => 'Melhor Idade',
            self::AUDIENCE_FAMILIES  => 'Famílias',
            self::AUDIENCE_COUPLES   => 'Casais',
            self::AUDIENCE_WOMEN     => 'Mulheres',
            self::AUDIENCE_MEN       => 'Homens',
            self::AUDIENCE_LEADERS   => 'Líderes',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dress Code
    // ─────────────────────────────────────────────────────────────────────────
    const DRESS_CODE_FREE       = 'Livre';
    const DRESS_CODE_CASUAL     = 'Casual';
    const DRESS_CODE_SPORTS     = 'Esportivo';
    const DRESS_CODE_SOCIAL     = 'Social';
    const DRESS_CODE_FORMAL     = 'Formal';
    const DRESS_CODE_RELIGIOUS  = 'Traje de Culto';

    public static function getDressCodeOptions(): array
    {
        return [
            self::DRESS_CODE_FREE      => 'Livre',
            self::DRESS_CODE_CASUAL    => 'Casual',
            self::DRESS_CODE_SPORTS    => 'Esportivo',
            self::DRESS_CODE_SOCIAL    => 'Social',
            self::DRESS_CODE_FORMAL    => 'Formal',
            self::DRESS_CODE_RELIGIOUS => 'Traje de Culto',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Recurrence
    // ─────────────────────────────────────────────────────────────────────────
    const RECURRENCE_WEEKLY  = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY  = 'yearly';

    public static function getRecurrenceOptions(): array
    {
        return [
            ''                        => 'Sem recorrência (evento único)',
            self::RECURRENCE_WEEKLY   => 'Semanal',
            self::RECURRENCE_MONTHLY  => 'Mensal',
            self::RECURRENCE_YEARLY   => 'Anual',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Fields control: quais campos são exibidos/obrigatórios no formulário
    // Valores possíveis para cada chave: 'required' | 'optional' | 'disabled'
    // ─────────────────────────────────────────────────────────────────────────
    public static function getAvailableFormFields(): array
    {
        return [
            'name'       => 'Nome completo',
            'email'      => 'E-mail',
            'phone'      => 'Telefone / WhatsApp',
            'birth_date' => 'Data de nascimento',
            'gender'     => 'Gênero',
            'cpf'        => 'CPF',
            'rg'         => 'RG',
            'church'     => 'Igreja que congrega',
            'city'       => 'Cidade',
            'state'      => 'Estado',
            'address'    => 'Endereço completo',
            'shirt_size' => 'Tamanho da camiseta',
            'food_restrictions' => 'Restrições alimentares',
            'emergency_contact' => 'Contato de emergência',
        ];
    }

    /** Default required fields status when not set */
    public static function defaultRequiredFields(): array
    {
        return [
            'name'       => 'required',
            'email'      => 'required',
            'phone'      => 'optional',
            'birth_date' => 'optional',
            'gender'     => 'disabled',
            'cpf'        => 'disabled',
            'rg'         => 'disabled',
            'church'     => 'disabled',
            'city'       => 'disabled',
            'state'      => 'disabled',
            'address'    => 'disabled',
            'shirt_size' => 'disabled',
            'food_restrictions' => 'disabled',
            'emergency_contact' => 'disabled',
        ];
    }

    /** Get the effective required_fields for this event (merged with defaults) */
    public function getEffectiveRequiredFields(): array
    {
        $defaults = self::defaultRequiredFields();
        $saved = is_array($this->default_required_fields) ? $this->default_required_fields : [];
        return array_merge($defaults, $saved);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Page options defaults
    // ─────────────────────────────────────────────────────────────────────────
    public static function defaultOptions(): array
    {
        return [
            'has_badge'       => false,
            'has_certificate' => false,
            'has_checkin'     => true,
            'has_ticket'      => true,
            'show_schedule'   => true,
            'show_speakers'   => true,
            'show_about'      => true,
            'show_location'   => true,
            'show_map'        => true,
            'show_capacity'   => true,
            'show_cover'      => true,
            'show_contact'    => true,
            'show_audience'   => true,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Boot
    // ─────────────────────────────────────────────────────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function ministryPlan(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\MinistryPlan::class, 'ministry_plan_id');
    }

    public function setlist(): BelongsTo
    {
        if (class_exists(\Modules\Worship\App\Models\WorshipSetlist::class)) {
            return $this->belongsTo(\Modules\Worship\App\Models\WorshipSetlist::class, 'setlist_id');
        }

        return $this->belongsTo(User::class, 'setlist_id', 'id')->whereRaw('1 = 0');
    }

    public function treasuryCampaign(): BelongsTo
    {
        return $this->belongsTo(\Modules\Treasury\App\Models\Campaign::class, 'treasury_campaign_id');
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class, 'event_id')->orderBy('order');
    }

    public function registrationSegments(): HasMany
    {
        return $this->hasMany(EventRegistrationSegment::class, 'event_id')->orderBy('order');
    }

    public function priceRules(): HasMany
    {
        return $this->hasMany(EventPriceRule::class, 'event_id')->orderBy('order');
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(EventCoupon::class, 'event_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(EventBatch::class, 'event_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->registrations()->where('status', EventRegistration::STATUS_CONFIRMED);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(EventCertificate::class, 'event_id');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(EventBadge::class, 'event_id');
    }

    public function getBadgeTemplate(): ?EventBadge
    {
        return $this->badges()->first();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────────────────────────────────

    public function getTotalParticipantsAttribute(): int
    {
        return \Modules\Events\App\Models\Participant::whereHas('registration', function ($q) {
            $q->where('event_id', $this->id)->where('status', 'confirmed');
        })->count();
    }

    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT            => 'Rascunho',
            self::STATUS_PUBLISHED        => 'Publicado',
            self::STATUS_CLOSED           => 'Encerrado',
            self::STATUS_WAITING_APPROVAL => 'Aguardando Conselho',
            default                       => 'Rascunho'
        };
    }

    public function getVisibilityDisplayAttribute(): string
    {
        return match ($this->visibility) {
            self::VISIBILITY_PUBLIC  => 'Público',
            self::VISIBILITY_MEMBERS => 'Membros',
            self::VISIBILITY_BOTH    => 'Ambos',
            default                  => 'Público'
        };
    }

    public function getFormattedTimeAttribute(): ?string
    {
        return $this->start_date?->format('H:i');
    }

    public function getFormattedDateAttribute(): ?string
    {
        return $this->start_date?->translatedFormat('d/m/Y');
    }

    public function getIsActiveAttribute(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }
        if ($this->end_date) {
            return $this->end_date->isFuture() || $this->end_date->isToday();
        }
        if ($this->start_date) {
            return $this->start_date->greaterThanOrEqualTo(now()->subHours(6));
        }
        return false;
    }

    public function getIsRegistrationOpenAttribute(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) return false;
        if ($this->registration_deadline && $this->registration_deadline->isPast()) return false;
        return true;
    }

    public function getAudienceDisplayAttribute(): string
    {
        if (empty($this->target_audience)) return 'Todos';
        $options = self::getAudienceOptions();
        $labels = array_map(fn($key) => $options[$key] ?? $key, (array) $this->target_audience);
        return implode(', ', $labels);
    }

    public function getRecurrenceDisplayAttribute(): ?string
    {
        return match ($this->recurrence_type) {
            self::RECURRENCE_WEEKLY  => 'Semanal',
            self::RECURRENCE_MONTHLY => 'Mensal',
            self::RECURRENCE_YEARLY  => 'Anual',
            default                  => null,
        };
    }

    public function getOptionsAttribute($value): array
    {
        $arr = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : []);
        return array_merge(self::defaultOptions(), is_array($arr) ? $arr : []);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Option helpers
    // ─────────────────────────────────────────────────────────────────────────
    public function hasBadgeEnabled(): bool          { return (bool) ($this->options['has_badge'] ?? false); }
    public function hasCertificateEnabled(): bool    { return (bool) ($this->options['has_certificate'] ?? false); }
    public function hasCheckinEnabled(): bool        { return (bool) ($this->options['has_checkin'] ?? true); }
    public function hasTicketEnabled(): bool         { return (bool) ($this->options['has_ticket'] ?? true); }
    public function showScheduleEnabled(): bool      { return (bool) ($this->options['show_schedule'] ?? true); }
    public function showSpeakersEnabled(): bool      { return (bool) ($this->options['show_speakers'] ?? true); }
    public function showAboutEnabled(): bool         { return (bool) ($this->options['show_about'] ?? true); }
    public function showLocationEnabled(): bool      { return (bool) ($this->options['show_location'] ?? true); }
    public function showMapEnabled(): bool           { return (bool) ($this->options['show_map'] ?? true); }
    public function showCapacityEnabled(): bool      { return (bool) ($this->options['show_capacity'] ?? true); }
    public function showCoverEnabled(): bool         { return (bool) ($this->options['show_cover'] ?? true); }
    public function showContactEnabled(): bool       { return (bool) ($this->options['show_contact'] ?? true); }
    public function showAudienceEnabled(): bool      { return (bool) ($this->options['show_audience'] ?? true); }

    // ─────────────────────────────────────────────────────────────────────────
    // Capacity helpers
    // ─────────────────────────────────────────────────────────────────────────
    public function hasCapacityAvailable(int $additionalParticipants = 1): bool
    {
        if ($this->capacity === null) return true;
        return ($this->total_participants + $additionalParticipants) <= $this->capacity;
    }

    public function isFree(): bool
    {
        $hasPaidBatches = $this->batches()->where('price', '>', 0)->exists();
        $hasPaidRules   = $this->priceRules()->where('price', '>', 0)->exists();
        return !$hasPaidBatches && !$hasPaidRules;
    }

    public function hasBatches(): bool
    {
        return $this->batches()->exists();
    }

    public function hasRegistrationSegments(): bool
    {
        return $this->registrationSegments()->exists();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePublic($query)
    {
        return $query->whereIn('visibility', [self::VISIBILITY_PUBLIC, self::VISIBILITY_BOTH]);
    }

    public function scopeMembers($query)
    {
        return $query->whereIn('visibility', [self::VISIBILITY_MEMBERS, self::VISIBILITY_BOTH]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->where('start_date', '>=', now())
                  ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
