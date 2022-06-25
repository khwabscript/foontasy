<?php

namespace Database\Seeders;

use App\Api\ApiFootball;
use App\Models\Fixture\ApiFixture;
use App\Models\League\ApiLeague;
use App\Models\Team\ApiTeam;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ApiFixtureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $apiLeagues = ApiLeague::all();

        foreach ($apiLeagues as $apiLeague) {
            $this->seedFixtures($apiLeague->id);
        }
    }

    public function seedFixtures(ApiLeague $apiLeague): void
    {
        $fixtures = json_decode(
            Storage::disk('local')->get(ApiFootball::$fixturesPath . 'league/' . $apiLeague->id . '.json')
        );

        foreach ($fixtures as $fixture) {
            $teams = $fixture->teams;
            $this->seedTeams($teams, $apiLeague);

            // prepare data
            $isPostponed = ($fixture->fixture->status->long === 'Match Postponed');
            $datetime = Carbon::parse($fixture->fixture->date)->addYears((int) $isPostponed);
            $tour = (int) preg_replace('/\D/', '', $fixture->league->round);

            // set fields, that should be updated
            $apiFixture = ApiFixture::find($fixture->fixture->id);
            $update = ['datetime', 'home_team_goals', 'away_team_goals'];
            if ($apiFixture && $apiFixture->tour === $apiFixture->fantasy_tour) {
                $update[] = 'fantasy_tour';
            }

            ApiFixture::upsert([
                'id' => $fixture->fixture->id,
                'api_league_id' => $fixture->league->id,
                'datetime' => $datetime,
                'tour' => $tour,
                'fantasy_tour' => $isPostponed ? ApiFixture::POSTPONED_TOUR : $tour,
                'home_team_id' => $teams->home->id,
                'away_team_id' => $teams->away->id,
                'home_team_goals' => $fixture->goals->home,
                'away_team_goals' => $fixture->goals->away,
            ], ['id'], $update);

            $apiFixture->attach([
                $teams->home->id,
                $teams->away->id,
            ]);
        }
    }

    public function seedTeams(object $teams, ApiLeague $apiLeague): void
    {
        ApiTeam::upsert([
            [
                'id' => $teams->home->id,
                'name' => $teams->home->name,
            ],
            [
                'id' => $teams->away->id,
                'name' => $teams->away->name,
            ],
        ], ['id']);

        $apiLeague->attach([
            $teams->home->id,
            $teams->away->id,
        ]);
    }
}
