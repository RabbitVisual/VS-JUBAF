<?php

namespace Modules\SocialAction\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialKit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function items()
    {
        return $this->belongsToMany(SocialPantryItem::class, 'social_kit_items', 'kit_id', 'pantry_item_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
