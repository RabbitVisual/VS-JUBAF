<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Worship\App\Models\WorshipSong;

class Sermon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'introduction',
        'development',
        'conclusion',
        'application',
        'sermon_structure_type',
        'structure_meta',
        'full_content',
        'category_id',
        'series_id',
        'user_id',
        'cover_image',
        'attachments',
        'worship_suggestion_id',
        'status',
        'visibility',
        'is_collaborative',
        'is_featured',
        'views',
        'likes',
        'downloads',
        'published_at',
        'sermon_date',
        'version',
        'parent_id',
    ];

    protected $casts = [
        'is_collaborative' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'sermon_date' => 'datetime',
        'views' => 'integer',
        'likes' => 'integer',
        'downloads' => 'integer',
        'version' => 'integer',
        'attachments' => 'array',
        'structure_meta' => 'array',
    ];

    // Structure type constants (Isaltino Coelho)
    const STRUCTURE_EXPOSITIVO = 'expositivo';

    const STRUCTURE_TEMATICO = 'temático';

    const STRUCTURE_TEXTUAL = 'textual';

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_ARCHIVED = 'archived';

    // Visibility constants
    const VISIBILITY_PUBLIC = 'public';

    const VISIBILITY_MEMBERS = 'members';

    const VISIBILITY_PRIVATE = 'private';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sermon) {
            if (empty($sermon->slug)) {
                $sermon->slug = Str::slug($sermon->title).'-'.Str::random(6);
            }
            if ($sermon->status === self::STATUS_PUBLISHED && ! $sermon->published_at) {
                $sermon->published_at = now();
            }
        });

        static::updating(function ($sermon) {
            if ($sermon->isDirty('status') && $sermon->status === self::STATUS_PUBLISHED && ! $sermon->published_at) {
                $sermon->published_at = now();
            }
        });
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SermonCategory::class, 'category_id');
    }

    /**
     * Get the series
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(BibleSeries::class, 'series_id');
    }

    /**
     * Get the author/creator
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the worship suggestion
     */
    public function worshipSuggestion(): BelongsTo
    {
        return $this->belongsTo(WorshipSong::class, 'worship_suggestion_id');
    }

    /**
     * Get the parent sermon (if this is a fork)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Sermon::class, 'parent_id');
    }

    /**
     * Get child sermons (forks)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Sermon::class, 'parent_id');
    }

    /**
     * Get tags
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(SermonTag::class, 'sermon_tag', 'sermon_id', 'sermon_tag_id');
    }

    /**
     * Get bible references
     */
    public function bibleReferences(): HasMany
    {
        return $this->hasMany(SermonBibleReference::class, 'sermon_id')->orderBy('order');
    }

    /**
     * Get study notes (exegesis) linked to this sermon or reusable by user
     */
    public function studyNotes(): HasMany
    {
        return $this->hasMany(SermonStudyNote::class, 'sermon_id');
    }

    /**
     * Get collaborators
     */
    public function collaborators(): HasMany
    {
        return $this->hasMany(SermonCollaborator::class, 'sermon_id');
    }

    /**
     * Get accepted collaborators
     */
    public function acceptedCollaborators(): HasMany
    {
        return $this->hasMany(SermonCollaborator::class, 'sermon_id')
            ->where('status', 'accepted');
    }

    /**
     * Get comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(SermonComment::class, 'sermon_id')
            ->whereNull('parent_id')
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get all comments (including replies)
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(SermonComment::class, 'sermon_id')
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get favorites
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(SermonFavorite::class, 'sermon_id');
    }

    /**
     * Check if user has favorited this sermon
     */
    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user can edit this sermon
     */
    public function canEdit(User $user): bool
    {
        // Admin / pastor can edit any sermon in the admin panel
        if ($user->isAdmin() || $user->isPastor()) {
            return true;
        }

        // Owner can always edit
        if ($this->user_id === $user->id) {
            return true;
        }

        // Check if user is an accepted collaborator with edit permissions
        return $this->acceptedCollaborators()
            ->where('user_id', $user->id)
            ->where('can_edit', true)
            ->exists();
    }

    /**
     * Check if user can delete this sermon (only owner or admin; co-authors cannot delete).
     */
    public function canDelete(User $user): bool
    {
        if ($user->isAdmin() || $user->isPastor()) {
            return true;
        }
        return $this->user_id === $user->id;
    }

    /**
     * Check if user can view this sermon
     */
    public function canView(User $user): bool
    {
        // Public sermons can be viewed by anyone
        if ($this->visibility === self::VISIBILITY_PUBLIC && $this->status === self::STATUS_PUBLISHED) {
            return true;
        }

        // Members visibility requires authenticated user
        if ($this->visibility === self::VISIBILITY_MEMBERS && $this->status === self::STATUS_PUBLISHED) {
            return true;
        }

        // Private sermons: owner or collaborator
        if ($this->visibility === self::VISIBILITY_PRIVATE) {
            // Owner can always view
            if ($this->user_id === $user->id) {
                return true;
            }

            // Collaborator can view
            return $this->acceptedCollaborators()
                ->where('user_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Increment views
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Rascunho',
            self::STATUS_PUBLISHED => 'Publicado',
            self::STATUS_ARCHIVED => 'Arquivado',
            default => 'Rascunho'
        };
    }

    /**
     * Get visibility display name
     */
    public function getVisibilityDisplayAttribute(): string
    {
        return match ($this->visibility) {
            self::VISIBILITY_PUBLIC => 'Público',
            self::VISIBILITY_MEMBERS => 'Membros',
            self::VISIBILITY_PRIVATE => 'Privado',
            default => 'Membros'
        };
    }

    /**
     * Scope for published sermons
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for featured sermons
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for visible sermons (based on visibility)
     */
    public function scopeVisible($query, ?User $user = null)
    {
        if (! $user) {
            return $query->where('visibility', self::VISIBILITY_PUBLIC)
                ->where('status', self::STATUS_PUBLISHED);
        }

        // Public sermons
        $query->where(function ($q) {
            $q->where('visibility', self::VISIBILITY_PUBLIC)
                ->where('status', self::STATUS_PUBLISHED);
        })
        // Member-only sermons (user is authenticated)
            ->orWhere(function ($q) {
                $q->where('visibility', self::VISIBILITY_MEMBERS)
                    ->where('status', self::STATUS_PUBLISHED);
            })
        // Private sermons (owner or collaborator)
            ->orWhere(function ($q) use ($user) {
                $q->where('visibility', self::VISIBILITY_PRIVATE)
                    ->where(function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id)
                            ->orWhereHas('acceptedCollaborators', function ($collabQ) use ($user) {
                                $collabQ->where('user_id', $user->id);
                            });
                    });
            });

        return $query;
    }

    /**
     * Get the cover image URL
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        // Fallback placeholder based on category
        $category = $this->category?->name ?? 'Sermon';

        return 'https://images.unsplash.com/photo-1504052434569-70ad5836ab65?q=80&w=1200&auto=format&fit=crop&text='.urlencode($category);
    }
}
