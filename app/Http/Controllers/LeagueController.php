<?php

namespace App\Http\Controllers;

use App\Enums\Difficulty;
use App\Models\League;
use App\Services\LeagueService;

class LeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fixtures(League $league)
    {
        $teams = app(LeagueService::class)->getTeamsForFixtures($league);

        $fantasyTourRange = app(LeagueService::class)->getFantasyTourRange($teams);

        if (is_null($fantasyTourRange)) {
            return view('errors.in-progress', [
                'h1' => __('messages.Coming soon'),
                'h2' => __('messages.Waiting for new season...')
            ]);
        }

        $difficultyEnum = Difficulty::class;

        return view('leagues.fixtures', compact('teams', 'fantasyTourRange', 'league', 'difficultyEnum'));
    }
}
