<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PautaVersion extends Model
{
    protected $table = 'pauta_versoes';

    protected $fillable = [
        'pauta_id',
        'version',
        'payload',
        'created_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Pauta::class, 'pauta_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
