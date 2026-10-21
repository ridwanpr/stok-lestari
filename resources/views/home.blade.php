<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface-base">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Stok Lestari') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center font-sans antialiased bg-surface-base">
    <h1 class="text-3xl font-bold tracking-tight text-ink-primary">
        {{ config('app.name', 'Stok Lestari') }}
    </h1>
</body>
</html>
