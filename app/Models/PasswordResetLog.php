<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'identifier',
        'ip_address',
        'user_agent',
        'status',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
