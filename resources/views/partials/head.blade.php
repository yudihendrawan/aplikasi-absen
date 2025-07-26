<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'Laravel' }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
{{-- <style>
    @import ('~lucide-static/font/Lucide.css');
</style> --}}

{{-- ketika mode development --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- ketika mode development --}}


{{-- ketika mode production --}}
{{-- @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp
@php
    $baseUrl = rtrim(config('app.url'), '/');
@endphp

<link rel="stylesheet" href="{{ $baseUrl }}/lucide-static/font/Lucide.css">
<link rel="stylesheet" href="{{ $baseUrl }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
<script type="module" src="{{ $baseUrl }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script> --}}

{{-- ketika mode production --}}

@fluxAppearance
