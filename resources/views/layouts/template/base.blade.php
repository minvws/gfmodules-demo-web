<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '') }}</title>
    <link rel="preload" href="{{ asset('img/ro-logo.svg') }}" as="image">
    <link href="{{ asset('img/favicon.ico') }}" rel="shortcut icon">
    @stack('styles')
</head>
<body>
<div class="container">

    @hasSection('sidebar')
        @yield('sidebar')
    @endif

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')

        @yield('explanation')
    </main>
</div>

@stack('scripts')
</body>
</html>
