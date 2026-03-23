<?php

namespace Modules\Worship\App\Enums;

enum RosterStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case DECLINED = 'declined';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::CONFIRMED => 'Confirmado',
            self::DECLINED => 'Recusado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'green',
            self::DECLINED => 'red',
        };
    }
}
