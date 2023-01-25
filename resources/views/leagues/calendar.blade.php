@extends('layouts.app')

@section('content')    
  <div class="md:max-w-screen-md mx-auto">
    <x-table>
      <thead>
        <tr>
          @foreach($weekPeriod as $weekDay)
            <x-th class="uppercase" style="text-align: center; padding: 1rem">{{ $weekDay->format('l') }}</x-th>
          @endforeach
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @foreach($calendar as $weekFixtures)
          <tr>
            @foreach($weekFixtures as $weekDayFixtures)
              <x-td class="font-medium text-center" style="padding: 2rem">
                {{ $weekDayFixtures['datetime']->format('j') }}<br>
                @foreach($weekDayFixtures['fixtures'] as $weekDayFixture)
                  @continue(is_null($weekDayFixture))
                  {{ Str::title(str_replace('-', ' ', $weekDayFixture->league->name)) . ' - ' . $weekDayFixture->deadline->format('H:i') }}
                  <br>
                @endforeach
              </x-td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </x-table>
  </div>
@endsection
