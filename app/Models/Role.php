<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
    ];

    public function getSlugAttribute(): string
    {
        return match ($this->name) {
            'Super Admin', 'Presidente' => 'admin',
            'Vice-Presidente', 'Secretário', 'Tesoureiro', 'Líder Local' => 'lideranca',
            'Jovem' => 'membro',
            default => \Illuminate\Support\Str::slug((string) $this->name, '_'),
        };
    }
}
