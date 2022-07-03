<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if (request()->path() === '/')
            {{-- footballfantasy.ru --}}
            <meta name="google-site-verification" content="9qsOwFpjHIstoXorD38XNh7PUkfS9tg9FHADl-aP1pU" />
            <meta name="yandex-verification" content="5634164384de1ba0" />
            {{-- foontasy.ru --}}
            <meta name="google-site-verification" content="26gZ7XUeBFacoEoVqFZM2mJFJiwbNIBRCrtRhq2bDPE" />
            <meta name="yandex-verification" content="32bd79466e2e0257" />
            {{-- foontasy.com --}}
            <meta name="google-site-verification" content="VKAdQFIZnV-LANYMXhc0NxhUSa--VrASZae_W4aKuWY" />
            <meta name="yandex-verification" content="46db827bce5ce82e" />
            <meta name="yandex-verification" content="32bd79466e2e0257" />
        @endif
        {{-- Title --}}
        @hasSection('title')
            <title>@yield('title') - {{ config('app.name') }}</title>
        @endif
        @sectionMissing('title')
            <title>{{ config('app.name') }}</title>
        @endif

        {{-- Meta tags --}}
        <meta name="description" content="@yield('description')">
        @yield('meta-tags')

        {{-- Favicon --}}
        @if(request()->getHost() === config('constants.host'))
        <link rel="apple-touch-icon" sizes="180x180" href="/storage/images/favicon/dove/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/storage/images/favicon/dove/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/storage/images/favicon/dove/favicon-16x16.png">
        <link rel="manifest" href="/storage/images/favicon/dove/site.webmanifest">
        <link rel="mask-icon" href="/storage/images/favicon/dove/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/storage/images/favicon/dove/favicon.ico">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-config" content="/storage/images/favicon/dove/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
        @else
        <link rel="apple-touch-icon" sizes="180x180" href="/storage/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/storage/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/storage/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="/storage/images/favicon/site.webmanifest">
        <link rel="mask-icon" href="/storage/images/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/storage/images/favicon/favicon.ico">
        <meta name="msapplication-TileColor" content="#00aba9">
        <meta name="msapplication-config" content="/storage/images/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
        @endif
        
        {{-- Fonts --}}
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> --}}
        {{-- <link rel="stylesheet" type="text/css" href="/css/fonts.css"> --}}

        {{-- Styles --}}
        {{-- <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}"> --}}
        @yield('style')
    </head>
    <body>
        @yield('content')
    </body>
</html>
