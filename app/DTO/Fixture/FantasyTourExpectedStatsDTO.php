<?php

namespace App\DTO\Fixture;

use App\Models\Team;
use Illuminate\Support\Collection as SupportCollection;
use Spatie\LaravelData\Data;

class FantasyTourExpectedStatsDTO extends Data
{
    public float $expectedPoints = 0;
    public float $expectedCleanSheets = 0;
    public float $expectedGoals = 0;

    public function __construct(Team $team, SupportCollection $opponents)
    {
        foreach ($opponents as $opponentTeam) {
            $this->expectedPoints += $this->getExpectedPoints($team, $opponentTeam);
            $this->expectedCleanSheets += $this->getExpectedCleanSheets($team, $opponentTeam);
            $this->expectedGoals += $this->getExpectedGoals($team, $opponentTeam);
        }
    }

    public function getExpectedPoints(Team $team, Team $opponentTeam): float
    {
        return $this->getExpectedProperty($team, $opponentTeam, 'expectedPoints');
    }

    public function getExpectedCleanSheets(Team $team, Team $opponentTeam): float
    {
        return $this->getExpectedProperty($team, $opponentTeam, 'cleanSheets');
    }

    public function getExpectedGoals(Team $team, Team $opponentTeam): float
    {
        return $this->getExpectedProperty($team, $opponentTeam, 'goalsFor');
    }

    public function getExpectedProperty(Team $team, Team $opponentTeam, string $property): float
    {
        $expectedProperty = 0;

        $defaultStatSamples = ['overall', 'lastFour'];

        $teamStatSamples = array_merge(
            $defaultStatSamples,
            $opponentTeam->isHome ? ['away', 'lastFourAway'] : ['home', 'lastFourHome']
        );
        $opponentTeamStatSamples = array_merge(
            $defaultStatSamples,
            $opponentTeam->isHome ? ['home', 'lastFourHome'] : ['away', 'lastFourAway']
        );

        foreach ($teamStatSamples as $statSample) {
            $expectedProperty += $team->stat->{$statSample}->getAverage($property);
        }

        foreach ($opponentTeamStatSamples as $statSample) {
            $oppositeProperty = $this->getOppositeProperty($property);
            $expectedProperty += $opponentTeam->stat->{$statSample}->getAverage($oppositeProperty);
        }

        return $expectedProperty;
    }

    public function getOppositeProperty(string $property): string
    {
        return match ($property) {
            'expectedPoints' => 'expectedPointsAgainst',
            'goalsFor' => 'goalsAgainst',
            'cleanSheets' => 'noGoals',
            default => '',
        };
    }
}
