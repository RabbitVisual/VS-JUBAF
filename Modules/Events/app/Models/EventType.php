<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EventType extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color', 'order'];


    protected static function booted(): void
    {
        static::creating(function (EventType $type) {
            if (empty($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }
}
