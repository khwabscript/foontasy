<?php

namespace App\Api\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class ApiFixture extends DataTransferObject
{
    public Carbon $datetime;

    public Collection $events;

    public object $fixture;

    public array $goalsConceded;

    public int $minutes;

    public array $ownGoals;

    public array $players;

    public array $formations;

    public Collection $startedPlayers;

    public array $substitutedTwicePlayerIntervals;

    public Collection $substitutions;

    public function __construct(object $fixture)
    {
        $this->fixture = $fixture;
        $this->setData();
    }

    public function setData()
    {
        $this->events = collect($this->fixture->events);
        $this->datetime = Carbon::parse(data_get($this->fixture, 'fixture.date'));
        $this->goalsConceded = $this->getGoalsConceded();
        $this->ownGoals = $this->getOwnGoals();
        $this->players = $this->getPlayers();
        $this->minutes = $this->getMinutes();
        $this->substitutions = $this->getSubstitutions();
        $this->substitutedTwicePlayerIntervals = $this->getSubstitutedTwicePlayerIntervals();
        $this->startedPlayers = $this->getStartedPlayers();
        $this->formations = $this->getFormations();
    }

    public function getFormations(): array
    {
        $lineups = $this->getLineups();

        return [
            data_get($lineups, '0.team.id') => data_get($lineups, '0.formation', ''),
            data_get($lineups, '1.team.id') => data_get($lineups, '1.formation', ''),
        ];
    }

    public function getLineups(): array
    {
        return $this->fixture->lineups;
    }

    public function getGoalsConceded(): array
    {
        $goalsConceded = [];

        foreach($this->events->pluck('team.id')->unique() as $teamId) {
            $goalsConceded[$teamId] = $this->events
                ->where('type', 'Goal')
                ->where('detail', '<>', 'Missed Penalty')
                ->where('comments', '<>', 'Penalty Shootout')
                ->where('team.id', '<>', $teamId)
                ->map(fn ($goal) => $goal->time->elapsed + $goal->time->extra)
                ->toArray();
        }

        return $goalsConceded;
    }

    public function getMinutes(): int
    {
        $lastEventTime = $this->events->last()->time;
        $players = collect(data_get($this->fixture, 'players.0.players', []));

        return max(
            $players->pluck('statistics')->flatten(1)->pluck('games.minutes')->max(),
            $lastEventTime->elapsed + $lastEventTime->extra
        );;
    }

    public function getOwnGoals(): array
    {
        return $this->events->where('detail', 'Own Goal')->pluck('player.id')->countBy()->toArray();
    }

    public function getPlayers(): array
    {
        return [
            data_get($this->fixture, 'players.0.team.id') => data_get($this->fixture, 'players.0.players', []),
            data_get($this->fixture, 'players.1.team.id') => data_get($this->fixture, 'players.1.players', []),
        ];
    }

    public function getStartedPlayers(): Collection
    {
        $lineups = $this->getLineups();

        return collect(array_merge($lineups[0]->startXI ?? [], $lineups[1]->startXI ?? []));
    }

    public function getSubstitutions(): Collection
    {
        $substs = $this->events->where('type', 'subst');

        return $substs->pluck('player.id')->merge($substs->pluck('assist.id'));
    }

    public function getSubstitutedTwicePlayerIntervals(): array
    {
        $substs = $this->events->where('type', 'subst');
        $substTwicePlayerIds = $this->getSubstitutedTwicePlayerIds();

        $substTwiceIntervals = [];

        foreach ($substTwicePlayerIds as $playerId) {
            if ($playerId === null) {
                continue;
            }
            foreach (['player', 'assist'] as $type) {
                $time = $substs->firstWhere($type . '.id', $playerId)->time ?? null;
                if (is_null($time)) {
                    unset($substTwiceIntervals[$playerId]);
                    break;
                }
                $substTwiceIntervals[$playerId][] = $time->elapsed + $time->extra;
                sort($substTwiceIntervals[$playerId]);
            }
        }

        return $substTwiceIntervals;
    }

    public function getSubstitutedTwicePlayerIds(): Collection
    {
        $substs = $this->events->where('type', 'subst');

        return $substs->pluck('player.id')->duplicates()->merge($substs->pluck('assist.id')->duplicates());
    }
}
