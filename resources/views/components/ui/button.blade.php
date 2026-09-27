{{--
    Button / link button.

    Renders an <a> when given an href and a <button> otherwise, so the correct
    element is always used for the job. Focus styling comes from the global
    :focus-visible rule, which keeps it identical everywhere.

    Props
      variant  'primary' | 'secondary' | 'ghost' | 'inverse' | 'glass'
               ('glass' is for photographs only: translucent, blurred, light text)
      size     'md' | 'lg'
      pill     fully rounded ends, used only inside the hero's rounded frame
      href     renders an anchor instead of a button
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'pill' => false,
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium '
        .'whitespace-nowrap transition-colors duration-150 select-none '
        .($pill ? 'rounded-full' : 'rounded-sm');

    $sizes = [
        'md' => 'h-11 px-5 text-small',
        'lg' => 'h-13 px-7 text-body',
    ];

    $variants = [
        'primary' => 'bg-accent text-paper hover:bg-accent-strong',
        'secondary' => 'bg-paper text-ink border border-line-strong hover:border-ink hover:bg-mist',
        'ghost' => 'text-ink hover:bg-mist',
        'inverse' => 'bg-paper text-ink hover:bg-mist',
        'glass' => 'bg-paper/10 text-paper ring-1 ring-inset ring-paper/25 backdrop-blur-md hover:bg-paper/20',
    ];

    $classes = trim($base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
