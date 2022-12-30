<?php

namespace App\Api\DTO;

use App\Api\DTO\ApiFixture;
use App\Enums\Event;
use App\Enums\LineupPosition;
use App\Enums\Position;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class ApiPlayerStatistics extends DataTransferObject
{
    public array $events = [];
    public array $minuteInterval = [];

    public function __construct(
        public ?object $statistics,
        public ApiFixture $fixture,
        public int $playerId,
        public int $teamId
    ) {
        if (is_null($this->statistics)) {
            return;
        }

        if (is_null(data_get($this->statistics, 'games.minutes'))) {
            $this->events = [
                Event::Position->value => $this->getPosition(),
                Event::OnTheBench->value => 1,
            ];
            return;
        }

        $this->setAdditionalData();

        $this->setEvents();
    }

    public function setEvents(): void
    {
        $this->events = [
            Event::Minutes->value => $this->getMinutes($this->statistics->games->minutes),
            Event::Goals->value => $this->statistics->goals->total,
            Event::Assists->value => $this->statistics->goals->assists,
            Event::Shots->value => $this->statistics->shots->total,
            Event::ShotsOnTarget->value => $this->statistics->shots->on,
            Event::KeyPasses->value => $this->statistics->passes->key,
            Event::GoalsConceded->value => $this->getPlayerGoalsConceded(),
            Event::PenaltiesScored->value => $this->statistics->penalty->scored,
            Event::PenaltiesWon->value => $this->getPenaltiesWon(),
            Event::PenaltiesWonWithoutAssist->value => $this->getPenaltiesWonWithoutAssist(),
            Event::PenaltiesCommited->value => $this->statistics->penalty->commited,
            Event::PenaltiesSaved->value => $this->statistics->penalty->saved,
            Event::PenaltiesMissed->value => $this->statistics->penalty->missed,
            Event::OwnGoals->value => data_get($this->fixture->ownGoals, $this->playerId),
            Event::Saves->value => $this->statistics->goals->saves,
            Event::YellowCards->value => $this->statistics->cards->yellow,
            Event::RedCards->value => $this->statistics->cards->red,
            Event::Position->value => $this->getPosition(),
            Event::LineupPosition->value => $this->getLineupPositionId(),
            Event::Started->value => (int) $this->isPlayerStarted(),
        ];
    }

    public function setAdditionalData(): void
    {
        $this->minuteInterval = $this->getMinuteInterval();
    }

    public function getMinutes(?int $minutes): ?int
    {
        return max($minutes, $this->minuteInterval[1] - $this->minuteInterval[0]);
    }

    public function isPlayerStarted(): bool
    {
        return $this->fixture->startedPlayers->where('player.id', $this->playerId)->isNotEmpty();
    }

    public function getPlayerLineupPosition(): ?string
    {
        $playerGrid = $this->fixture->startedPlayers->firstWhere('player.id', $this->playerId)->player->grid ?? null;
        if (is_null($playerGrid)) {
            return null;
        }

        $formation = $this->fixture->formations[$this->teamId];
        preg_match('/(?<G>)(?<D>\d)\-(?<M>\d\-?\d?\-?\d?)\-(?<F>\d)/', $formation, $matches);

        $playerPosition = $this->statistics->games->position;
        $playerOrder = strlen($matches[$playerPosition]) === 1 ? (int) $matches[$playerPosition] : $matches[$playerPosition];
        $playerLine = (int) explode(':', $playerGrid)[0];
        $lineupPositions = config('constants.lineup_positions');

        return $lineupPositions[$playerGrid] ??
            $lineupPositions[$playerLine][$playerOrder][$playerGrid] ??
            $lineupPositions[$formation][$playerGrid] ??
            null;
    }

    public function getLineupPositionId(): ?int
    {
        return LineupPosition::api($this->getPlayerLineupPosition());
    }

    public function getPosition(): ?Position
    {
        return Position::api($this->statistics->games->position);
    }

    public function getPlayerGoalsConceded(): int
    {
        $goalsConceded = collect(data_get($this->fixture->goalsConceded, $this->teamId, []));

        return $goalsConceded->filter(function ($goalMinute) {
            return $goalMinute >= $this->minuteInterval[0] && $goalMinute <= $this->minuteInterval[1];
        })->count();
    }

    public function getPenaltiesWon(): ?int
    {
        return $this->statistics->penalty->scored > 0 ? null : $this->statistics->penalty->won;
    }

    public function getPenaltiesWonWithoutAssist(): ?int
    {
        return $this->statistics->penalty->scored > 0 ? $this->statistics->penalty->won : null;
    }

    public function getMinuteInterval(): array
    {
        $minuteInterval = data_get($this->fixture->substitutedTwicePlayerIntervals, $this->playerId);

        if ($minuteInterval) {
            return $minuteInterval;
        }

        if ($this->statistics->games->substitute) {
            return [$this->fixture->minutes - $this->statistics->games->minutes, $this->fixture->minutes];
        }

        $notFinished = $this->fixture->substitutions->contains($this->playerId) || $this->statistics->cards->red;
        $lastMinute = $notFinished ? $this->statistics->games->minutes : $this->fixture->minutes;

        return [0, $lastMinute];
    }
}
