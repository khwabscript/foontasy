<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LeagueService
{
    public function getTeamsForFixtures(League $league): Collection
    {
        $teams = $league->teams()
            ->with([
                'fixtures' => fn (BelongsToMany $query): BelongsToMany => $query->leagueId($league->id),
                // 'fixtures.teams.fixtures',
            ])
            ->get();

        $teams = $teams->sortBy(fn (Team $team): string => __('teams.' . $team->name));

        // $this->setRelations($teams);

        return $teams;
    }

    public function setRelations(Collection $teams): void
    {
        $fixtures = $teams->pluck('fixtures')->flatten(1)->unique('id');

        foreach ($teams as $team) {
            foreach ($team->fixtures as $fixture) {
                $fixtureTeamIds = [$fixture->home_team_id, $fixture->away_team_id];
                $fixture->teams = $teams->whereIn('id', $fixtureTeamIds);

                foreach ($fixture->teams as $team) {
                    $team->fixtures = new Collection(
                        $fixtures->filter(
                            fn (Fixture $fixture): bool => in_array(
                                $team->id,
                                $fixtureTeamIds
                            )
                        )
                    );
                }
            }
        }
    }

    public function getFantasyTourRange(Collection $teams, int $numTours = 4): ?array
    {
        $fantasyTours = $teams->pluck('upcomingFixtures')->flatten()->pluck('fantasy_tour');

        $firstTour = $fantasyTours->countBy()->filter(fn (int $count): bool => $count >= 4 * 2)->keys()->min();

        if (is_null($firstTour)) {
            return null;
        }

        $lastTour = $fantasyTours
            ->filter(
                fn (int $tour): bool => $tour > $firstTour && $tour < $firstTour + $numTours
            )
            ->max();

        return range($firstTour, $lastTour ?? $firstTour);
    }
}
