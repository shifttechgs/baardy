{{--
    Hero.

    ONE SCREEN, NO SCROLL. The hero is exactly the height of the visible
    viewport (svh, so a phone's collapsing toolbar cannot push the foot out of
    view) and everything a visitor needs to decide is inside it. Type sizes are
    clamped against viewport HEIGHT as well as width, so a short laptop screen
    shrinks the headline rather than cropping the actions.

    READING ORDER. Down the left edge, in the order that answers a visitor:

        1. headline      two statements: loans you can read, people you can reach
        2. lead          who it is for, and what makes it different
        3. actions       the ask, directly under the promise
        4. credentials   one quiet line of facts on the RBZ register

    ONE PHOTOGRAPH, SHOWN PROPERLY. A single borrower (the first of
    hero.slides), not a rotating slideshow: nothing to wait for, one message.
    The scrims are light and only where the copy sits -- the foot on a
    phone, the lower left at lg -- so the person reads as a person rather
    than a shape under a colour wash. The photograph still moves: it settles
    in, drifts slowly, and leans a little toward the pointer and away on
    scroll (heroSlides in app.js, which with one slide skips the rotation).

    THE PAPERWORK STARTS HERE. On the right sits a loan offer on the Baardy
    letterhead, set square on the site's frosted panel: interest,
    every fee and the total repayable, each "in writing", with the signature
    line left for the borrower. It is the page's paper motif (How it works,
    the closing slip) introduced on the first screen, and it shows the
    headline's first promise rather than repeating it. It states no figures
    because the site has none to state. Decorative (aria-hidden); xl only.

    CREDENTIALS, HONESTLY. The years figure is counted from the year of first
    RBZ listing (credentials.years.since), so it cannot go stale. A review
    rating appears on the same line only once a real, sourced score is set
    (hero.rating); the local design preview never reaches a live server.
--}}
@php
    $hero = config('marketing.hero');
    $credentials = $hero['credentials'];
    $yearsLending = now()->year - $credentials['years']['since'];
    $rating = $hero['rating'] ?? null;
    // The design-preview sample never leaves a local environment, whatever
    // the flag says: unlabelled, it would read as a real customer claim.
    $rating ??= app()->environment('local') ? ($hero['rating_preview'] ?? null) : null;

    $lines = $hero['heading'];

    $slide = collect($hero['slides'])->first();
    $photo = [
        ...$slide,
        'src' => asset(array_key_last($slide['sources'])),
        'srcset' => collect($slide['sources'])
            ->map(fn (int $width, string $path): string => asset($path).' '.$width.'w')
            ->implode(', '),
    ];

    $branchNames = collect(config('company.branches'))->pluck('name')->join(' & ');
@endphp

{{-- The hero photograph is the page's largest image: fetch it from the head,
     at high priority, with the same candidates the <img> will choose from. --}}
@push('head')
    <link rel="preload" as="image" fetchpriority="high" imagesrcset="{{ $photo['srcset'] }}" imagesizes="(min-width: 1024px) {{ round(108 * (1 + $photo['stretch'])) }}vw, 108vw">
@endpush

<section aria-labelledby="hero-heading" class="hero relative h-svh min-h-[34rem]">
    <div
        x-data="heroSlides(1)"
        class="relative isolate flex h-full flex-col overflow-hidden bg-ink text-paper"
    >

        {{-- The photograph --}}
        <div class="hero-media hero-settle absolute inset-0 -z-20">
            <div x-ref="parallax" class="absolute inset-0 scale-[1.08] will-change-transform">
                <div class="hero-photo is-active absolute inset-0 overflow-hidden" style="--drift-x: 2%; --stretch: {{ 1 + $photo['stretch'] }}">
                    <img
                        src="{{ $photo['src'] }}"
                        srcset="{{ $photo['srcset'] }}"
                        sizes="(min-width: 1024px) {{ round(108 * (1 + $photo['stretch'])) }}vw, 108vw"
                        width="{{ max($photo['sources']) }}"
                        height="{{ (int) round(max($photo['sources']) * 2 / 3) }}"
                        alt="{{ $photo['alt'] }}"
                        fetchpriority="high"
                        decoding="async"
                        @class(['hero-photo-img absolute inset-y-0 left-0 h-full w-full max-w-none object-cover', '-scale-x-100' => $photo['flip']])
                        style="object-position: {{ $photo['position'] }}"
                    >
                </div>
            </div>
        </div>

        {{--
            Scrims, light and local: the top one seats the header; the bottom
            one carries the copy (stronger on a phone, where it runs full
            width); the side one only darkens the lower left at lg.
        --}}
        <div aria-hidden="true" class="absolute inset-x-0 top-0 -z-10 h-36 bg-linear-to-b from-ink/50 to-transparent"></div>
        <div aria-hidden="true" class="absolute inset-x-0 bottom-0 -z-10 h-[70%] bg-linear-to-t from-ink/90 via-ink/45 to-transparent lg:h-[62%] lg:from-ink/75 lg:via-ink/25"></div>
        <div aria-hidden="true" class="absolute inset-0 -z-10 hidden bg-[radial-gradient(ellipse_at_0%_100%,rgb(21_16_25/0.55),transparent_60%)] lg:block"></div>

        <x-ui.container wide class="flex h-full flex-col pt-22 sm:pt-26 lg:pt-28">
            <div class="mt-auto grid gap-10 pb-8 sm:pb-12 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">

                {{-- Proposition and calls to action --}}
                <div class="flex min-w-0 flex-col items-start">

                    {{--
                        The full sentence is given once, as text, for screen
                        readers and search; the animated words beside it are
                        hidden from assistive tech so it is not read twice.
                    --}}
                    <h1
                        id="hero-heading"
                        class="text-[length:clamp(2.5rem,min(6vw,11.5svh),7rem)] leading-[0.98]
                               font-normal tracking-[-0.04em] optical-left sm:whitespace-nowrap"
                    >
                        <span class="sr-only">{{ implode(' ', $lines) }}</span>

                        <span aria-hidden="true">
                            @php $wordIndex = 0; @endphp
                            @foreach ($lines as $lineIndex => $line)
                                <span @class(['block', 'text-paper/60' => $lineIndex > 0])>
                                    @foreach (preg_split('/\s+/', $line) as $word)
                                        <span class="hero-word"><span style="--w: {{ $wordIndex++ }}">{{ $word }}</span></span>
                                    @endforeach
                                </span>
                            @endforeach
                        </span>
                    </h1>

                    <p class="rise mt-6 max-w-[34rem] text-body text-pretty text-paper/85 [animation-delay:650ms] sm:mt-8 sm:text-lead lg:max-w-[38rem]">
                        {{ $hero['lead'] }}
                    </p>

                    <div class="rise mt-7 flex w-full flex-col gap-2.5 [animation-delay:780ms] sm:mt-9 sm:w-auto sm:flex-row sm:gap-3">
                        <x-ui.button
                            :href="config('company.cta.primary.href')"
                            variant="inverse"
                            size="lg"
                            pill
                            class="hero-cta group justify-between gap-5 pr-1.5 pl-6 sm:h-14 sm:pl-7"
                        >
                            {{ config('company.cta.primary.label') }}
                            <span class="inline-flex size-10 items-center justify-center rounded-full bg-accent text-paper sm:size-11
                                         transition-transform duration-300 group-hover:translate-x-0.5">
                                <x-ui.icon name="arrow-right" />
                            </span>
                        </x-ui.button>

                        <x-ui.button
                            :href="config('company.cta.secondary.href')"
                            variant="glass"
                            size="lg"
                            pill
                            class="sm:h-14 sm:px-9"
                        >
                            {{ config('company.cta.secondary.label') }}
                        </x-ui.button>
                    </div>

                    {{-- Credentials: one quiet line, every item checkable. --}}
                    <ul class="rise mt-7 flex flex-wrap items-center gap-x-5 gap-y-2 text-small text-paper/75 [animation-delay:900ms] sm:mt-9">
                        <li class="flex items-center gap-2">
                            <x-ui.icon name="shield" class="text-paper/55" />
                            Licensed by the Reserve Bank of Zimbabwe
                        </li>
                        <li class="flex items-center gap-2">
                            <span aria-hidden="true" class="hidden size-1 rounded-full bg-paper/35 sm:block"></span>
                            <span>
                                <span class="figure-nums text-paper"><span data-count-to="{{ $yearsLending }}" data-count-delay="1000">{{ $yearsLending }}</span>+</span>
                                {{ $credentials['years']['label'] }}
                            </span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span aria-hidden="true" class="hidden size-1 rounded-full bg-paper/35 sm:block"></span>
                            {{ $branchNames }}
                        </li>
                        @if ($rating)
                            <li class="flex items-center gap-2">
                                <span aria-hidden="true" class="hidden size-1 rounded-full bg-paper/35 sm:block"></span>
                                <x-ui.rating
                                    :score="$rating['score'] ?? null"
                                    :count="$rating['count'] ?? null"
                                    :source="$rating['source'] ?? null"
                                    :href="$rating['href'] ?? null"
                                    tone="inverse"
                                    class="whitespace-nowrap text-highlight"
                                />
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- The right-hand rail: the loan offer, and the scroll cue at the foot. --}}
                <div class="hidden flex-col items-end gap-10 lg:flex">
                    {{-- The loan offer, on the site's frosted panel (as on the loans
                         rail, the FAQ card and the Why us tiles), set square. --}}
                    <div aria-hidden="true" class="hero-slide hidden [animation-delay:950ms] xl:block">
                        <div class="w-[21rem] rounded-2xl bg-ink/45 px-6 pt-5 pb-6 text-paper ring-1 ring-paper/15 ring-inset backdrop-blur-xl">
                            <div class="flex items-center justify-between border-b border-paper/25 pb-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('images/baardy-mark.png') }}" alt="" class="size-4">
                                    <span class="text-[0.6875rem] font-medium tracking-[0.12em] uppercase">Baardy Micro Capital</span>
                                </div>
                                <span class="text-[0.6875rem] tracking-[0.12em] text-paper/60 uppercase">Loan offer</span>
                            </div>

                            <dl class="mt-4 text-small">
                                @foreach (['Interest', 'Every fee', 'Total repayable'] as $term)
                                    <div class="flex items-baseline gap-2 py-1.5">
                                        <dt class="text-paper/85">{{ $term }}</dt>
                                        <span class="mb-1 flex-1 border-b border-dotted border-paper/25"></span>
                                        <dd class="flex items-center gap-1.5 text-paper">
                                            <span class="offer-ink" style="--i: {{ $loop->index }}">in writing</span>
                                            <svg viewBox="0 0 18 18" class="size-3.5 text-highlight" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path class="offer-tick" style="--i: {{ $loop->index }}" pathLength="1" d="M3.5 9.5 7 13l7.5-8" />
                                            </svg>
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>

                            <div class="mt-5 flex items-end justify-between gap-4">
                                <div class="flex-1">
                                    {{-- An anonymous flourish, drawn only while the reader
                                         is on "Start an application" (app.css, .offer-sign). --}}
                                    <div class="relative h-9 border-b border-paper/45">
                                        <svg viewBox="0 0 220 52" class="absolute -bottom-2 left-0 h-12 w-[12.5rem] overflow-visible text-paper" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            {{-- One stroke: a tall opening loop, uneven lower-case
                                                 runs, then an underline swash back across. --}}
                                            <path
                                                class="offer-sign"
                                                pathLength="1"
                                                d="M12 40 C 6 22, 14 4, 24 8 C 33 12, 24 34, 15 42 C 28 33, 38 22, 45 26 C 50 29, 43 38, 51 36 C 59 33, 61 23, 67 27 C 71 30, 66 39, 75 35 C 85 30, 88 14, 94 9 C 99 6, 99 20, 94 31 C 91 38, 101 36, 109 29 C 115 24, 119 27, 121 33 C 123 38, 131 32, 138 26 C 143 22, 146 27, 144 33 C 142 39, 150 37, 158 30 C 150 44, 110 48, 58 47 C 100 45, 160 41, 212 33"
                                            />
                                        </svg>
                                    </div>
                                    <p class="mt-1.5 text-[0.6875rem] tracking-[0.08em] text-paper/60 uppercase">Your signature</p>
                                </div>
                                <p class="offer-sign-note pb-6 text-[0.75rem] text-paper/60">only when you&rsquo;re ready</p>
                            </div>
                        </div>
                    </div>

                    <a
                        href="#trust"
                        class="rise group inline-flex items-center gap-3 rounded-full text-small text-paper/80 transition-colors
                               [animation-delay:1150ms] hover:text-paper"
                    >
                        Scroll to explore
                        <span
                            aria-hidden="true"
                            class="inline-flex size-9 items-center justify-center overflow-hidden rounded-full ring-1 ring-paper/30
                                   ring-inset transition-colors duration-300 group-hover:bg-paper/10 group-hover:ring-paper/60"
                        >
                            <x-ui.icon name="arrow-down" class="hero-scroll" />
                        </span>
                    </a>
                </div>
            </div>
        </x-ui.container>
    </div>
</section>
