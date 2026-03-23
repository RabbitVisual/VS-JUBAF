<?php

namespace Modules\Ministries\App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de compatibilidade quando o módulo Ministries completo não está instalado.
 * Evita Class not found em User, Treasury, Events, etc.
 */
class Ministry extends Model
{
    protected $table = 'ministries';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
