<?php

namespace Modules\Worship\App\Enums;

enum AcademyCourseCategory: string
{
    case VOCAL = 'vocal';
    case INSTRUMENTAL = 'instrumental';
    case TEORIA = 'teoria';
    case ESPIRITUALIDADE = 'espiritualidade';

    public function label(): string
    {
        return match ($this) {
            self::VOCAL => 'Vocal',
            self::INSTRUMENTAL => 'Instrumental',
            self::TEORIA => 'Teoria',
            self::ESPIRITUALIDADE => 'Espiritualidade',
        };
    }
}
