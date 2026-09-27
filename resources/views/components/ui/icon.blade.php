{{--
    Inline icon set.

    A small, hand-picked set kept inline rather than pulled from an icon
    package: it is a handful of shapes, it ships no extra bytes, and it
    inherits currentColor so icons always match their surrounding text.

    Props
      name  one of the keys below
--}}
@props(['name'])

@php
    $paths = [
        'check' => '<path d="M3.5 8.5 7 12l7.5-8"/>',
        'minus' => '<path d="M3.5 9h11"/>',
        'plus' => '<path d="M9 3.5v11M3.5 9h11"/>',
        'chevron-down' => '<path d="M4.5 7 9 11.5 13.5 7"/>',
        'arrow-right' => '<path d="M3.5 9h11M10 4.5 14.5 9 10 13.5"/>',
        'arrow-down' => '<path d="M9 3.5v11M4.5 10 9 14.5l4.5-4.5"/>',
        'pause' => '<path d="M6.5 4.5v9M11.5 4.5v9"/>',
        'play' => '<path d="M6 4.2v9.6L13.5 9 6 4.2Z"/>',
        'arrow-up-right' => '<path d="M5 13 13 5M6.5 5H13v6.5"/>',
        'phone' => '<path d="M6.2 3.5H3.7c-.7 0-1.2.6-1.2 1.3 0 5.9 4.8 10.7 10.7 10.7.7 0 1.3-.5 1.3-1.2v-2.5l-3-1-1.3 1.6a9.6 9.6 0 0 1-4.3-4.3L7.5 6.7l-1.3-3.2Z"/>',
        'mail' => '<path d="M2.5 5.5h13v9h-13v-9Z"/><path d="m2.5 6 6.5 4.5L15.5 6"/>',
        'clock' => '<circle cx="9" cy="9" r="6.5"/><path d="M9 5.5V9l2.5 1.5"/>',
        'lock' => '<path d="M4.5 8h9v6.5h-9V8Z"/><path d="M6.5 8V6a2.5 2.5 0 0 1 5 0v2"/>',
        'linkedin' => '<path d="M4 7.5v7M4 4.2v.1M8 14.5v-4a2.5 2.5 0 0 1 5 0v4"/><path d="M8 7.5v7"/>',
        'x' => '<path d="M3.5 3.5l11 11M14.5 3.5l-11 11"/>',
        'facebook' => '<path d="M11.5 3.5h-1.8A2.7 2.7 0 0 0 7 6.2V15"/><path d="M4.8 8.6h5"/>',

        // The "why us" benefit set. Same geometry rules as the icons above:
        // 18x18, one or two simple strokes, nothing that only reads at a
        // larger size.
        'receipt' => '<path d="M5 2.5h6l2 2v11h-8v-13Z"/><path d="M11 2.5v2h2"/><path d="m6.8 10 1.4 1.4L11 8.5"/>',
        'user' => '<circle cx="9" cy="6.3" r="2.6"/><path d="M4 15.2c0-2.9 2.2-5.2 5-5.2s5 2.3 5 5.2"/>',
        'waveform' => '<path d="M2.5 11c1.4-2 2.9-2 4.3 0s2.9 2 4.3 0 2.9-2 4.4 0"/><circle cx="9" cy="5.3" r="2"/>',
        'percent' => '<circle cx="6.2" cy="6.2" r="1.6"/><circle cx="11.8" cy="11.8" r="1.6"/><path d="M12.8 5.2 5.2 12.8"/>',
        'chat' => '<path d="M3 4.5h12v8H8.2L5 15.2V12.5H3v-8Z"/><path d="M6 7.5h6M6 9.5h4"/>',
        'shield' => '<path d="M9 2.5 14.5 4.6V9c0 4-2.4 6.9-5.5 8-3.1-1.1-5.5-4-5.5-8V4.6L9 2.5Z"/>',
    ];
@endphp

<svg
    viewBox="0 0 18 18"
    fill="none"
    stroke="currentColor"
    stroke-width="1.5"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
    {{ $attributes->merge(['class' => 'size-[1.125em] shrink-0']) }}
>
    {!! $paths[$name] ?? '' !!}
</svg>
