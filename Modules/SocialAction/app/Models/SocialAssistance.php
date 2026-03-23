<?php

namespace Modules\SocialAction\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Treasury\App\Models\FinancialEntry;

class SocialAssistance extends Model
{
    use HasFactory;

    /** type: item, kit (estoque) ou financial (auxílio financeiro → tesouraria) */
    protected $fillable = [
        'social_beneficiary_id',
        'social_pantry_item_id',
        'kit_id',
        'type',
        'quantity',
        'registered_at',
        'description',
        'amount',
        'financial_entry_id',
        'notes',
        'status',
        'delivered_at',
        'volunteer_id',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'delivered_at' => 'datetime',
        'notes' => 'encrypted',
        'amount' => 'decimal:2',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(SocialBeneficiary::class, 'social_beneficiary_id');
    }

    public function pantryItem()
    {
        return $this->belongsTo(SocialPantryItem::class, 'social_pantry_item_id');
    }

    public function kit()
    {
        return $this->belongsTo(SocialKit::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }

    /** Entrada de despesa na tesouraria (quando type = financial). */
    public function financialEntry()
    {
        return $this->belongsTo(FinancialEntry::class);
    }

    public function isFinancial(): bool
    {
        return $this->type === 'financial';
    }
}
