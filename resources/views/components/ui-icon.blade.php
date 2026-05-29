@props(['name' => 'circle'])

@php
    $icons = [
        'book' => '<path d="M5 4.5A2.5 2.5 0 0 1 7.5 2H19v18H7.5A2.5 2.5 0 0 0 5 22V4.5Z"/><path d="M5 4.5A2.5 2.5 0 0 0 2.5 2H2v18h.5A2.5 2.5 0 0 1 5 22"/><path d="M9 6h6M9 10h6"/>',
        'layers' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 16 9 5 9-5"/>',
        'language' => '<path d="M4 5h9M8.5 3v2M10 5c-.8 4.2-3.1 7-6 9"/><path d="M5.5 9.5c1 1.8 2.2 3.1 3.9 4"/><path d="M14 21l4-10 4 10M15.5 17h5"/>',
        'chart' => '<path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20V8"/>',
        'flag' => '<path d="M5 21V4"/><path d="M5 4h11l-1.4 4L16 12H5"/>',
        'puzzle' => '<path d="M9 3h4v3a2 2 0 1 0 4 0V3h4v7h-3a2 2 0 1 0 0 4h3v7h-7v-3a2 2 0 1 0-4 0v3H3v-7h3a2 2 0 1 0 0-4H3V3h6Z"/>',
        'tasks' => '<path d="M9 7h12M9 12h12M9 17h12"/><path d="m3 7 1 1 2-2M3 12l1 1 2-2M3 17l1 1 2-2"/>',
        'route' => '<path d="M6 19a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M18 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M8.5 15.5C13 15 11 8 15 8"/><path d="M18 11v10"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/>',
        'edit' => '<path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/>',
        'refresh' => '<path d="M20 7v5h-5"/><path d="M4 17v-5h5"/><path d="M18.2 9A7 7 0 0 0 6.1 6.8L4 12"/><path d="M5.8 15A7 7 0 0 0 17.9 17.2L20 12"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'headset' => '<path d="M4 13a8 8 0 0 1 16 0"/><path d="M4 13v3a2 2 0 0 0 2 2h2v-7H6a2 2 0 0 0-2 2Z"/><path d="M20 13v3a2 2 0 0 1-2 2h-2v-7h2a2 2 0 0 1 2 2Z"/><path d="M15 21h1a4 4 0 0 0 4-4"/>',
        'play' => '<path d="m8 5 11 7-11 7V5Z"/>',
        'music' => '<path d="M9 18V5l10-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="16" cy="16" r="3"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
    ];

    $svg = $icons[$name] ?? '<circle cx="12" cy="12" r="8"/>';
@endphp

<svg {{ $attributes->merge(['class' => 'ui-icon', 'viewBox' => '0 0 24 24', 'aria-hidden' => 'true', 'focusable' => 'false']) }}>
    {!! $svg !!}
</svg>
