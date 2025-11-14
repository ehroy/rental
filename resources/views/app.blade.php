<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{ $setting->meta_author ?? 'https://github.com/ehroy' }}">
    <meta name="description" content="{{ $setting->meta_description ?? 'Default description' }}">
    <meta name="keywords" content="{{ $setting->meta_keywords ?? 'default,keywords' }}">
    {!! $setting->additional_meta ?? '' !!}
    <title>{{ $setting->name ?? config('app.name') }}</title>
    @if(!empty($setting?->icon))
        <link rel="icon" href="{{ asset('storage/' . $setting->icon) }}">
    @endif
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"></noscript>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @inertiaHead
</head>

<body>
    @inertia
</body>
</html>
