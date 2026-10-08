{{--
    Logo.

    The client's supplied lockup, as-is: mark, "BAARDY MICRO CAPITAL" and
    "(REGISTERED MICROFINANCE)". Two transparent files in public/images/:
    baardy-logo.png (coloured, for light backgrounds) and
    baardy-logo-reversed.png (name in white, for dark ones).

    Assets live in public/images/, NOT in public/build/. The build directory is
    Vite's output and is emptied on every `npm run build`.

    ASK THE CLIENT FOR VECTOR ARTWORK (SVG/AI/EPS). These are derived from a
    raster image; an SVG would be a fraction of the weight and sharp at
    every size.

    Props
      tone     'ink' (default) for light backgrounds, 'inverse' for dark
               backgrounds, or 'adaptive' for the header over the hero
               photograph: reversed while the header's `group/nav` carries
               [data-over-photo], coloured otherwise
--}}
@props(['tone' => 'ink'])

@php
    $size = 'h-11 w-auto shrink-0 sm:h-12 2xl:h-14';
@endphp

<a
    href="{{ url('/') }}"
    {{ $attributes->merge(['class' => 'group inline-flex items-center rounded-xs']) }}
>
    <span class="sr-only">{{ config('company.legal_name') }}, home</span>

    @if ($tone !== 'inverse')
        <img
            src="{{ asset('images/baardy-logo.png') }}"
            width="510"
            height="151"
            alt=""
            aria-hidden="true"
            decoding="async"
            fetchpriority="high"
            @class([$size, 'group-data-over-photo/nav:hidden' => $tone === 'adaptive'])
        >
    @endif

    @if ($tone !== 'ink')
        <img
            src="{{ asset('images/baardy-logo-reversed.png') }}"
            width="510"
            height="151"
            alt=""
            aria-hidden="true"
            decoding="async"
            @class([$size, 'hidden group-data-over-photo/nav:block' => $tone === 'adaptive'])
        >
    @endif
</a>
