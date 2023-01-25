<?php

namespace App\Models;

use App\DTO\Team\TeamStatDTO;
use App\Enums\Api\Source;
use App\Models\Traits\Team\HasFantasyTour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;
    use HasFantasyTour;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'external_id',
        'source',
    ];

    /**
     * Get the fixtures for the team.
     *
     * @return BelongsToMany
     */
    public function fixtures(): BelongsToMany
    {
        return $this->belongsToMany(Fixture::class);
    }

    /**
     * Get the fixtures for the league.
     *
     * @return BelongsToMany
     */
    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class);
    }
    /**
     * Scope a query to only include finished fixtures.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeApi(Builder $query): Builder
    {
        return $query->where('source', Source::Api);
    }

    /**
     * Scope a query to only include finished fixtures.
     *
     * @param  Builder  $query
     * @param  array  $externalIds
     * @return Builder
     */
    public function scopeWhereExternalIdIn(Builder $query, array $externalIds): Builder
    {
        return $query->whereIn('external_id', $externalIds);
    }

    /**
     * Get the stat.
     *
     * @return Attribute
     */
    protected function stat(): Attribute
    {
        return Attribute::make(
            get: fn (): TeamStatDTO => new TeamStatDTO(
                $this->finishedFixtures->sortByDesc('datetime'),
                $this->id
            ),
        );
    }

    /**
     * Get the finished fixtures.
     *
     * @return Attribute
     */
    protected function finishedFixtures(): Attribute
    {
        return Attribute::make(
            get: fn (): Collection => $this->fixtures->where('datetime', '<', now()->subHours(2)),
        );
    }

    /**
     * Get the upcoming fixtures.
     *
     * @return Attribute
     */
    protected function upcomingFixtures(): Attribute
    {
        return Attribute::make(
            get: fn (): Collection => $this->fixtures->where('datetime', '>', now()),
        );
    }
}
