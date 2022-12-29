<?php

namespace App\Models\Pivot;

use App\Models\Fixture;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventFixture extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'player_id',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total' => 'integer'
    ];

    /**
     * Get the events for the player.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }
}
