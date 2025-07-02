<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'Laravel' }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<style>
    @import ('~lucide-static/font/Lucide.css');
</style>
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp

<link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
<script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script> --}}

@fluxAppearance
