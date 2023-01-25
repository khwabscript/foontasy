<?php

namespace App\Models\Traits\Team;

use App\DTO\Fixture\FantasyTourDifficultiesDTO;
use App\DTO\Fixture\FantasyTourExpectedStatsDTO;
use App\Enums\Difficulty;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

trait HasFantasyTour
{
    /**
     * Get opponents in fantasy tour
     *
     * @param  int $fantasyTour
     * @return SupportCollection
     */
    public function getOpponents(int $fantasyTour): SupportCollection
    {
        return $this->fixtures
            ->where('fantasy_tour', $fantasyTour)
            ->reduce(
                function (SupportCollection $carry, Fixture $fixture): SupportCollection {
                    $carry[$fixture->home_team_id] = $fixture->teams->merge($carry->get($fixture->home_team_id, []));
                    return $carry;
                },
                collect()
            )
            ->map(function (Collection $teams, int $homeTeamId): Collection {
                return $teams
                    ->filter(fn (Team $team): bool => $team->id !== $this->id)
                    ->each(function (Team $team) use ($homeTeamId): void  {
                        $team->isHome = $team->id === $homeTeamId;
                    });
            })
            ->flatten(1);
    }

    /**
     * Get fantasy tour difficulty
     *
     * @param  int $fantasyTour
     * @return FantasyTourExpectedStatsDTO
     */
    public function getFantasyTourExpectedStats(int $fantasyTour): FantasyTourExpectedStatsDTO
    {
        return new FantasyTourExpectedStatsDTO($this, $this->getOpponents($fantasyTour));
    }

    /**
     * Get fantasy tour difficulty
     *
     * @param  int $fantasyTour
     * @param  Collection $teams
     * @param  string $statSample
     * @return Difficulty
     */
    public function getFantasyTourDifficulty(int $fantasyTour, Collection $teams, string $statSample): Difficulty
    {
        $rank = $teams
            ->filter(function (Team $team) use ($fantasyTour, $statSample): bool {
                $opponentExpectedStat = $team->getFantasyTourExpectedStats($fantasyTour)->{$statSample};
                $teamExpectedStat = $this->getFantasyTourExpectedStats($fantasyTour)->{$statSample};

                return $opponentExpectedStat > $teamExpectedStat;
            })
            ->count() + 1;

        $numTeams = $teams->count();

        $separator = floor($numTeams / 3) + (int) ($numTeams % 3 === 2);
        $topSeparator = $numTeams - $separator;

        return $rank <= $separator
            ? Difficulty::Easy
            : ($rank > $topSeparator ? Difficulty::Hard : Difficulty::Medium);
    }

    /**
     * Get fantasy tour difficulties
     *
     * @param  int $fantasyTour
     * @param  Collection $teams
     * @return FantasyTourDifficultiesDTO
     */
    public function getFantasyTourDifficulties(int $fantasyTour, Collection $teams): FantasyTourDifficultiesDTO
    {
        return new FantasyTourDifficultiesDTO(
            overall: $this->getFantasyTourDifficulty($fantasyTour, $teams, 'expectedPoints'),
            defence: $this->getFantasyTourDifficulty($fantasyTour, $teams, 'expectedCleanSheets'),
            attack: $this->getFantasyTourDifficulty($fantasyTour, $teams, 'expectedGoals')
        );
    }
}
