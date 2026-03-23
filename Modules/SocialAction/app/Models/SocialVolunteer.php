<?php

namespace Modules\SocialAction\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialVolunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'phone',
        'skills',
        'availability',
        'total_hours',
        'bio',
        'is_active',
    ];

    protected $casts = [
        'skills'       => 'array',
        'availability' => 'array',
        'total_hours'  => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    public static function skillsLabels(): array
    {
        return [
            'food_prep'      => 'Preparo de Alimentos',
            'distribution'   => 'Distribuição de Cestas',
            'transport'      => 'Transporte',
            'administration' => 'Administração',
            'pastoral'       => 'Cuidado Pastoral',
            'health'         => 'Saúde / Primeiros Socorros',
            'teaching'       => 'Ensino / Capacitação',
            'other'          => 'Outros',
        ];
    }

    public static function availabilityLabels(): array
    {
        return [
            'monday'    => 'Segunda-feira',
            'tuesday'   => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday'  => 'Quinta-feira',
            'friday'    => 'Sexta-feira',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'leader' => 'Líder',
            'helper' => 'Auxiliar',
            default  => ucfirst($this->role),
        };
    }
}
