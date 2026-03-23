<?php

namespace Modules\SocialAction\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPrayerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'request',
        'is_anonymous',
        'status',
        'prayed_at',
    ];

    protected $casts = [
        'name'         => 'encrypted',
        'request'      => 'encrypted',
        'is_anonymous' => 'boolean',
        'prayed_at'    => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePrayed($query)
    {
        return $query->where('status', 'prayed');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous ? 'Anônimo' : ($this->name ?? 'Sem nome');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Aguardando',
            'prayed'   => 'Orado',
            'archived' => 'Arquivado',
            default    => 'N/D',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'yellow',
            'prayed'   => 'green',
            'archived' => 'gray',
            default    => 'gray',
        };
    }
}
