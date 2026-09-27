{{--
    Logo.

    The supplied artwork is a three-line lockup: mark, "BAARDY MICRO CAPITAL",
    and "(REGISTERED MICROFINANCE)". Set at a navigation bar's ~30px it turns
    to mush -- the third line is roughly 4px tall and unreadable.

    So the lockup is split: the mark is used as artwork, and the name is set as
    live text beside it. This is standard practice for a tall lockup and it is
    still the client's identity -- same mark, same hierarchy, same colour. It
    also scales, recolours for the dark foot, and stays sharp on any display.

    The full lockup is kept at public/images/baardy-logo.png for the places
    that can afford its height -- share images, documents, letterheads.

    Assets live in public/images/, NOT in public/build/. The build directory is
    Vite's output and is emptied on every `npm run build`.

    ASK THE CLIENT FOR VECTOR ARTWORK (SVG/AI/EPS). These are derived from a
    raster PNG; an SVG mark would be a fraction of the weight and sharp at
    every size.

    Props
      tone     'ink' (default), 'inverse' for dark backgrounds, or 'adaptive'
               for the header over the hero photograph: light while the
               header's `group/nav` carries [data-over-photo], ink otherwise
      compact  drop the "Micro Capital" descriptor below xl. The header runs
               seven nav items, a phone number and a button on one line, and
               below 1280px the full name is the difference between fitting and
               wrapping mid-word.
--}}
@props(['tone' => 'ink', 'compact' => false])

<a
    href="{{ url('/') }}"
    {{ $attributes->merge(['class' => 'group inline-flex items-center gap-3 rounded-xs']) }}
>
    <span class="sr-only">{{ config('company.legal_name') }}, home</span>

    <img
        src="{{ asset('images/baardy-mark.png') }}"
        width="192"
        height="192"
        alt=""
        aria-hidden="true"
        class="size-10 shrink-0 2xl:size-11"
        decoding="async"
        fetchpriority="high"
    >

    {{-- Mirrors the lockup's own hierarchy: name solid, descriptor lighter. --}}
    <span
        aria-hidden="true"
        @class([
            'text-[1.125rem] leading-none font-semibold 2xl:text-[1.25rem] tracking-[-0.03em] whitespace-nowrap',
            'text-ink' => $tone === 'ink',
            'text-paper' => $tone === 'inverse',
            // In the header: the wordmark folds away when the pill condenses.
            'inline-block max-w-[16rem] overflow-hidden text-ink transition-[color,max-width,opacity] duration-500 group-data-over-photo/nav:text-paper group-data-compact/nav:max-w-0 group-data-compact/nav:opacity-0' => $tone === 'adaptive',
        ])
    >
        Baardy<span
            @class([
                'font-normal',
                'hidden xl:inline' => $compact,
                'text-muted' => $tone === 'ink',
                'text-ink-muted' => $tone === 'inverse',
                'text-muted transition-colors duration-300 group-data-over-photo/nav:text-paper/70' => $tone === 'adaptive',
            ])
        > Micro Capital</span>
    </span>
</a>
