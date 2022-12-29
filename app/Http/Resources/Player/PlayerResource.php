<?php

namespace App\Http\Resources\Player;

use App\Http\Resources\Fixture\FixtureResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
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
            'name' => $this->name,
            'team' => $this->when($this->team, [
                'name' => optional($this->team)->name,
            ]),
            'fixutres' => FixtureResource::collection($this->fixtures),
            // 'fixutres' => FixtureResource::collection($this->fixtures->unique()),
        ];
    }
}
