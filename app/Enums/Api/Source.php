<?php

namespace App\Enums\Api;

enum Source: string
{
    case Amfr = 'amfr';
    case Api = 'api';
    case Flashscore = 'flashscore';
    case Lnfs = 'lnfs';
}
