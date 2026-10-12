{{--
    Hero.

    A light panel with a centred portrait, after the editorial layouts that
    lead with a real person: text on the left, one tall portrait with a single
    cut corner in the middle, and the loans as cards that overlap the
    portrait's right edge.

        left    one quiet line (the licence, checkable at the RBZ), the
                headline in two lines, the promise and one button
        centre  a single still portrait
        right   the lead loans as cards; above them, a live promotion's flyer
                when one runs

    The panel is light, so the header floats on it, transparent and in dark
    type (the homepage asks for the 'floating' header, see layouts/marketing). Cards are flat fills: no
    borders, rings or shadows.

    Aimed at salaried people: the lead card is the Salary-Based Loans product,
    for public- and private-sector employees and government pensioners.

    ONE STATIC PHOTOGRAPH, per the client: the first entry of hero.slides, at
    full resolution (the browser picks the smaller file on small screens). No
    slideshow, no pointer movement. The other entries in hero.slides are kept
    in config for reuse but not shown.

    NO CAPTION. The photograph is stock, so it carries no name, role or claim
    that the person is a customer. A named caption belongs here only once a
    real, consenting customer is photographed.

    MOTION. All of it is transform or clip only (the photograph is never faded
    up from nothing), and off for visitors who ask for reduced motion: the
    portrait's frame rises from its foot, the loan cards slide in one after
    another, and on scroll the portrait trails the page while the cards run
    ahead of it. See the hero rules in app.css.

    WHILE A PROMOTION RUNS. A live promotion placed in the hero (admin panel)
    appears as a flyer card above the loan cards (lg and up); the bar above the
    header carries it at every size.

    CREDENTIALS, HONESTLY. The only claims are the licence and the year of
    first listing, checkable at the RBZ. A review rating joins the first line
    only once a real, sourced score is set (hero.rating); the local design
    preview never reaches a live server.
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
    $widest = max($slide['sources']);
    $photo = [
        ...$slide,
        'src' => asset(array_key_last($slide['sources'])),
        'srcset' => collect($slide['sources'])
            ->map(fn (int $width, string $path): string => asset($path).' '.$width.'w')
            ->implode(', '),
    ];

    // A live promotion placed in the hero (admin panel), shown as a flyer
    // card above the loan cards. Nothing renders when none is running.
    $heroPromotion = \App\Models\Promotion::forPlacement('hero');

    // The loans the cards lead with, salary first.
    $cardSlugs = ['salary-based-loans', 'sme-bridging-finance', 'agricultural-loans'];
    $cards = collect(config('marketing.products'))
        ->whereIn('slug', $cardSlugs)
        ->sortBy(fn (array $product): int => array_search($product['slug'], $cardSlugs))
        ->values();
@endphp

{{-- The hero photograph is the page's largest image: fetch it from the head,
     at high priority, with the same candidates the <img> will choose from. --}}
@push('head')
    <link rel="preload" as="image" fetchpriority="high" imagesrcset="{{ $photo['srcset'] }}" imagesizes="(min-width: 1024px) 34vw, 100vw">
@endpush

<section aria-labelledby="hero-heading" class="hero relative flex flex-col p-2 lg:min-h-[max(36rem,100svh)]">
    <div class="relative isolate flex flex-1 flex-col gap-8 overflow-hidden rounded-[1.25rem] bg-mist p-6 pt-28 text-ink sm:p-10 sm:pt-32 lg:grid lg:grid-cols-12 lg:gap-0 lg:p-0">

        {{-- Left: the licence line, the headline, the promise, the ask --}}
        <div class="flex min-w-0 flex-col justify-between lg:col-span-4 lg:pt-28 lg:pb-14 lg:pr-4 lg:pl-14 2xl:pl-[4.5rem]">
            <div class="hidden lg:block"></div>

            <div>
                <p class="rise flex flex-wrap items-center gap-x-2.5 gap-y-1 text-body font-medium text-accent [animation-delay:200ms]">
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
                    class="mt-4 text-[length:clamp(2.25rem,min(3.15vw,8svh),4rem)] leading-[1] font-medium tracking-[-0.03em]"
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
            </div>

            <div class="rise mt-2 lg:mt-0 [animation-delay:780ms]">
                <p class="max-w-xs text-body text-pretty text-ink-soft sm:text-lead">
                    {{ $hero['lead'] }}
                </p>
                <div class="mt-6">
                    <x-ui.button
                        :href="config('company.cta.primary.href')"
                        arrow
                        class="hero-cta"
                    >
                        {{ config('company.cta.primary.label') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Centre: one tall portrait, one cut corner --}}
        <div class="hero-portrait-drift relative lg:col-span-4 lg:p-3 lg:pt-[5.5rem]">
            <figure
                class="hero-portrait-reveal relative aspect-[4/5] size-full overflow-hidden rounded-[1.75rem] bg-accent-tint lg:aspect-auto"
                style="clip-path: polygon(0 0, 100% 0, 100% 100%, 26% 100%, 0 80%)"
            >
                <img
                    src="{{ $photo['src'] }}"
                    srcset="{{ $photo['srcset'] }}"
                    sizes="(min-width: 1024px) 34vw, 100vw"
                    width="{{ $widest }}"
                    height="{{ (int) round($widest * 3 / 2) }}"
                    alt="{{ $photo['alt'] }}"
                    fetchpriority="high"
                    decoding="async"
                    @class(['hero-photo-in absolute inset-0 size-full max-w-none object-cover', '-scale-x-100' => $photo['flip']])
                    style="object-position: {{ $photo['position'] }}"
                >
            </figure>
        </div>

        {{-- Right: a live promotion when one runs, then the loans as
             cards overlapping the portrait's edge. Raised above the portrait
             so the overlap shows. --}}
        <div class="relative z-10 flex min-w-0 flex-col justify-between gap-6 lg:col-span-4 lg:-ml-16 lg:pt-28 lg:pb-14 lg:pr-14 lg:pl-0 2xl:pr-[4.5rem]">
            @if ($heroPromotion)
            <div class="hidden lg:block lg:pt-6 lg:pl-16">
                    <a
                        href="{{ route('promotions.show', $heroPromotion) }}"
                        class="group/flyer block w-64 rounded-2xl bg-paper p-2 text-left text-ink transition-colors duration-500 hover:bg-accent-tint xl:w-72"
                    >
                        {{-- The frame takes the poster's own proportions (so a square
                             poster is not squeezed into a wide strip) and is capped to
                             a share of the screen height. --}}
                        <span class="relative block max-h-[30svh] w-full overflow-hidden rounded-xl bg-accent" style="aspect-ratio: {{ $heroPromotion->imageAspectRatio() }}">
                            @if ($heroPromotion->imageUrl())
                                <img src="{{ $heroPromotion->imageUrl() }}" alt="" aria-hidden="true" decoding="async" loading="lazy"
                                     class="absolute inset-0 size-full scale-125 object-cover opacity-50 blur-2xl">
                                <img src="{{ $heroPromotion->imageUrl() }}" alt="{{ $heroPromotion->imageAlt() }}" decoding="async" loading="lazy"
                                     class="relative size-full object-contain transition-transform duration-[1200ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/flyer:scale-[1.03]">
                            @else
                                <span class="flex size-full items-end p-4 text-body leading-snug text-paper text-balance">{{ $heroPromotion->summary }}</span>
                            @endif
                        </span>
                        <span class="flex items-end justify-between gap-3 px-3 pt-3 pb-2">
                            <span class="flex min-w-0 flex-col gap-1.5">
                                <span class="flex items-center gap-2 text-small">
                                    <span class="shrink-0 rounded-full bg-highlight px-2.5 py-0.5 text-micro font-medium whitespace-nowrap text-paper">Limited offer</span>
                                    <span class="figure-nums whitespace-nowrap text-muted">Ends {{ $heroPromotion->ends_at->format('j M') }}</span>
                                </span>
                                <span class="text-body leading-snug text-balance">{{ $heroPromotion->title }}</span>
                            </span>
                            <span aria-hidden="true" class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-accent text-paper transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/flyer:translate-x-0.5">
                                <x-ui.icon name="arrow-right" />
                            </span>
                        </span>
                    </a>
            </div>
            @endif

            <div class="hero-cards-drift mt-auto flex flex-col gap-3">
                <p class="hidden text-small font-medium tracking-wide text-muted uppercase lg:block">Loans for salaried people and more</p>

                @foreach ($cards as $i => $product)
                    <a
                        href="{{ route('loans.show', $product['slug']) }}"
                        style="--i: {{ $i }}"
                        @class([
                            'hero-card-in group/card flex items-center gap-4 rounded-2xl p-3 transition-[background-color,transform] duration-300 hover:-translate-y-0.5 lg:max-w-xs',
                            'bg-accent text-paper hover:bg-accent-strong' => $i === 0,
                            'bg-paper text-ink hover:bg-accent-tint' => $i > 0,
                            'lg:ml-16' => $i === 1,
                            'lg:ml-8' => $i === 2,
                        ])
                    >
                        <img
                            src="{{ asset(str_replace('-1600.webp', '-480.webp', $product['image']['src'])) }}"
                            alt=""
                            width="72"
                            height="72"
                            loading="lazy"
                            decoding="async"
                            class="size-16 shrink-0 rounded-xl object-cover"
                        >
                        <span class="min-w-0 flex-1 text-body leading-snug font-medium text-balance">{{ $product['name'] }}</span>
                        <span aria-hidden="true" @class([
                            'inline-flex size-9 shrink-0 items-center justify-center rounded-full transition-transform duration-300 group-hover/card:translate-x-0.5',
                            'bg-paper text-accent' => $i === 0,
                            'bg-accent text-paper' => $i > 0,
                        ])>
                            <x-ui.icon name="arrow-right" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
