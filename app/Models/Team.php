<?php

namespace App\Models;

use App\Enums\Api\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

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
     */
    public function fixtures(): BelongsToMany
    {
        return $this->belongsToMany(Fixture::class);
    }

    /**
     * Get the fixtures for the league.
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
}
