<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use Illuminate\Support\Collection;

class LeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fixtures(League $league)
    {
        $teams = $league->teams()
            ->with([
                'fixtures' => fn ($query) => $query->upcoming()->leagueId($league->id)->orderBy('datetime'),
            ])
            ->get();

        $fantasyTourRange = $this->getFantasyTourRange(
            $teams->pluck('fixtures')->flatten()->pluck('fantasy_tour')
        );

        if (is_null($fantasyTourRange)) {
            return view('errors.in-progress', [
                'h1' => __('messages.Coming soon'),
                'h2' => __('messages.Waiting for new season...')
            ]);
        }

        $teams = $teams->sortBy(fn (Team $team) => __('teams.' . $team->name));

        return view('leagues.fixtures', compact('teams', 'fantasyTourRange', 'league'));
    }

    public function getFantasyTourRange(Collection $fantasyTours, int $numTours = 4): ?array
    {
        $firstTour = $fantasyTours->countBy()->filter(fn ($count) => $count >= 4 * 2)->keys()->min();

        if (is_null($firstTour)) {
            return null;
        }

        $lastTour = $fantasyTours->filter(fn ($tour) => $tour > $firstTour && $tour < $firstTour + $numTours)->max();

        return range($firstTour, $lastTour ?? $firstTour);
    }
}
