<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Worship\Database\Factories\WorshipEquipmentFactory;

class WorshipEquipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): WorshipEquipmentFactory
    // {
    //     // return WorshipEquipmentFactory::new();
    // }
}
