<?php

namespace Modules\Intercessor\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrayerRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'ministry_id',
        'title',
        'description',
        'privacy_level', // public, members_only, intercessors_only, pastoral_only
        'urgency_level', // normal, high, critical
        'is_anonymous',
        'show_identity',
        'expiration_date',
        'status', // draft, pending, active, answered, archived
        'testimony',
        'answered_at',
        'is_testimony_public',
        'testimony_status',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'show_identity' => 'boolean',
        'expiration_date' => 'datetime',
        'answered_at' => 'datetime',
        'is_testimony_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(PrayerCategory::class, 'category_id');
    }

    public function ministry()
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function interactions()
    {
        return $this->hasMany(PrayerInteraction::class, 'request_id');
    }

    public function commitments()
    {
        return $this->hasMany(PrayerCommitment::class, 'request_id');
    }

    public function accessLogs()
    {
        return $this->hasMany(PrayerAccessLog::class, 'request_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Privacy Scopes
    public function scopePublic($query)
    {
        return $query->where('privacy_level', 'public');
    }

    public function scopeMembers($query)
    {
        return $query->where('privacy_level', 'members_only');
    }

    public function scopeIntercessors($query)
    {
        return $query->where('privacy_level', 'intercessors_only');
    }

    public function scopePastoral($query)
    {
        return $query->where('privacy_level', 'pastoral_only');
    }

    public function getUrgencyLabelAttribute()
    {
        return match ($this->urgency_level) {
            'critical' => 'Prioridade Extrema',
            'high' => 'Prioridade Alta',
            'normal' => 'Normal',
            default => 'Normal',
        };
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsArchivedAttribute()
    {
        return $this->status === 'archived';
    }

    /**
     * Helper to determine author name display based on anonymity settings.
     */
    public function getAuthorNameAttribute()
    {
        if ($this->is_anonymous || !$this->show_identity) {
            return 'Anônimo';
        }
        return $this->user->name ?? 'Usuário Removido';
    }
}
