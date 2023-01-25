<?php

namespace App\DTO\Team;

use Spatie\LaravelData\Data;

class TeamStatSampleDTO extends Data
{
    public int $expectedPoints;
    public int $expectedPointsAgainst;

    public const NUM_POINTS_FOR_GOAL = 7;
    public const NUM_POINTS_FOR_CLEAN_SHEET = 20;

    public function __construct(
        public int $matches,
        public int $noGoals,
        public int $cleanSheets,
        public int $goalsFor,
        public int $goalsAgainst
    ) {
        $this->expectedPoints = $this->goalsFor * $this::NUM_POINTS_FOR_GOAL +
            $this->cleanSheets * $this::NUM_POINTS_FOR_CLEAN_SHEET;

        $this->expectedPointsAgainst = $this->goalsAgainst * $this::NUM_POINTS_FOR_GOAL +
            $this->noGoals * $this::NUM_POINTS_FOR_CLEAN_SHEET;
    }

    public function getAverage(string $property): float
    {
        return $this->matches > 0 ? round($this->{$property} / $this->matches, 2) : 0;
    }
}
