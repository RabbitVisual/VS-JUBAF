<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Worship\App\Enums\InstrumentCategory;

class WorshipInstrument extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'category_id'];

    public function category()
    {
        return $this->belongsTo(WorshipInstrumentCategory::class, 'category_id');
    }
    public function rosters()
    {
        return $this->hasMany(WorshipRoster::class, 'instrument_id');
    }
}
