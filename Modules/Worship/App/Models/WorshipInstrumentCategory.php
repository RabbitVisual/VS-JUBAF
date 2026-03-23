<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;

class WorshipInstrumentCategory extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'icon', 'description'];

    public function instruments()
    {
        return $this->hasMany(WorshipInstrument::class, 'category_id');
    }
}
