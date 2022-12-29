<?php

namespace App\Http\Resources\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'datetime' => $this->datetime->format('Y-m-d H:i'),
            'home_team' => $this->homeTeam->only(['id', 'name']),
            'away_team' => $this->awayTeam->only(['id', 'name']),
            'score' => $this->score,
        ];
    }
}
