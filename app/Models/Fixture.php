<?php

namespace App\Models;

use App\Enums\Api\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fixture extends Model
{
    use HasFactory;

    public const POSTPONED_TOUR = 100;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'datetime',
        'tour',
        'fantasy_tour',
        'home_team_id',
        'away_team_id',
        'home_team_goals',
        'away_team_goals',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'datetime' => 'datetime',
        'tour' => 'integer',
        'fantasy_tour' => 'integer',
    ];

    /**
     * Get the events for the fixture.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_fixture');
    }

    /**
     * Get the league that has the fixture.
     *
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the fixtures for the team.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * Get the home team that has the fixture.
     *
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * Get the away team that has the fixture.
     *
     * @return BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * Scope a query to only include finished fixtures.
     *
     * @param  int  $externalId
     * @param  int  $leagueId
     * @return Fixture|null
     */
    public static function findInLeague(int $externalId, int $leagueId): ?self
    {
        return self::query()->where('external_id', $externalId)->firstWhere('league_id', $leagueId);
    }

    /**
     * Scope a query to only include finished fixtures.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('datetime', '<', now()->subHours(2));
    }

    /**
     * Scope a query to only include league fixtures.
     *
     * @param  Builder  $query
     * @param  int  $leagueId
     * @return Builder
     */
    public function scopeLeagueId(Builder $query, int $leagueId): Builder
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope a query to only include upcoming fixtures.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('datetime', '>', now());
    }

    /**
     * Get the fixture's score.
     *
     * @return Attribute
     */
    protected function score(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => is_null($this->home_team_goals) || is_null($this->away_team_goals)
                ? null
                : $this->home_team_goals . ':' . $this->away_team_goals,
        );
    }
}
