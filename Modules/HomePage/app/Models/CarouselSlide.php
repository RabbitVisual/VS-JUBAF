<?php

namespace Modules\HomePage\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarouselSlide extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'logo_path',
        'logo_position',
        'logo_scale',
        'alt_text',
        'link',
        'link_text',
        'text_position',
        'text_alignment',
        'overlay_opacity',
        'overlay_color',
        'text_color',
        'button_style',
        'order',
        'is_active',
        'transition_type',
        'transition_duration',
        'show_indicators',
        'show_controls',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'overlay_opacity' => 'integer',
        'transition_duration' => 'integer',
        'show_indicators' => 'boolean',
        'show_controls' => 'boolean',
        'logo_scale' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($slide) {
            if (empty($slide->alt_text) && $slide->title) {
                $slide->alt_text = $slide->title;
            }
        });

        static::updating(function ($slide) {
            if (empty($slide->alt_text) && $slide->title) {
                $slide->alt_text = $slide->title;
            }
        });

        static::deleting(function ($slide) {
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
            if ($slide->logo_path) {
                Storage::disk('public')->delete($slide->logo_path);
            }
        });
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::url($this->image);
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if (! $this->logo_path) {
            return null;
        }

        if (str_starts_with($this->logo_path, 'http')) {
            return $this->logo_path;
        }

        return Storage::url($this->logo_path);
    }

    /**
     * Get overlay style
     */
    public function getOverlayStyleAttribute()
    {
        $opacity = $this->overlay_opacity / 100;
        $color = $this->overlay_color;

        // Convert hex to rgb
        $rgb = $this->hexToRgb($color);

        return "background-color: rgba({$rgb['r']}, {$rgb['g']}, {$rgb['b']}, {$opacity});";
    }

    /**
     * Get text style
     */
    public function getTextStyleAttribute()
    {
        return "color: {$this->text_color};";
    }

    /**
     * Check if slide is currently active based on dates
     */
    public function getIsCurrentlyActiveAttribute()
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active slides
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently active slides (considering dates)
     */
    public function scopeCurrentlyActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    /**
     * Scope for ordered slides
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Convert hex color to RGB
     */
    private function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }
}
