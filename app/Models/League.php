<?php

namespace App\Models;

use App\Enums\Api\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'name';
    }

    /**
     * Get the fixtures for the league.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * Get the teams for the league.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * Scope a query to only include api leagues.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeApi(Builder $query): Builder
    {
        return $query->where('source', Source::Api);
    }
}
