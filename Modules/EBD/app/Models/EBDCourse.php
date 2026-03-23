<?php

namespace Modules\EBD\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EBDCourse extends Model
{
    protected $table = 'ebd_courses';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
        'homologation_status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    const HOMOLOGATION_DRAFT = 'draft';

    const HOMOLOGATION_PENDING = 'pending_approval';

    const HOMOLOGATION_APPROVED = 'approved';

    public function lessons(): HasMany
    {
        return $this->hasMany(EBDLesson::class, 'course_id')->orderBy('order');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(EBDClass::class, 'course_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('homologation_status', self::HOMOLOGATION_APPROVED);
    }

    public function scopeDraft($query)
    {
        return $query->where('homologation_status', self::HOMOLOGATION_DRAFT);
    }

    public function isApproved(): bool
    {
        return $this->homologation_status === self::HOMOLOGATION_APPROVED;
    }

    protected static function booted(): void
    {
        static::creating(function (EBDCourse $course) {
            if (empty($course->slug)) {
                $course->slug = \Illuminate\Support\Str::slug($course->name);
            }
        });
    }
}
