<?php

namespace App\Http\Controllers;

use App\Http\Resources\Player\PlayerResource;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(string $leagueName)
    {
        $players = Player::with([
            'fixtures' => fn ($q) => $q->distinct(),
            'fixtures.teams:id,name',
            'fixtures.events',
            'teams',
        ])->paginate();

        return $players;
    }

    public function show(string $leagueName, string $teamName, Player $player): PlayerResource
    {
        $player->load([
            'fixtures' => fn ($q) => $q->distinct(),
            'fixtures.teams:id,name',
            'fixtures.events' => fn ($q) => $q->withPivot('player_id')->where('player_id', $player->id),
            'teams' => fn ($q) => $q->where('name', $teamName)->take(1),
        ]);

        return PlayerResource::make($player);
    }
}
