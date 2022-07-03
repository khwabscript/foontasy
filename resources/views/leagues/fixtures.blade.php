@extends('layouts.app')

{{-- @section('title', __('fixtures.League fixtures, grouped by team', ['league' => __('leagues.genitive.' . $league->name)]))

@section('description')
{{ __('fixtures.Fixture difficulties', ['league' => __('leagues.country.' . $league->name)]) }}.
@endsection --}}

@section('content')    
    {{-- <x-backlink :link="'/fixtures'" /> --}}
    
    <div class="md:max-w-screen-md mx-auto">
        @include('leagues.table')
    </div>
@endsection
