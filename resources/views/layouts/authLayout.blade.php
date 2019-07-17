<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ url('iCheck/square/blue.css') }}">
</head>
    @yield('content')
</html>