<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{mix('assets/app.css')}}" rel="stylesheet">
    <title>{{ config('app.name') }}</title>
</head>
<body class="h-full">
<div id="app">
    @yield('content')
</div>
<script src="{{ mix('assets/app.js') }}"></script>
</body>
</html>
