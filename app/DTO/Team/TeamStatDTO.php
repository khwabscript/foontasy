<?php

namespace App\DTO\Team;

use App\Models\Fixture;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;

class TeamStatDTO extends Data
{
    public TeamStatSampleDTO $overall;
    public TeamStatSampleDTO $home;
    public TeamStatSampleDTO $away;
    public TeamStatSampleDTO $lastFour;
    public TeamStatSampleDTO $lastFourHome;
    public TeamStatSampleDTO $lastFourAway;

    public function __construct(Collection $fixtures, public int $teamId)
    {
        $this->overall = $this->getTeamStatSample($fixtures);
        $this->lastFour = $this->getTeamStatSample($fixtures->take(4));

        $homeFixtures = $fixtures->filter(fn (Fixture $fixture): bool => $fixture->home_team_id === $teamId);
        $awayFixtures = $fixtures->filter(fn (Fixture $fixture): bool => $fixture->away_team_id === $teamId);

        $this->home = $this->getTeamStatSample($homeFixtures);
        $this->away = $this->getTeamStatSample($awayFixtures);
        $this->lastFourHome = $this->getTeamStatSample($homeFixtures->take(4));
        $this->lastFourAway = $this->getTeamStatSample($awayFixtures->take(4));
    }

    public function getTeamStatSample(Collection $fixtures): TeamStatSampleDTO
    {
        $teamGoalsFor = $fixtures->map(
            fn (Fixture $fixture): int => $fixture->home_team_id === $this->teamId
                ? $fixture->home_team_goals
                : $fixture->away_team_goals
        );

        $teamGoalsAgainst = $fixtures->map(
            fn (Fixture $fixture): int => $fixture->home_team_id === $this->teamId
                ? $fixture->away_team_goals
                : $fixture->home_team_goals
        );

        return new TeamStatSampleDTO(
            matches: $fixtures->count(),
            noGoals: $teamGoalsFor->filter(fn (int $goals): bool => $goals === 0)->count(),
            cleanSheets: $teamGoalsAgainst->filter(fn (int $goals): bool => $goals === 0)->count(),
            goalsFor: $teamGoalsFor->sum(),
            goalsAgainst: $teamGoalsAgainst->sum(),
        );
    }
}
