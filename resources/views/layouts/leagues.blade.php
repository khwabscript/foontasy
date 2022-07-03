@extends('layouts.index')

@section('links')
	@foreach ($leagueNames as $leagueName)
		<x-a href="/{{ request()->path() }}/{{ $leagueName }}">
			<nobr><i class="mr-3 flag-{{ $leagueFlags[$leagueName] }}"></i>{{ __('leagues.name.' . $leagueName) }}</nobr>
		</x-a>
	@endforeach
@endsection