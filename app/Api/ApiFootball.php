<?php

namespace App\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ApiFootball
{
    public static $baseDir = 'json/api-football/';

    private static $apiHost = 'v3.football.api-sports.io';
    private static $apiKey = 'secret';
    private static $apiUrl = 'https://v3.football.api-sports.io/';
    private static $season = 2022;
    private static $timezone = 'Europe/Moscow';

    public function __construct()
    {
        self::$apiKey = config('services.api-football.token');
    }

    public static function get(string $endpoint, array $params = []): mixed
    {
        $response = Http::withHeaders([
                'x-rapidapi-host' => self::$apiHost,
                'x-rapidapi-key' => self::$apiKey,
            ])
            ->get(self::$apiUrl . $endpoint, $params)
            ->object();

        return data_get($response, 'response');
    }

    public static function getFixture(int $fixtureId): ?object
    {
        $response = self::get('fixtures', [
            'id' => $fixtureId,
            'timezone' => self::$timezone,
        ]);

        return self::handleResponse(data_get($response, 0), 'fixtures/' . $fixtureId);
    }

    public static function getLeagueFixtures(int $leagueId, ?int $season = null): array
    {
        $response = self::get('fixtures', [
            'league' => $leagueId,
            'season' => self::getSeason($season),
            'timezone' => self::$timezone,
        ]);

        return self::handleResponse($response, 'league-fixtures/' . $leagueId);
    }

    private static function getSeason(?int $season): int
    {
        return $season ?? self::$season;
    }

    private static function handleResponse(mixed $response, string $filePath): mixed
    {
        Storage::disk('local')->put(self::$baseDir . $filePath . '.json', json_encode($response));

        return $response;
    }
}
