<?php

namespace Database\Seeders;

use App\Api\ApiFootball;
use App\Api\DTO\ApiFixture;
use App\Api\DTO\ApiPlayerStatistics;
use App\Enums\Api\Source;
use App\Enums\Event as EventEnum;
use App\Events\InvalidFixture;
use App\Models\Event;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Pivot\EventFixture;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Storage;

class ApiFixtureEventSeeder extends Seeder
{
    public const NUM_PAST_DAYS = 14;

    public Collection $events;

    private Fixture $fixture;

    private League $apiLeague;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(League $apiLeague)
    {
        $this->apiLeague = $apiLeague;
        $this->setEvents();

        $finishedFixtures = $this->getFinishedFixtures();

        foreach ($finishedFixtures as $fixture) {
            $this->fixture = $fixture;
            $this->seedFixtureEvents($fixture->external_id);
        }
    }

    public function seedFixtureEvents(int $fixtureId)
    {
        $fixture = $this->getFixture($fixtureId);

        if (!$fixture) {
            InvalidFixture::dispatch($fixtureId);
            return;
        }

        $apiFixture = new ApiFixture($fixture);

        foreach ($apiFixture->players as $teamId => $players) {
            foreach ($players as $player) {
                $this->seedPlayerEvents($player, $teamId, $apiFixture);
            }
        }
    }

    public function seedPlayerEvents(object $player, int $teamId, ApiFixture $apiFixture): void
    {
        $apiPlayer = $this->getOrCreatePlayer($player);

        $statistics = data_get($player, 'statistics.0');

        $apiPlayerStatistics = new ApiPlayerStatistics($statistics, $apiFixture, $apiPlayer->external_id, $teamId);

        $this->syncPlayerTeam($apiPlayer, $teamId, $apiPlayerStatistics, $statistics);

        $this->upsertPlayerEvents($apiPlayer, $apiPlayerStatistics);
    }

    public function getFinishedFixtures(): SupportCollection
    {
        # sort by date for right player_team table seed
        return $this->apiLeague->fixtures()
            ->where('datetime', '>', now()->subDays($this::NUM_PAST_DAYS))
            ->finished()
            ->orderBy('datetime')
            ->get(['id', 'external_id']);
    }

    public function getFixture(int $fixtureId): object
    {
        $fixture = $this->validateFixture($fixtureId);

        if (!$fixture) {
            app(ApiFootball::class)->getFixture($fixtureId);
            $fixture = $this->validateFixture($fixtureId);
        }

        return $fixture;
    }

    public function getOrCreatePlayer(object $player): Player
    {
        $apiPlayer = Player::query()
            ->when(
                $playerId = data_get($player, 'player.id'),
                fn ($q) => $q->where('external_id', $playerId),
                fn ($q) => $q->where('name', data_get($player, 'player.name'))
            )
            ->api()
            ->first();

        if (!$apiPlayer) {
            $apiPlayer = Player::create([
                'name' => data_get($player, 'player.name'),
                'external_id' => $playerId ?: null,
                'source' => Source::Api,
            ]);
        }

        return $apiPlayer;
    }

    public function validateFixture(int $fixtureId): ?object
    {
        $filePath = ApiFootball::$baseDir . 'fixtures/' . $fixtureId . '.json';

        if (Storage::disk('local')->missing($filePath)) {
            dump('missing: ' . $fixtureId);
            return null;
        }

        $fixture = json_decode(Storage::disk('local')->get($filePath));

        if (!$fixture) {
            dump('bad json: ' . $fixtureId);
            return null;
        }

        if (count($fixture->events) === 0) {
            dump('Empty events: ' . $fixtureId);
            return null;
        }

        return $fixture;
    }

    public function syncPlayerTeam(
        Player $apiPlayer,
        int $teamId,
        ApiPlayerStatistics $apiPlayerStatistics,
        object $statistics
    ): void {
        $apiTeamId = $this->apiLeague->teams->firstWhere('external_id', $teamId)->id;

        $apiPlayer->teams()->withTimestamps()->syncWithoutDetaching([
            $apiTeamId => [
                'position_id' => $apiPlayerStatistics->events[EventEnum::Position->value],
                'number' => data_get($statistics, 'games.number'),
            ],
        ]);
    }

    public function upsertPlayerEvents(Player $apiPlayer, ApiPlayerStatistics $apiPlayerStatistics): void
    {
        $events = [];

        foreach ($apiPlayerStatistics->events as $name => $total) {
            if ($total == 0) {
                continue;
            }

            $events[] = [
                'event_id' => $this->events->firstWhere('name', $name)->id,
                'fixture_id' => $this->fixture->id,
                'player_id' => $apiPlayer->id,
                'total' => $total,
            ];
        }

        EventFixture::upsert($events, ['event_id', 'fixture_id', 'player_id'], ['total']);
    }

    public function setEvents(): void
    {
        $this->events = Event::all();
    }
}
