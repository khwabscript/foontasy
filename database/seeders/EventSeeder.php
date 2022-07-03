<?php

namespace Database\Seeders;

use App\Enums\Event as EventEnum;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = [];

        foreach (EventEnum::values() as $value) {
            $events[] = ['name' => $value];
        }

        Event::upsert($events, ['id']);
    }
}
