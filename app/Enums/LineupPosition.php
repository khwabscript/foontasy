<?php

namespace App\Enums;

enum LineupPosition: int
{
    case GK = 1;
    case RB = 2;
    case LB = 3;
    case CB = 4;
    case CDM = 5;
    case LWB = 6;
    case LW = 7;
    case CM = 8;
    case CF = 9;
    case CAM = 10;
    case RW = 11;
    case LM = 12;
    case RWB = 13;
    case RM = 14;
    case LAM = 15;
    case RAM = 16;
    
    public static function api(?string $position): ?int
    {
        return match ($position) {
            'GK' => 1, 
            'RB' => 2, 
            'LB' => 3, 
            'CB' => 4, 
            'CDM' => 5, 
            'LWB' => 6, 
            'LW' => 7, 
            'CM' => 8, 
            'CF' => 9,
            'CAM' => 10, 
            'RW' => 11, 
            'LM' => 12, 
            'RWB' => 13, 
            'RM' => 14, 
            'LAM' => 15, 
            'RAM' => 16,
            default => null,
        };
    }
}
