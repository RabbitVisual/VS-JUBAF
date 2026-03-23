<?php

namespace Modules\Worship\App\Enums;

enum SetlistStatus: string
{
    case DRAFT = 'draft';
    case REHEARSAL = 'rehearsal';
    case LIVE = 'live';
    case FINISHED = 'finished';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Rascunho',
            self::REHEARSAL => 'Ensaio',
            self::LIVE => 'Ao Vivo',
            self::FINISHED => 'Finalizado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::REHEARSAL => 'blue',
            self::LIVE => 'red',
            self::FINISHED => 'green',
        };
    }
}
