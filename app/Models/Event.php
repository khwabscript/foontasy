<?php

namespace App\Models;

use App\Models\Pivot\EventFixture;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function eventFixtures(): HasMany
    {
        return $this->hasMany(EventFixture::class);
    }
}
