@props([
    'title' => 'Jigar Dhulla - Backend engineer',
    'description' => 'Jigar Dhulla builds backend systems that handle growth, and leads the teams that build them. Laravel, PHP, MySQL and AWS, from Pune.',
    'robots' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if ($robots)
        <meta name="robots" content="{{ $robots }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/jigar-laracon.webp') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="@jigar_dhulla">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{ $slot }}
</body>
</html>
