{{--
    Hero.

    After trova-travel.framer.website: the photograph is an inset, rounded
    frame on the page (not edge to edge), and almost nothing sits on it.

        bottom left    one quiet line (the licence, checkable at the RBZ), the
                       headline in two lines, and one button
        bottom right   the sentence on who it is for, and three small chips
                       for the loans people ask about first

    Restraint is the polish: no panels, no offer card, no row of credentials,
    no scroll cue, one light scrim at the foot. The headline is set large and
    tight in plain white; the lead sits far right, right-aligned, on two lines.
    Everything fits one screen (svh, so a phone's collapsing toolbar cannot
    push the foot out of view); type is clamped against viewport HEIGHT as
    well as width.

    ONE STATIC PHOTOGRAPH, per the client: the first entry of hero.slides, at
    full resolution (2400px wide; the browser picks the smaller file on small
    screens). No slideshow, no drift, no pointer movement. The other entries
    in hero.slides are kept in config for reuse but not shown.

    WHILE A PROMOTION RUNS. A live promotion placed in the hero (admin panel)
    appears as a small flyer card above the sentence on the right (lg and up);
    the bar above the header carries it at every size.

    CREDENTIALS, HONESTLY. The only claim is the licence, checkable at the RBZ
    (the years-lending figure lives in the section below the hero). A review
    rating joins the line only once a real, sourced score is set (hero.rating);
    the local design preview never reaches a live server.
--}}
@php
    $hero = config('marketing.hero');
    $credentials = $hero['credentials'];

    $rating = $hero['rating'] ?? null;
    // The design-preview sample never leaves a local environment, whatever
    // the flag says: unlabelled, it would read as a real customer claim.
    $rating ??= app()->environment('local') ? ($hero['rating_preview'] ?? null) : null;

    $lines = $hero['heading'];

    // The one photograph, with its responsive candidates (the widest is the
    // fallback `src`).
    $slide = collect($hero['slides'])->first();
    $photo = [
        ...$slide,
        'src' => asset(array_key_last($slide['sources'])),
        'srcset' => collect($slide['sources'])
            ->map(fn (int $width, string $path): string => asset($path).' '.$width.'w')
            ->implode(', '),
    ];

    // A live promotion placed in the hero (admin panel), shown as a flyer
    // card on the right. Nothing renders when none is running.
    $heroPromotion = \App\Models\Promotion::forPlacement('hero');

    // The three chips: the loans people ask about first, linked to the loans.
    $chips = [
        ['label' => 'Salary', 'icon' => 'M3 8h18v12H3zM9 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18'],
        ['label' => 'Farming', 'icon' => 'M12 21v-9M12 12c0-4 3-6 7-6 0 4-3 6-7 6ZM12 15c0-3-2-5-6-5 0 3 2 5 6 5Z'],
        ['label' => 'Business', 'icon' => 'M4 9l1.5-5h13L20 9M4 9v11h16V9M10 20v-5h4v5'],
    ];
@endphp

{{-- The hero photograph is the page's largest image: fetch it from the head,
     at high priority, with the same candidates the <img> will choose from. --}}
@push('head')
    <link rel="preload" as="image" fetchpriority="high" imagesrcset="{{ $photo['srcset'] }}" imagesizes="100vw">
@endpush

<section aria-labelledby="hero-heading" class="hero relative h-svh min-h-[36rem] p-2">
    <div class="relative isolate flex h-full flex-col overflow-hidden rounded-[1.25rem] bg-ink text-paper">

        {{-- The photograph: one, still, at full resolution. --}}
        {{-- Calm motion: the wrapper travels a little slower than the page as it
             scrolls away (hero-photo-scroll); the picture itself settles in on
             arrival (hero-photo-in). Two elements, because each owns one
             animation. See the hero rules in app.css. --}}
        <div class="hero-photo-scroll absolute inset-0 -z-20">
            <img
                src="{{ $photo['src'] }}"
                srcset="{{ $photo['srcset'] }}"
                sizes="100vw"
                width="{{ max($photo['sources']) }}"
                height="{{ (int) round(max($photo['sources']) * 2 / 3) }}"
                alt="{{ $photo['alt'] }}"
                fetchpriority="high"
                decoding="async"
                @class(['hero-photo-in absolute inset-0 size-full max-w-none object-cover', '-scale-x-100' => $photo['flip']])
                style="object-position: {{ $photo['position'] }}"
            >
        </div>

        {{-- One light scrim at the foot to seat the type, and a hint at the top for the header. --}}
        <div aria-hidden="true" class="absolute inset-x-0 top-0 -z-10 h-32 bg-linear-to-b from-ink/30 to-transparent"></div>
        <div aria-hidden="true" class="absolute inset-x-0 bottom-0 -z-10 h-[58%] bg-linear-to-t from-ink/70 via-ink/25 to-transparent"></div>

        <x-ui.container wide class="flex h-full flex-col px-4! pt-28 sm:px-8! sm:pt-32 lg:px-14! 2xl:px-[4.5rem]!">
            <div class="mt-auto flex flex-col gap-8 pb-20 sm:pb-[4.5rem] lg:flex-row lg:items-end lg:justify-between lg:gap-12 lg:pb-[4.5rem]">

                {{-- Left: the licence line, the headline, the ask --}}
                <div class="hero-lift flex min-w-0 flex-col items-start lg:w-1/2">
                    <p class="rise flex flex-wrap items-center gap-x-2.5 gap-y-1 text-body font-medium text-paper sm:text-lead [animation-delay:200ms]">
                        <span>Licensed Microfinance</span>
                        <span aria-hidden="true">&middot;</span>
                        <span>Est. {{ $credentials['years']['since'] }}</span>
                        @if ($rating)
                            <span aria-hidden="true">&middot;</span>
                            <x-ui.rating
                                :score="$rating['score'] ?? null"
                                :count="$rating['count'] ?? null"
                                :source="$rating['source'] ?? null"
                                :href="$rating['href'] ?? null"
                                tone="inverse"
                                class="whitespace-nowrap text-highlight"
                            />
                        @endif
                    </p>

                    {{--
                        The full sentence is given once, as text, for screen
                        readers and search; the animated words beside it are
                        hidden from assistive tech so it is not read twice.
                    --}}
                    <h1
                        id="hero-heading"
                        class="mt-5 text-[length:clamp(2.75rem,min(6.67vw,13svh),6rem)] leading-[1] font-medium
                               tracking-[-0.03em] optical-left sm:mt-6 sm:whitespace-nowrap"
                    >
                        <span class="sr-only">{{ implode(' ', $lines) }}</span>

                        <span aria-hidden="true">
                            @php $wordIndex = 0; @endphp
                            @foreach ($lines as $line)
                                <span class="block">
                                    @foreach (preg_split('/\s+/', $line) as $word)
                                        <span class="hero-word"><span style="--w: {{ $wordIndex++ }}">{{ $word }}</span></span>
                                    @endforeach
                                </span>
                            @endforeach
                        </span>
                    </h1>

                    <div class="rise mt-7 flex w-full flex-col items-stretch [animation-delay:780ms] sm:mt-7 sm:w-auto">
                        <x-ui.button
                            :href="config('company.cta.primary.href')"
                            variant="inverse"
                            arrow
                            class="hero-cta"
                        >
                            {{ config('company.cta.primary.label') }}
                        </x-ui.button>

                    </div>
                </div>

                {{-- Right: the offer and the loans people ask about first. The outer
                     element lifts on scroll WITHOUT fading (it holds frosted glass);
                     the inner one carries the arrival animation. --}}
                <div class="hero-lift-soft lg:w-1/2 lg:max-w-[44rem]">
                <div class="rise flex flex-col gap-5 [animation-delay:900ms] lg:items-end lg:text-right">
                    @if ($heroPromotion)
                        <a
                            href="{{ route('promotions.show', $heroPromotion) }}"
                            class="group/flyer hidden w-72 rounded-2xl bg-ink/45 p-2 text-left text-paper ring-1 ring-paper/15 backdrop-blur-xl ring-inset
                                   transition-[background-color,box-shadow] duration-500 hover:bg-ink/55 hover:ring-paper/30
                                   md:[@media(min-height:44rem)]:block lg:block lg:w-64 xl:w-72 2xl:w-80"
                        >
                            {{-- The frame takes the poster's own proportions (so a square
                                 poster is not squeezed into a wide strip) and is capped to
                                 a share of the screen height, so on a short screen the card
                                 stays clear of the headline. --}}
                            <span class="relative block max-h-[34svh] w-full overflow-hidden rounded-xl bg-accent" style="aspect-ratio: {{ $heroPromotion->imageAspectRatio() }}">
                                @if ($heroPromotion->imageUrl())
                                    <img src="{{ $heroPromotion->imageUrl() }}" alt="" aria-hidden="true" decoding="async"
                                         class="absolute inset-0 size-full scale-125 object-cover opacity-50 blur-2xl">
                                    <img src="{{ $heroPromotion->imageUrl() }}" alt="{{ $heroPromotion->imageAlt() }}" decoding="async"
                                         class="relative size-full object-contain transition-transform duration-[1200ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/flyer:scale-[1.03]">
                                @else
                                    <span class="flex size-full items-end p-4 text-body leading-snug text-balance">{{ $heroPromotion->summary }}</span>
                                @endif
                            </span>
                            <span class="flex items-end justify-between gap-3 px-3 pt-3 pb-2">
                                <span class="flex min-w-0 flex-col gap-1.5">
                                    <span class="flex items-center gap-2 text-small">
                                        <span class="shrink-0 rounded-full bg-highlight px-2.5 py-0.5 text-micro font-medium whitespace-nowrap">Limited offer</span>
                                        <span class="figure-nums whitespace-nowrap text-paper/60">Ends {{ $heroPromotion->ends_at->format('j M') }}</span>
                                    </span>
                                    <span class="text-body leading-snug text-balance">{{ $heroPromotion->title }}</span>
                                </span>
                                {{-- The call-to-action arrow: a frosted circle, easing forward on hover. --}}
                                <span aria-hidden="true" class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-paper/15 text-paper transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/flyer:translate-x-0.5">
                                    <x-ui.icon name="arrow-right" />
                                </span>
                            </span>
                        </a>
                    @endif

                    <p class="max-w-md text-body text-pretty text-paper sm:text-lead lg:max-w-[30rem] lg:text-[1.25rem] lg:leading-snug lg:text-balance">
                        {{ $hero['lead'] }}
                    </p>

                    <ul class="hidden flex-wrap gap-2 sm:flex lg:justify-end">
                        @foreach ($chips as $chip)
                            <li>
                                <a
                                    href="/#products"
                                    class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-ink/60 px-3 py-2 text-body font-medium text-paper backdrop-blur-md
                                           transition-colors duration-200 hover:bg-ink/75"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-4.5"><path d="{{ $chip['icon'] }}" /></svg>
                                    {{ $chip['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                </div>
            </div>
        </x-ui.container>
    </div>
</section>
