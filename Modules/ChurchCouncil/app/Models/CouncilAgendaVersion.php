<?php

namespace Modules\ChurchCouncil\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouncilAgendaVersion extends Model
{
    protected $fillable = [
        'council_agenda_id',
        'version',
        'payload',
        'created_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(CouncilAgenda::class, 'council_agenda_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

