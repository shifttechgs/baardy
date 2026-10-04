{{--
    Button / link button.

    Renders an <a> when given an href and a <button> otherwise, so the correct
    element is always used for the job. Focus styling comes from the global
    :focus-visible rule, which keeps it identical everywhere.

    THE SITE'S CTA is the pill with an arrow in a circle on its right (`arrow`):
    one shape, one height per size, and one motion -- the circle eases forward
    on hover and the whole button settles slightly on press. Use `arrow` for
    every call to action; the WhatsApp button (x-ui.whatsapp-cta) shares it.

    Props
      variant  'primary' | 'secondary' | 'ghost' | 'inverse' | 'glass'
               ('glass' is for photographs only: translucent, blurred, light text)
      size     'md' | 'lg'
      arrow    the standard CTA: a pill with the arrow circle (implies `pill`)
      pill     fully rounded ends (older buttons that draw their own circle)
      href     renders an anchor instead of a button
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'arrow' => false,
    'pill' => false,
    'href' => null,
    'type' => 'button',
])

@php
    $pill = $pill || $arrow;

    $base = 'group inline-flex items-center '.($arrow ? 'justify-between' : 'justify-center').' gap-2 font-medium '
        .'whitespace-nowrap transition-[color,background-color,border-color,transform] duration-200 select-none active:scale-[0.98] '
        .($pill ? 'rounded-full' : 'rounded-sm');

    $sizes = [
        'md' => 'h-11 px-5 text-small',
        'lg' => 'h-13 px-7 text-body',
    ];

    // With the arrow circle the right padding shrinks to frame it.
    $arrowSizes = [
        'md' => 'h-11 gap-3 pr-1.5 pl-5 text-small',
        'lg' => 'h-13 gap-3 pr-1.5 pl-6 text-body',
    ];

    $variants = [
        'primary' => 'bg-accent text-paper hover:bg-accent-strong',
        'secondary' => 'bg-paper text-ink border border-line-strong hover:border-ink hover:bg-mist',
        'ghost' => 'text-ink hover:bg-mist',
        'inverse' => 'bg-paper text-ink hover:bg-mist',
        'glass' => 'bg-paper/10 text-paper ring-1 ring-inset ring-paper/25 backdrop-blur-md hover:bg-paper/20',
    ];

    $circles = [
        'primary' => 'bg-paper/15 text-paper',
        'secondary' => 'bg-accent text-paper',
        'ghost' => 'bg-accent text-paper',
        'inverse' => 'bg-accent text-paper',
        'glass' => 'bg-paper/15 text-paper',
    ];

    $sizeClasses = $arrow ? ($arrowSizes[$size] ?? $arrowSizes['md']) : ($sizes[$size] ?? $sizes['md']);
    $classes = trim($base.' '.$sizeClasses.' '.($variants[$variant] ?? $variants['primary']));
    $circleSize = $size === 'lg' ? 'size-10' : 'size-8';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
        @if ($arrow)
            <span aria-hidden="true" class="inline-flex {{ $circleSize }} shrink-0 items-center justify-center rounded-full {{ $circles[$variant] ?? $circles['primary'] }} transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-0.5">
                <x-ui.icon name="arrow-right" />
            </span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
        @if ($arrow)
            <span aria-hidden="true" class="inline-flex {{ $circleSize }} shrink-0 items-center justify-center rounded-full {{ $circles[$variant] ?? $circles['primary'] }} transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-0.5">
                <x-ui.icon name="arrow-right" />
            </span>
        @endif
    </button>
@endif
