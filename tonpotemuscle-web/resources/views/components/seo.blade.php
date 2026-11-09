@props([
    'title' => 'Ton Pote Musclé',
    'description' => 'Ton corps, ton rythme. Séances live et programmes avec ton coach, sur Ton Pote Musclé.',
    'url' => null,
])

@php
    $title = $title ?: 'Ton Pote Musclé';
    $description = $description ?: 'Ton corps, ton rythme. Séances live et programmes avec ton coach, sur Ton Pote Musclé.';
    $url = $url ?: url()->current();
    $image = asset('assets/img/logo.webp');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="icon" href="{{ $image }}" type="image/webp">
<link rel="apple-touch-icon" href="{{ $image }}">
<meta property="og:site_name" content="Ton Pote Musclé">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
