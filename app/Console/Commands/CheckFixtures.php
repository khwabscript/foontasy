<?php

namespace App\Console\Commands;

use App\Api\ApiFootball;
use App\Models\League;
use Database\Seeders\ApiFixtureSeeder;
use Illuminate\Console\Command;

class CheckFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:check {league=all} {--S|season=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get fixtures from api-football.com, compare these with api fixtures in DB and update';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leagueArgument = $this->argument('league');
        $apiLeagues = $leagueArgument === 'all' ? League::api() : League::where('name', $leagueArgument)->get();
        $season = $this->option('season');

        foreach ($apiLeagues as $apiLeague) {
            app(ApiFootball::class)->getLeagueFixtures($apiLeague->id, $season);
            app(ApiFixtureSeeder::class)->seedFixtures($apiLeague);
        }

        return 0;
    }
}
