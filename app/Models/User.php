<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Bible\App\Traits\HasReadingProgress;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasReadingProgress, HasRoles, Notifiable, SoftDeletes {
        HasRoles::hasRole as private spatieHasRole;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['photo_url'];

    /**
     * Boot function from Laravel
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // Normalize legacy payloads so controllers/views can still send old keys.
            if (isset($user->attributes['first_name']) && ! isset($user->attributes['name'])) {
                $user->attributes['name'] = $user->attributes['first_name'];
            }
            if (isset($user->attributes['last_name']) && ! isset($user->attributes['sobrenome'])) {
                $user->attributes['sobrenome'] = $user->attributes['last_name'];
            }
            if (isset($user->attributes['date_of_birth']) && ! isset($user->attributes['data_nascimento'])) {
                $user->attributes['data_nascimento'] = $user->attributes['date_of_birth'];
            }
            if (isset($user->attributes['photo']) && ! isset($user->attributes['avatar'])) {
                $user->attributes['avatar'] = $user->attributes['photo'];
            }
            if ((isset($user->attributes['phone']) || isset($user->attributes['cellphone'])) && ! isset($user->attributes['whatsapp'])) {
                $user->attributes['whatsapp'] = $user->attributes['cellphone'] ?? $user->attributes['phone'];
            }
        });

        static::created(function (self $user) {
            if (! $user->roles()->exists()) {
                $defaultRole = \Spatie\Permission\Models\Role::query()
                    ->where('name', 'Jovem')
                    ->where('guard_name', 'web')
                    ->first();
                if ($defaultRole) {
                    $user->assignRole($defaultRole);
                }
            }
        });
    }

    /**
     * Acessor para first_name (Legacy Support)
     */
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (?string $value, array $attributes) {
                if (! empty($value)) {
                    return $value;
                }

                return explode(' ', $attributes['name'] ?? '', 2)[0] ?? '';
            },
            set: function (?string $value, array $attributes) {
                if ($value === null || $value === '') {
                    return [];
                }

                return ['name' => trim($value.' '.($attributes['sobrenome'] ?? ''))];
            },
        );
    }

    /**
     * Acessor para last_name (Legacy Support)
     */
    protected function lastName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (?string $value, array $attributes) {
                if (! empty($value)) {
                    return $value;
                }

                return $attributes['sobrenome'] ?? explode(' ', $attributes['name'] ?? '', 2)[1] ?? '';
            },
            set: fn (?string $value) => ['sobrenome' => $value],
        );
    }

    protected function dateOfBirth(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (?string $value, array $attributes) => $value ?? ($attributes['data_nascimento'] ?? null),
            set: fn (?string $value) => ['data_nascimento' => $value],
        );
    }

    protected function photo(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (?string $value, array $attributes) => $value ?? ($attributes['avatar'] ?? null),
            set: fn (?string $value) => ['avatar' => $value],
        );
    }

    protected function phone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (?string $value, array $attributes) => $value ?? ($attributes['whatsapp'] ?? null),
            set: fn (?string $value) => ['whatsapp' => $value],
        );
    }

    protected function cellphone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (?string $value, array $attributes) => $value ?? ($attributes['whatsapp'] ?? null),
            set: fn (?string $value) => ['whatsapp' => $value],
        );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'sobrenome',
        'email',
        'password',
        'email_verified_at',
        'whatsapp',
        'data_nascimento',
        'cpf',
        'avatar',
        'is_active',
        'igreja_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_nascimento' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Verifica se o usuário tem 2FA (TOTP) ativo e confirmado.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return false;
    }

    /**
     * Relacionamento com Role
     */
    public function role()
    {
        return $this->roles()->wherePivot('model_type', static::class)->limit(1);
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }

    /**
     * Relacionamento com Ministérios
     */
    public function ministries()
    {
        return $this->belongsToMany(
            \Modules\Ministries\App\Models\Ministry::class,
            'ministry_members'
        )->withPivot('role', 'status', 'joined_at', 'approved_at', 'approved_by', 'notes')
            ->withTimestamps();
    }

    public function bibleFavorites()
    {
        return $this->belongsToMany(
            \Modules\Bible\App\Models\Verse::class,
            'bible_favorites',
            'user_id',
            'verse_id'
        )->withPivot('color')->withTimestamps();
    }

    /**
     * Relacionamento com Badges (módulo Gamification)
     */
    public function badges()
    {
        return $this->belongsToMany(\Modules\Gamification\App\Models\Badge::class, 'user_badges')
            ->withPivot('earned_at', 'notes')
            ->withTimestamps();
    }

    /**
     * Vínculos familiares (parentesco): quem este usuário declarou como pai, mãe, cônjuge, etc.
     */
    public function relationships()
    {
        return $this->hasMany(UserRelationship::class, 'user_id');
    }

    /**
     * Quantidade de vínculos familiares (accepted + pending) para exibição na listagem.
     */
    public function getFamilyCount(): int
    {
        return $this->relationships()->count();
    }

    /**
     * Relacionamento com Ministérios ativos
     */
    public function activeMinistries()
    {
        return $this->ministries()->wherePivot('status', 'active');
    }

    /**
     * Verifica se o usuário pode projetar
     */
    public function canProject()
    {
        return $this->isAdmin() || $this->islideranca();
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin()
    {
        return $this->spatieHasRole(['Super Admin', 'Presidente']);
    }

    /**
     * Verifica se o usuário é membro
     */
    public function isMember()
    {
        return $this->spatieHasRole(['Jovem']);
    }

    /**
     * Verifica se o usuário possui um papel específico (slug)
     */
    public function hasRole($roleSlug)
    {
        $mapped = $this->normalizeLegacyRole($roleSlug);
        if (is_array($mapped)) {
            return $this->spatieHasRole($mapped);
        }

        return $this->spatieHasRole($mapped);
    }

    /**
     * Verifica se o usuário é lideranca
     */
    public function islideranca()
    {
        return $this->hasRole('lideranca');
    }

    private function normalizeLegacyRole($role): array|string
    {
        $normalized = mb_strtolower((string) $role);

        return match ($normalized) {
            'admin', 'super_admin', 'super admin' => ['Super Admin', 'Presidente'],
            'lideranca', 'liderança' => ['Super Admin', 'Presidente', 'Vice-Presidente', 'Secretário', 'Tesoureiro', 'Líder Local'],
            'membro', 'member', 'jovem' => ['Jovem'],
            default => $role,
        };
    }

    /**
     * Verifica se o usuário tem acesso ao painel administrativo
     */
    public function hasAdminAccess()
    {
        return $this->isAdmin() || $this->islideranca();
    }

    /**
     * Campos utilizados para cálculo de completitude do perfil
     */
    public const PROFILE_FIELDS = [
        'first_name', 'last_name', 'cpf', 'date_of_birth', 'gender',
        'marital_status', 'email', 'phone', 'cellphone', 'address',
        'address_number', 'address_complement', 'neighborhood', 'city',
        'state', 'zip_code', 'membership_date', 'time_congregating_months',
        'baptism_date', 'baptism_place', 'is_baptized', 'profession',
        'education_level', 'workplace', 'emergency_contact_name',
        'emergency_contact_phone', 'emergency_contact_relationship',
    ];

    /**
     * Calcula a porcentagem de completitude do perfil
     */
    public function getProfileCompletionPercentage()
    {
        $filled = 0;
        foreach (self::PROFILE_FIELDS as $field) {
            $value = $this->$field;
            if ($value !== null && $value !== '') {
                $filled++;
            }
        }

        return round(($filled / count(self::PROFILE_FIELDS)) * 100);
    }

    /**
     * Calcula pontos de gamificação
     */
    public function getGamificationPoints()
    {
        $points = 0;

        // Pontos por tempo de congregação (1 ponto por mês)
        if ($this->time_congregating_months) {
            $points += $this->time_congregating_months;
        }

        // Pontos por batismo
        if ($this->is_baptized) {
            $points += 50;
        }

        // Pontos por completar perfil (10 pontos por campo preenchido)
        foreach (self::PROFILE_FIELDS as $field) {
            if (! empty($this->$field)) {
                $points += 10;
            }
        }

        // Pontos de serviço em ministérios (relatórios enviados no prazo)
        if (class_exists(\Modules\Ministries\App\Models\MinistryServicePoint::class)) {
            $points += \Modules\Ministries\App\Models\MinistryServicePoint::getPointsForUser($this->id);
        }

        return $points;
    }

    /**
     * Retorna o nível do membro baseado nos pontos
     */
    public function getGamificationLevel()
    {
        $points = $this->getGamificationPoints();

        // Tenta buscar do banco de dados primeiro
        $level = \Modules\Gamification\App\Models\GamificationLevel::getLevelByPoints($points);

        if ($level) {
            return [
                'name' => $level->name,
                'color' => $level->color,
                'icon' => $level->icon,
                'points_min' => $level->points_min,
                'points_max' => $level->points_max,
            ];
        }

        // Fallback para valores padrão se não houver níveis configurados (ícones: nomes curtos para <x-icon>)
        if ($points >= 500) {
            return ['name' => 'Líder', 'color' => 'purple', 'icon' => 'trophy', 'points_min' => 500, 'points_max' => null];
        }
        if ($points >= 300) {
            return ['name' => 'Experiente', 'color' => 'blue', 'icon' => 'star', 'points_min' => 300, 'points_max' => 499];
        }
        if ($points >= 200) {
            return ['name' => 'Ativo', 'color' => 'green', 'icon' => 'medal', 'points_min' => 200, 'points_max' => 299];
        }
        if ($points >= 100) {
            return ['name' => 'Participante', 'color' => 'yellow', 'icon' => 'star', 'points_min' => 100, 'points_max' => 199];
        }

        return ['name' => 'Novato', 'color' => 'gray', 'icon' => 'user', 'points_min' => 0, 'points_max' => 99];
    }

    /**
     * Retorna badges do membro (do banco de dados)
     */
    public function getBadges()
    {
        // Busca badges do banco de dados
        $userBadges = $this->badges()->active()->ordered()->get();

        if ($userBadges->isEmpty()) {
            // Fallback para badges padrão se não houver badges configurados
            return $this->getDefaultBadges();
        }

        return $userBadges->map(function ($badge) {
            return [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'color' => $badge->color,
                'earned_at' => $badge->pivot->earned_at,
            ];
        })->toArray();
    }

    /**
     * Retorna badges padrão (fallback)
     */
    private function getDefaultBadges()
    {
        $badges = [];

        // 1. Batizado (Font Awesome: water)
        if ($this->is_baptized) {
            $badges[] = ['name' => 'Batizado', 'icon' => 'water', 'color' => 'blue', 'description' => 'Membro batizado na congregação'];
        }

        // 2. Tempo de Casa
        if ($this->time_congregating_months >= 12) {
            $years = floor($this->time_congregating_months / 12);
            $icon = 'cake-candles';
            $color = 'yellow';
            if ($years >= 5) {
                $icon = 'trophy';
                $color = 'purple';
            } elseif ($years >= 2) {
                $icon = 'champagne-glasses';
                $color = 'green';
            }

            $badges[] = [
                'name' => "{$years} ".($years > 1 ? 'Anos' : 'Ano'),
                'icon' => $icon,
                'color' => $color,
                'description' => "Membro há {$years} ".($years > 1 ? 'anos' : 'ano').' na congregação',
            ];
        }

        // 3. Perfil Completo
        if ($this->getProfileCompletionPercentage() >= 100) {
            $badges[] = [
                'name' => 'Perfil Completo',
                'icon' => 'circle-check',
                'color' => 'green',
                'description' => 'Membro com perfil 100% preenchido',
            ];
        }

        // 4. Leitor Dedicado (5+ favoritos na bíblia)
        if ($this->bibleFavorites()->count() >= 5) {
            $badges[] = [
                'name' => 'Leitor Dedicado',
                'icon' => 'book-bible',
                'color' => 'indigo',
                'description' => 'Membro dedicado à leitura da palavra',
            ];
        }

        // 5. Servo Engajado (Ativo em ministérios)
        if ($this->activeMinistries()->count() > 0) {
            $badges[] = [
                'name' => 'Servo Engajado',
                'icon' => 'users',
                'color' => 'orange',
                'description' => 'Membro ativo em ministérios da igreja',
            ];
        }

        // 5b. Família Unida (núcleo familiar cadastrado e confirmado)
        $familyAccepted = $this->relationships()->accepted()->whereIn('relationship_type', ['pai', 'mae', 'conjuge', 'filho'])->count();
        if ($familyAccepted >= 2) {
            $badges[] = [
                'name' => 'Família Unida',
                'icon' => 'people-group',
                'color' => 'emerald',
                'description' => 'Núcleo familiar cadastrado e confirmado',
            ];
        }

        // 6. Membro Participativo (3+ eventos)
        if ($this->registrations()->where('status', 'confirmed')->count() >= 3) {
            $badges[] = [
                'name' => 'Participativo',
                'icon' => 'calendar-check',
                'color' => 'cyan',
                'description' => 'Presença confirmada em diversos eventos',
            ];
        }

        // 7. Dizimista Fiel (Contribuições financeiras confirmadas)
        if ($this->financialEntries()->income()->where(function ($q) {
            $q->whereNull('payment_id')
                ->orWhereHas('payment', fn ($p) => $p->where('status', 'completed'));
        })->count() > 0) {
            $badges[] = [
                'name' => 'Dizimista Fiel',
                'icon' => 'hand-holding-dollar',
                'color' => 'emerald',
                'description' => 'Membro fiel em suas contribuições',
            ];
        }

        // 8. Cadastro Ativo
        if ($this->is_active) {
            $badges[] = ['name' => 'Cadastro Ativo', 'icon' => 'user-check', 'color' => 'green', 'description' => 'Membro com cadastro regularizado'];
        }

        // 9. Rosto Familiar (Foto de perfil)
        if ($this->photo) {
            $badges[] = [
                'name' => 'Rosto Familiar',
                'icon' => 'circle-user',
                'color' => 'blue',
                'description' => 'Membro identificável com foto de perfil definida',
            ];
        }

        // 10. Visionário (Admin ou Conselho)
        if ($this->isAdmin() || $this->councilMember()->exists()) {
            $badges[] = [
                'name' => 'Visionário',
                'icon' => 'eye',
                'color' => 'rose',
                'description' => 'Membro com visão estratégica e liderança',
            ];
        }

        // 11. Generoso (10+ contribuições confirmadas)
        $confirmedContributions = $this->financialEntries()->income()->where(function ($q) {
            $q->whereNull('payment_id')
                ->orWhereHas('payment', fn ($p) => $p->where('status', 'completed'));
        })->count();

        if ($confirmedContributions >= 10) {
            $badges[] = [
                'name' => 'Generoso',
                'icon' => 'gift',
                'color' => 'amber',
                'description' => 'Membro com histórico notável de contribuições',
            ];
        }

        return $badges;
    }

    /**
     * Relacionamento com permissões da Tesouraria
     */
    public function treasuryPermission()
    {
        return $this->hasOne(\Modules\Treasury\App\Models\TreasuryPermission::class);
    }

    /**
     * Relacionamento com Conselho da Igreja
     */
    public function councilMember()
    {
        return $this->hasOne(\Modules\ChurchCouncil\App\Models\CouncilMember::class);
    }

    /**
     * Verifica se o usuário é membro ativo do conselho (pode acessar rotas admin/conselho/*).
     */
    public function isActiveCouncilMember(): bool
    {
        $member = $this->councilMember;

        return $member && $member->isActive();
    }

    /**
     * Relacionamento com registros de eventos
     */
    public function registrations()
    {
        return $this->hasMany(\Modules\Events\App\Models\EventRegistration::class);
    }

    /**
     * Relacionamento com entradas financeiras
     */
    public function financialEntries()
    {
        return $this->hasMany(\Modules\Treasury\App\Models\FinancialEntry::class);
    }

    /**
     * Relacionamento com múltiplas fotos de perfil
     */
    public function profilePhotos()
    {
        return $this->hasMany(\App\Models\UserPhoto::class);
    }

    /**
     * Relacionamento com voluntariado de Ação Social
     */
    public function socialVolunteer()
    {
        return $this->hasOne(\Modules\SocialAction\App\Models\SocialVolunteer::class, 'user_id');
    }

    /**
     * Relationship with Worship Academy Progress (v2 academy)
     */
    public function academyProgress()
    {
        return $this->hasMany(\Modules\Worship\App\Models\AcademyProgress::class, 'user_id');
    }

    /**
     * @deprecated Use academyProgress() for v2 academy. Legacy table worship_musician_progress.
     */
    public function worshipProgress()
    {
        return $this->hasMany(\Modules\Worship\App\Models\WorshipMusicianProgress::class, 'user_id');
    }


    /**
     * Retorna a foto ativa ou nulo
     */
    public function getActivePhoto()
    {
        return $this->profilePhotos()->where('is_active', true)->first();
    }

    /**
     * Get the photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return asset('storage/' . $this->photo);
    }

    /**
     * Retorna a URL do avatar do usuário
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo_url) {
            return $this->photo_url;
        }

        $activePhoto = $this->getActivePhoto();
        if ($activePhoto) {
            return asset('storage/'.$activePhoto->path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Alias para exibição da foto de perfil (compatível com views que usam profile_photo_url).
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->avatar_url;
    }
}
