<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SermonComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sermon_id',
        'user_id',
        'comment',
        'type',
        'parent_id',
        'reference_section',
        'reference_text',
        'status',
        'likes',
    ];

    protected $casts = [
        'likes' => 'integer',
    ];

    // Type constants
    const TYPE_COMMENT = 'comment';

    const TYPE_SUGGESTION = 'suggestion';

    const TYPE_QUESTION = 'question';

    const TYPE_FEEDBACK = 'feedback';

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_RESOLVED = 'resolved';

    /**
     * Get the sermon
     */
    public function sermon(): BelongsTo
    {
        return $this->belongsTo(Sermon::class, 'sermon_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get parent comment
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SermonComment::class, 'parent_id');
    }

    /**
     * Get replies
     */
    public function replies(): HasMany
    {
        return $this->hasMany(SermonComment::class, 'parent_id')
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_COMMENT => 'Comentário',
            self::TYPE_SUGGESTION => 'Sugestão',
            self::TYPE_QUESTION => 'Pergunta',
            self::TYPE_FEEDBACK => 'Feedback',
            default => 'Comentário'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_RESOLVED => 'Resolvido',
            default => 'Pendente'
        };
    }
}
