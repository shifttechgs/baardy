{{--
    A small inline label.

    Used sparingly -- for the "illustrative figures" disclosure and for product
    eligibility notes. Sentence case, never all-caps.

    Props
      tone  'neutral' | 'accent'
--}}
@props(['tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'border-line bg-mist text-muted',
        'accent' => 'border-accent/15 bg-accent-tint text-accent',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1.5 rounded-xs border px-2 py-1 text-micro font-medium '
        .($tones[$tone] ?? $tones['neutral']),
]) }}>
    {{ $slot }}
</span>
