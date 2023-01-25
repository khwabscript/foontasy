<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\League;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function calendar()
    {
        $range = [$calendarStart, $calendarEnd] = [now()->startOfWeek(), now()->addMonth()->endOfWeek()];

        $period = CarbonPeriod::create($calendarStart, '1 day', $calendarEnd);

        $fixtures = Fixture::query()
            ->with([
                'league' => fn (BelongsTo $q) => $q->api(),
            ])
            ->whereBetween('datetime', $range)
            ->groupBy('fantasy_tour', 'league_id')
            ->selectRaw('MIN(datetime) as datetime, fantasy_tour, league_id')
            ->get();

        $calendar = [];

        foreach ($period as $date) {
            $calendar[] = [
                'datetime' => $date,
                'fixtures' => $fixtures
                    ->filter(
                        fn (Fixture $f) => $f->datetime->between($date, $date->copy()->addDay())
                    )
                    ->groupBy('league_id')
                    ->transform(fn (Collection $fixtures) => (object) [
                        'league' => $fixtures->first()->league,
                        'deadline' => $fixtures->first()->datetime,
                    ])
                    ->sortBy('deadline'),
            ];
        }

        return view(
            'leagues.calendar',
            [
                'calendar' => array_chunk($calendar, 7),
                'weekPeriod' => CarbonPeriod::create($calendarStart, '1 day', now()->endOfWeek()),
            ]
        );
    }
}
