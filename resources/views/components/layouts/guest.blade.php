<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface-base">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Stok Lestari') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-ink-primary antialiased bg-surface-base">
    <div class="flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
</body>
</html>
