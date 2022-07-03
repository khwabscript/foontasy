@extends('layouts.app')

@section('content')
    
	@if (request()->path() !== '/')
        <div class="lg:absolute lg:top-4.5 lg:right-2.5 mt-2 mr-1 md:my-4 text-right lg:m-0">
            <x-a href="/" class="lg:block">{{ __('menu.Home') }}</x-a>
        </div>
    @else
        <form class="absolute top-0 w-full flex justify-between items-center md:my-4">
            <select class="px-2 md:ml-4 bg-white font-body text-primary font-semibold text-tiny tracking-widest uppercase" name="lang"
                onchange="this.form.submit()" style="max-width: 7rem">
                @foreach ($locales as $locale)
                <option class="normal-case text-base" value="{{ $locale }}" {{ app()->getLocale() === $locale ? 'selected' : '' }}>
                    <nobr>{{ strtoupper($locale) }}</nobr>
                  </option>
                @endforeach
            </select>
            <x-a class="px-2" href="/contact">{{ __('menu.Contact') }}</x-a>
        </form>
    @endif

    <div class="flex items-center justify-center lg:h-screen {{ request()->path() === '/' ? 'h-screen' : '' }} font-body text-primary font-thin">
        <div class="text-center">
            <h1 class="md:text-title text-4xl leading-10 md:leading-normal md:mb-8 mb-4 mt-0">
                @yield('heading')
            </h1>
            <div class="{{ request()->path() !== '/' ? 'md:max-w-screen-md' : '' }} mx-auto lg:leading-6">
                @yield('links')
            </div>
        </div>
    </div>

@endsection