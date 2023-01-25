<x-table>
	{{-- <caption class="px-6 py-1 lg:py-3">
	    @foreach([
        'xP' => __('fixtures.Overall'), 'CS' => __('fixtures.Defence'), 'G' => __('fixtures.Attack'), 'noColor' => __('fixtures.No color')
      ] as $tabKey => $tabName)
	        <span class="px-3 py-2 text-left text-xs leading-8 font-medium tracking-wider whitespace-no-wrap uppercase cursor-pointer hover:bg-gray-800 hover:text-white rounded {{ $loop->iteration === 1 ? 'bg-gray-800 text-white' : 'text-gray-900' }}"
          onclick="tabClickFixture(event, '{{ $tabKey }}')">{{ $tabName }}</span>
	    @endforeach
	</caption> --}}
  <thead>
    <tr>
    	<x-th class="uppercase text-center" style="text-align: left;">
        {{ 'Team' }}
    		{{-- @lang('fixtures.Team') --}}
    	</x-th>
      @foreach($fantasyTourRange as $fantasyTour)
        <x-th class="uppercase" style="text-align: left;">
          {{ 'GW ' . $fantasyTour }}
        	{{-- @lang('fixtures.Gw', ['num' => $fantasyTour]) --}}
        </x-th>
      @endforeach
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-200">
	@foreach($teams as $team)
    <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-200' : 'bg-white' }}">
      <x-td class="font-medium text-center">
        {{-- @lang('teams.' . $team->name) --}}
        {{ $team->name }}
    	</x-td>
      {{-- @foreach($team->fantasyFixtures as $fantasyFixture) --}}
    	@foreach($fantasyTourRange as $fantasyTour)
        @php $difficulty = $team->getFantasyTourDifficulties($fantasyTour, $teams)->overall @endphp
        <x-td class="bg-gradient-to-tl 
        {{-- from-{{ $fantasyFixture->color }}-50 to-white --}}
        " style="color: {{ $difficulty === $difficultyEnum::Hard ? 'red' : ($difficulty === $difficultyEnum::Easy ? 'green' : 'orange') }}">
        	{{-- {{ $team->fixtures->where('fantasy_tour', $fantasyTour)->pluck('teams')->flatten()->filter(fn ($t) => $t->id !== $team->id)->pluck('name')->join(PHP_EOL) }} --}}
          {{ $team->getOpponents($fantasyTour)->pluck('name')->join(PHP_EOL) }}
        </x-td>
      @endforeach
    </tr>
  @endforeach
  </tbody>
</x-table>
{{-- <script>$json = @json($difficulties)</script> --}}
{{-- <script src="{{ mix('js/app.js') }}"></script> --}}
