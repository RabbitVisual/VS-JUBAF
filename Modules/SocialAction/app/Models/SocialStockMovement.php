<?php

namespace Modules\SocialAction\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class SocialStockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['pantry_item_id', 'type', 'quantity', 'reason', 'user_id'];

    public function item()
    {
        return $this->belongsTo(SocialPantryItem::class, 'pantry_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
