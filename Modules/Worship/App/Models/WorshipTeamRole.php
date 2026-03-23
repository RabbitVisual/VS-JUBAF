<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Worship\Database\Factories\WorshipTeamRoleFactory;

class WorshipTeamRole extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): WorshipTeamRoleFactory
    // {
    //     // return WorshipTeamRoleFactory::new();
    // }
}
