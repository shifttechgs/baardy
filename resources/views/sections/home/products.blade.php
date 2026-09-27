{{--
    Loan products.

    Presentation only -- no eligibility logic, no pricing, no calculation. The
    amount ranges and terms come from config/marketing.php and are rendered as
    a description list, which is what a term sheet is: paired terms and values.

    Restyled to sit with the hero and the standing section: every facility is
    a photograph of the person it is for, the open one sharp with its term
    sheet on glass, the closed ones a quiet purple band.
--}}
@php
    $products = config('marketing.products');
@endphp

<x-ui.section id="products" :rule="false" class="pt-10! pb-12! sm:pt-14! sm:pb-14! lg:pt-20! lg:pb-16!">
    <x-ui.container wide>

        {{-- Heading left; one line and the action right. --}}
        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-body font-medium text-accent">What we lend</p>
                <h2 data-word-reveal class="mt-4 text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink">
                    Five loans, matched to<br class="hidden sm:inline"> how you actually earn
                </h2>
            </div>

            <div class="flex flex-col items-start gap-5 lg:col-span-4 lg:col-start-9 lg:items-end lg:text-right">
                <p data-word-reveal class="max-w-md text-lead text-ink-soft">
                    A salary, a harvest and a shop&rsquo;s takings arrive differently. Each loan has
                    its own range, term and paperwork.
                </p>

                <x-ui.button :href="config('company.cta.primary.href')" pill class="group gap-3 pr-1.5 pl-5">
                    Find the right loan
                    <span class="inline-flex size-8 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5">
                        <x-ui.icon name="arrow-right" />
                    </span>
                </x-ui.button>
            </div>
        </div>

        {{--
            Desktop: a scroll-driven expanding-panel rail.

            One facility is open at a time; the others collapse to a rail
            showing the name set vertically over a dimmed, blurred photograph,
            so the section has a single point of focus.

            SCROLLING IS WHAT OPENS THEM. This element is the track -- five
            viewports tall -- and the rail inside is pinned, so travelling
            through the section moves the open facility from the first to the
            last. Raise the track height to slow the rail down.

            Everything that moves -- width, opacities, the photograph's blur,
            saturation and zoom -- is a pure function of scroll position,
            recomputed per frame by the `productRail` component in app.js. That
            is why there is no CSS transition on any of it: a transition would
            only add lag. Adjacent panels' weights always sum to 1, so the row
            stays exactly as wide as its track and nothing reflows mid-scroll.

            Clicking a facility -- or focusing it and pressing Enter or Space --
            scrolls to the point on the track where it is open. Hover does not
            open anything: with scroll driving the selection, a pointer crossing
            a pinned rail would fight it and flicker.

            Below lg the track is display:none and the photo cards beneath take
            over.
        --}}
        <div
            x-data="productRail({{ count($products) }})"
            x-on:scroll.window.passive="onScroll()"
            x-on:resize.window.passive="onScroll()"
            class="mt-14 hidden lg:mt-20 lg:block lg:h-[500vh]"
        >
            {{-- top-24 clears the sticky header, which is 4.5rem tall. --}}
            <div class="lg:sticky lg:top-24">

                {{-- data-reveal-cards, not data-reveal-stagger: the stagger rule
                     sets a transform on each child, which would be layered onto
                     panels whose width is already being driven per frame. --}}
                <div class="flex h-[min(40rem,calc(100svh-8rem))] gap-2" data-reveal-cards>
                    @foreach ($products as $i => $product)
                        <div
                            role="button"
                            tabindex="0"
                            x-bind:aria-expanded="weight({{ $i }}) > 0.5 ? 'true' : 'false'"
                            aria-label="{{ $product['name'] }}"
                            x-on:click="open({{ $i }})"
                            x-on:keydown.enter.prevent="open({{ $i }})"
                            x-on:keydown.space.prevent="open({{ $i }})"
                            x-bind:style="widthStyle({{ $i }})"
                            class="group/panel relative shrink-0 cursor-pointer overflow-hidden rounded-xl bg-ink
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                   focus-visible:outline-accent"
                        >
                            {{-- The photograph, set at the open panel's width and
                                 centred, so a narrowing panel crops the picture
                                 rather than squashing it. Blurred, desaturated and
                                 slightly zoomed while closed; sharp as it opens. --}}
                            <img
                                src="{{ asset($product['image']['src']) }}"
                                alt="{{ $product['image']['alt'] }}"
                                width="1600"
                                height="1067"
                                loading="lazy"
                                decoding="async"
                                class="pointer-events-none absolute inset-y-0 left-1/2 h-full w-[min(62rem,80vw)] max-w-none object-cover"
                                style="object-position: {{ $product['image']['position'] }}; transform: translateX(-50%)"
                                x-bind:style="photoStyle({{ $i }}, '{{ $product['image']['position'] }}')"
                            >

                            {{-- Scrims: a purple wash that makes the closed rails read
                                 as one quiet band, lifting as the panel opens; and a
                                 foot gradient that seats the term sheet. --}}
                            <div
                                aria-hidden="true"
                                class="pointer-events-none absolute inset-0 bg-accent-strong mix-blend-multiply"
                                x-bind:style="'opacity:' + (0.85 - weight({{ $i }}) * 0.85).toFixed(3)"
                            ></div>
                            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-linear-to-t from-ink/85 via-ink/20 to-ink/45"></div>

                            {{-- Closed: the name set vertically on the rail --}}
                            <div
                                x-show="railOpacity({{ $i }}) > 0"
                                x-bind:style="'opacity:' + railOpacity({{ $i }})"
                                class="absolute inset-0 flex flex-col items-center justify-between py-7"
                            >
                                <span class="figure-nums text-small text-paper/70">0{{ $i + 1 }}</span>

                                <span class="rotate-180 text-h3 font-normal tracking-[-0.015em] whitespace-nowrap text-paper [writing-mode:vertical-rl]">
                                    {{ $product['name'] }}
                                </span>

                                <span class="inline-flex size-9 items-center justify-center rounded-full text-paper ring-1 ring-paper/40 ring-inset transition-colors group-hover/panel:bg-paper group-hover/panel:text-ink">
                                    <x-ui.icon name="plus" />
                                </span>
                            </div>

                            {{-- Open: the name, then the term sheet on glass. --}}
                            <div
                                x-show="contentOpacity({{ $i }}) > 0"
                                x-cloak
                                x-bind:style="'opacity:' + contentOpacity({{ $i }})"
                                class="absolute inset-0 flex flex-col justify-between gap-8 p-8 xl:p-10"
                            >
                                <div class="flex flex-col gap-4">
                                    <p class="figure-nums text-small text-paper/75">
                                        0{{ $i + 1 }} <span class="text-paper/40">/ 0{{ count($products) }}</span>
                                    </p>

                                    <h3 class="max-w-lg text-[length:clamp(2rem,1.2rem+2vw,3.25rem)] leading-[1.05] font-normal tracking-[-0.03em] text-paper">
                                        {{ $product['name'] }}
                                    </h3>
                                </div>

                                <div class="flex flex-col gap-6 rounded-[0.625rem] bg-ink/45 p-6 ring-1 ring-paper/15 ring-inset backdrop-blur-xl xl:max-w-[44rem] xl:p-7">
                                    <p class="max-w-xl text-lead leading-relaxed text-paper/90">
                                        {{ $product['summary'] }}
                                    </p>

                                    <dl class="grid grid-cols-2 gap-x-10 gap-y-4 border-t border-paper/15 pt-5 xl:grid-cols-[auto_auto_1fr]">
                                        <div class="flex flex-col gap-1">
                                            <dt class="text-body text-paper/70">Amount</dt>
                                            <dd class="text-h3 font-normal text-paper">
                                                <x-ui.money :amount="$product['min']" />
                                                <span class="mx-0.5 text-paper/50">&ndash;</span>
                                                <x-ui.money :amount="$product['max']" />
                                            </dd>
                                        </div>

                                        <div class="flex flex-col gap-1">
                                            <dt class="text-body text-paper/70">Term</dt>
                                            <dd class="figure-nums text-h3 font-normal text-paper">
                                                {{ $product['term'] }}
                                            </dd>
                                        </div>

                                        <div class="col-span-2 flex flex-col gap-1 xl:col-span-1">
                                            <dt class="text-body text-paper/70">Best for</dt>
                                            <dd class="text-body text-paper">
                                                {{ $product['best_for'] }}
                                            </dd>
                                        </div>
                                    </dl>

                                    <a
                                        href="{{ config('company.cta.primary.href') }}"
                                        x-on:click.stop
                                        class="group inline-flex w-fit items-center gap-3 rounded-full bg-paper py-1.5 pr-1.5 pl-5 text-small font-medium text-ink transition-colors hover:bg-mist"
                                    >
                                        Apply for {{ Str::lower($product['name']) }}
                                        <span class="inline-flex size-8 items-center justify-center rounded-full bg-accent text-paper transition-transform duration-300 group-hover:translate-x-0.5">
                                            <x-ui.icon name="arrow-right" />
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Below lg: photo cards. --}}
        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:hidden" data-reveal data-reveal-stagger>
            @foreach ($products as $i => $product)
                <article class="relative flex min-h-[30rem] flex-col justify-end overflow-hidden rounded-xl bg-ink p-5 text-paper">
                    <img
                        src="{{ asset($product['image']['src']) }}"
                        alt="{{ $product['image']['alt'] }}"
                        width="1600"
                        height="1067"
                        loading="lazy"
                        decoding="async"
                        class="absolute inset-0 size-full object-cover"
                        style="object-position: {{ $product['image']['position'] }}"
                    >
                    <div aria-hidden="true" class="absolute inset-0 bg-linear-to-t from-ink/90 via-ink/30 to-transparent"></div>

                    <p class="figure-nums absolute top-5 left-5 text-small text-paper/80">0{{ $i + 1 }}</p>

                    <div class="relative flex flex-col gap-4 rounded-[0.625rem] bg-ink/45 p-5 ring-1 ring-paper/15 ring-inset backdrop-blur-xl">
                        <h3 class="text-h3 font-normal tracking-[-0.015em]">{{ $product['name'] }}</h3>

                        <dl class="grid grid-cols-2 gap-4 border-t border-paper/15 pt-4">
                            <div class="flex flex-col gap-0.5">
                                <dt class="text-micro text-paper/65">Amount</dt>
                                <dd class="text-small font-medium">
                                    <x-ui.money :amount="$product['min']" />
                                    <span class="text-paper/50">&ndash;</span>
                                    <x-ui.money :amount="$product['max']" />
                                </dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="text-micro text-paper/65">Term</dt>
                                <dd class="figure-nums text-small font-medium">{{ $product['term'] }}</dd>
                            </div>
                        </dl>

                        <p class="text-body text-paper/85">
                            <span class="text-paper">Best for</span>
                            {{ Str::lcfirst($product['best_for']) }}
                        </p>

                        <a
                            href="{{ config('company.cta.primary.href') }}"
                            class="group inline-flex w-fit items-center gap-3 rounded-full bg-paper py-1.5 pr-1.5 pl-4 text-small font-medium text-ink"
                        >
                            Apply for {{ Str::lower($product['name']) }}
                            <span class="inline-flex size-7 items-center justify-center rounded-full bg-accent text-paper">
                                <x-ui.icon name="arrow-right" />
                            </span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- The "illustrative placeholders" disclosure was removed at the
             client's request (2026-09-26). The ranges and terms in
             config/marketing.php are still NOT client-confirmed -- replace them
             with the client's real figures before launch. --}}
    </x-ui.container>
</x-ui.section>
