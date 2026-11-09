@props([
    'title' => null,
    'description' => null,
    'url' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <x-seo :title="$title" :description="$description" :url="$url" />
        <x-font-awesome />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-dvh bg-bg font-sans text-text antialiased">
        {{ $slot }}
    </body>
</html>
