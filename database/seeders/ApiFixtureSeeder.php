<?php

namespace Database\Seeders;

use App\Api\ApiFootball;
use App\Enums\Api\Source;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
        $apiLeagues = League::api();

        foreach ($apiLeagues as $apiLeague) {
            $this->seedFixtures($apiLeague->external_id);
        }
    }

    public function seedFixtures(League $apiLeague): void
    {
        $fixtures = json_decode(
            Storage::disk('local')->get(ApiFootball::$baseDir . 'league-fixtures/' . $apiLeague->external_id . '.json')
        );

        foreach ($fixtures as $fixture) {
            $teams = $fixture->teams;
            $apiTeams = $this->seedTeams($teams, $apiLeague);

            // prepare data
            $isPostponed = ($fixture->fixture->status->long === 'Match Postponed');
            $datetime = Carbon::parse($fixture->fixture->date)->addYears((int) $isPostponed);
            $tour = (int) preg_replace('/\D/', '', $fixture->league->round);

            // set fields, that should be updated
            $apiFixture = Fixture::findInLeague($fixture->fixture->id, $apiLeague->id);
            $update = ['datetime', 'home_team_goals', 'away_team_goals'];
            if ($apiFixture && $apiFixture->tour === $apiFixture->fantasy_tour) {
                $update[] = 'fantasy_tour';
            }

            Fixture::upsert([
                'external_id' => $fixture->fixture->id,
                'league_id' => $apiLeague->id,
                'datetime' => $datetime,
                'tour' => $tour,
                'fantasy_tour' => $isPostponed ? Fixture::POSTPONED_TOUR : $tour,
                'home_team_id' => $apiTeams->firstWhere('external_id', $teams->home->id)->id,
                'away_team_id' => $apiTeams->firstWhere('external_id', $teams->away->id)->id,
                'home_team_goals' => $fixture->goals->home,
                'away_team_goals' => $fixture->goals->away,
            ], ['external_id', 'league_id'], $update);

            if (is_null($apiFixture)) {
                $apiFixture = Fixture::findInLeague($fixture->fixture->id, $apiLeague->id);
            }

            $apiFixture->teams()->syncWithoutDetaching($apiTeams);
        }
    }

    public function seedTeams(object $teams, League $apiLeague): Collection
    {
        Team::upsert([
            [
                'external_id' => $teams->home->id,
                'source' => Source::Api,
                'name' => $teams->home->name,
            ],
            [
                'external_id' => $teams->away->id,
                'source' => Source::Api,
                'name' => $teams->away->name,
            ],
        ], ['external_id', 'source']);

        $apiTeams = Team::api()->whereExternalIdIn([$teams->home->id, $teams->away->id])->get();

        $apiLeague->teams()->syncWithoutDetaching($apiTeams);

        return $apiTeams;
    }
}
