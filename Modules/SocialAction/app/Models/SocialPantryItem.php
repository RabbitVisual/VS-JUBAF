<?php

namespace Modules\SocialAction\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialPantryItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'current_quantity', 'min_quantity'];

    public function movements()
    {
        return $this->hasMany(SocialStockMovement::class, 'pantry_item_id');
    }
}
