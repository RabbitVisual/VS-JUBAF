<?php

namespace Modules\ChurchCouncil\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferLetter extends Model
{
    protected $fillable = [
        'user_id',
        'direction',
        'from_church',
        'to_church',
        'status',
        'issued_at',
        'received_at',
        'file_path',
        'file_type',
        'file_size',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'received_at' => 'date',
        'file_size' => 'integer',
        'metadata' => 'array',
    ];

    public const DIRECTION_OUTGOING = 'outgoing';
    public const DIRECTION_INCOMING = 'incoming';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_COUNCIL = 'pending_council';
    public const STATUS_PENDING_ASSEMBLY = 'pending_assembly';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACKNOWLEDGED = 'acknowledged';

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

