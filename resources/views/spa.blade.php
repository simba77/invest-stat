<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{mix('assets/app.css')}}" rel="stylesheet">
    <title>{{ config('app.name') }}</title>
</head>
<body>
<div id="app">
    @yield('content')
</div>
<script src="{{ mix('assets/app.js') }}"></script>
</body>
</html>
