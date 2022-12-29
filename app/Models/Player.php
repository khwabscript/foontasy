<?php

namespace App\Models;

use App\Enums\Api\Source;
use App\Models\Pivot\EventFixture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
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
     * Get the events for the player.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_fixture')
            ->using(EventFixture::class)
            ->withPivot('fixture_id', 'total');
    }

    /**
     * Get the events for the player.
     */
    public function fixtures(): BelongsToMany
    {
        return $this->belongsToMany(Fixture::class, 'event_fixture');
    }

    /**
     * Get the teams for the player.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('created_at')->orderByDesc('pivot_created_at');
    }

    /**
     * Get the player's team.
     *
     * @return Attribute
     */
    protected function team(): Attribute
    {
        return Attribute::make(
            get: fn (): Team => $this->teams->first(),
        );
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
}
