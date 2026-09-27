{{--
    The single horizontal measure for the whole site.

    Gutters widen with the viewport (24 / 40 / 64px) so content never touches
    the edge on a phone and never feels adrift on a large display.

    Props
      as    element to render
      wide  run to the viewport edge, held off it only by the gutter, rather
            than the content measure -- only for the full-bleed hero and the
            header floating over it, which share one set of edges

    The measure is 1200px, widening to 1600px from 2xl (1536px) so a large
    desktop display is not left with wide empty margins.
--}}
@props(['as' => 'div', 'wide' => false])

<{{ $as }} {{ $attributes->merge(['class' => 'mx-auto w-full px-6 sm:px-10 lg:px-16 2xl:px-20 '.($wide ? 'max-w-none' : 'max-w-content 2xl:max-w-content-wide')]) }}>
    {{ $slot }}
</{{ $as }}>
