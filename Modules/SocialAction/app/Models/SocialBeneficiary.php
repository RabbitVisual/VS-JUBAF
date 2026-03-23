<?php

namespace Modules\SocialAction\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialBeneficiary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'contact_info',   // legado (encriptado)
        'phone',
        'address',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'family_size',
        'monthly_income',
        'needs',
        'pastoral_notes',
        'status',
    ];

    protected $casts = [
        'full_name'      => 'encrypted',
        'contact_info'   => 'encrypted',
        'phone'          => 'encrypted',
        'address'        => 'encrypted',
        'neighborhood'   => 'encrypted',
        'pastoral_notes' => 'encrypted',
        'latitude'       => 'decimal:7',
        'longitude'      => 'decimal:7',
        'monthly_income' => 'decimal:2',
        'family_size'    => 'integer',
        'needs'          => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function assistances()
    {
        return $this->hasMany(SocialAssistance::class, 'social_beneficiary_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowIncome($query, float $threshold = 1500.00)
    {
        return $query->where('monthly_income', '<=', $threshold)
                     ->orWhereNull('monthly_income');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->neighborhood, $this->city, $this->state])
            ->filter()
            ->implode(', ');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'green',
            'inactive' => 'gray',
            'flagged'  => 'red',
            default    => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
            'flagged'  => 'Atenção',
            default    => 'N/D',
        };
    }

    public static function needsLabels(): array
    {
        return [
            'food'     => 'Alimentação',
            'clothes'  => 'Vestuário',
            'medicine' => 'Medicamentos',
            'hygiene'  => 'Higiene',
            'other'    => 'Outros',
        ];
    }
}
