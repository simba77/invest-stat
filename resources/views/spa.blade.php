<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body class="h-full">
<div id="app">
    @yield('content')
</div>
@vite(['resources/sass/app.scss', 'resources/js/app.ts'])
</body>
</html>
