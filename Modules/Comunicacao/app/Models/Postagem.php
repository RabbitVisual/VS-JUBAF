<?php

namespace Modules\Comunicacao\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Comunicacao\Database\Factories\PostagemFactory;

class Postagem extends Model
{
    use HasFactory;

    protected $table = 'postagens';

    protected $fillable = [
        'titulo',
        'conteudo',
        'tipo',
        'anexo_path',
        'user_id',
    ];

    // protected static function newFactory(): PostagemFactory
    // {
    //     // return PostagemFactory::new();
    // }
}
