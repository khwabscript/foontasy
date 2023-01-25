<?php

namespace App\DTO\Fixture;

use App\Enums\Difficulty;
use Spatie\LaravelData\Data;

class FantasyTourDifficultiesDTO extends Data
{
    public function __construct(
        public Difficulty $overall,
        public Difficulty $defence,
        public Difficulty $attack
    ) {
    }
}
