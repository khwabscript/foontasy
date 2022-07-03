<?php

namespace App\Enums;

enum Position: int
{
    case GKP = 1;
    case DEF = 2;
    case MID = 3;
    case FWD = 4;

    public static function api(string $position): ?self
    {
        return match ($position) {
            'G' => self::GKP,
            'D' => self::DEF,
            'M' => self::MID,
            'F' => self::FWD,
            default => null,
        };
    }
}
