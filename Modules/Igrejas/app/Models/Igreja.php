<?php

namespace Modules\Igrejas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Igrejas\Database\Factories\IgrejaFactory;

class Igreja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'pastor_titular',
        'lider_jovens',
        'cidade',
        'estado',
        'logo_path',
    ];

    // protected static function newFactory(): IgrejaFactory
    // {
    //     // return IgrejaFactory::new();
    // }
}
