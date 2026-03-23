<?php

namespace Modules\Worship\App\Enums;

enum InstrumentCategory: string
{
    case HARMONIA = 'harmonia';
    case MELODIA = 'melodia';
    case PERCUSSAO = 'percussao';

    public function label(): string
    {
        return match($this) {
            self::HARMONIA => 'Harmonia',
            self::MELODIA => 'Melodia',
            self::PERCUSSAO => 'Percussão',
        };
    }
}
