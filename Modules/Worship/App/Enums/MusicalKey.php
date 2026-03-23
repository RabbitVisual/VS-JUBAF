<?php

namespace Modules\Worship\App\Enums;

enum MusicalKey: string
{
    case C = 'C';
    case Cs = 'C#';
    case D = 'D';
    case Ds = 'D#';
    case E = 'E';
    case F = 'F';
    case Fs = 'F#';
    case G = 'G';
    case Gs = 'G#';
    case A = 'A';
    case As = 'A#';
    case B = 'B';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getIndex(): int
    {
        return array_search($this->value, self::values());
    }

    public static function fromIndex(int $index): self
    {
        $values = self::values();
        $normalizedIndex = $index % 12;
        if ($normalizedIndex < 0) $normalizedIndex += 12;

        return self::from($values[$normalizedIndex]);
    }
}
