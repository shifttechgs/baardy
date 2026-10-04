{{--
    Why borrow from us -- a bento grid.

    Every reason is on screen at once: nothing to swipe or click through to
    find out why to choose us. Heading left and one line and an action right,
    as on the other sections, then four tiles:

      schedule  the large tile. Pick weekly, fortnightly or monthly and the
                payment days light up on a sample month -- the benefit shown
                working, not described. An illustration with no amounts; the
                real dates are agreed before the borrower accepts. Ends on an
                action, so trying it leads straight to applying.
      advisory  the wide tile: what the free advisory covers, as chips
      privacy   the small stat tile
      decision  the brand-colour tile, which asks for the application

    PHOTOGRAPHS. Untinted, never washed with colour. The schedule and privacy
    tiles sit on a photograph (a farmer at planting, a portrait) with their
    text on the site's frosted panel, as on the hero, the loans rail and the
    FAQ card. The advisory tile is split -- text left, the shopkeeper right --
    because in a wide tile her face would sit under any overlay. The decision tile stays solid
    purple: the colour anchor, and the ask. Self-hosted Unsplash stock,
    atmosphere not customers:
      schedules   unsplash.com/photos/QYcSeY7vuZM
      shopkeeper  unsplash.com/photos/EOkN2pRjFsg
      privacy     unsplash.com/photos/VPvYUK2Iibo

    Claims come from marketing.benefits and hero.promises by index (via
    why_us.tiles), so their wording lives in one place.
--}}
@php
    $whyUs = config('marketing.why_us');
    $tiles = $whyUs['tiles'];
    $benefits = config('marketing.benefits');

    $schedule = $tiles['schedule'];
    $scheduleBenefit = $benefits[$schedule['benefit']];
    $advisory = $tiles['advisory'];
    $advisoryBenefit = $benefits[$advisory['benefit']];
    $privacyBenefit = $benefits[$tiles['privacy']['benefit']];
    $decision = $tiles['decision'];
    $decisionPromise = config('marketing.hero.promises')[$decision['promise']];
@endphp

<x-ui.section id="why-us" :rule="false" aria-labelledby="why-us-heading" class="pt-10! pb-12! sm:pt-14! sm:pb-14! lg:pt-20! lg:pb-20!">
    <x-ui.container wide>
        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-body font-medium text-accent">Why borrow from us</p>
                <h2 data-word-reveal
                    id="why-us-heading"
                    class="mt-4 max-w-[20ch] text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink"
                >
                    {{ $whyUs['heading'] }}
                </h2>
            </div>

            <div class="flex flex-col items-start gap-5 lg:col-span-4 lg:col-start-9 lg:items-end lg:text-right">
                <p data-word-reveal class="max-w-sm text-lead text-ink-soft">{{ $whyUs['lead'] }}</p>

                <x-ui.button :href="$whyUs['action']['href']" pill class="group gap-3 pr-1.5 pl-5">
                    {{ $whyUs['action']['label'] }}
                    <span class="inline-flex size-8 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5">
                        <x-ui.icon name="arrow-right" />
                    </span>
                </x-ui.button>
            </div>
        </div>

        <div data-reveal-cards class="mt-12 grid gap-4 lg:mt-16 lg:grid-cols-12 lg:grid-rows-[auto_auto] lg:gap-5">

            {{-- Schedule: the benefit, working, over a farmer at planting time. --}}
            <article
                x-data="{ options: {{ Js::from($schedule['options']) }}, chosen: 0 }"
                class="group relative isolate flex flex-col justify-between gap-6 overflow-hidden rounded-xl bg-ink p-3 sm:p-4 lg:col-span-5 lg:row-span-2"
            >
                <img
                    src="{{ asset('images/why-us/schedules-1400.webp') }}"
                    alt=""
                    width="1400"
                    height="932"
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 -z-10 size-full object-cover object-[30%_50%] transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]"
                >

                <div class="flex flex-col gap-5 rounded-lg bg-paper p-5 shadow-[0_24px_48px_-24px_rgb(21_16_25/0.55)] sm:p-6">
                    <fieldset>
                        <legend class="text-small font-medium text-ink">Choose how you repay</legend>

                        <div class="mt-3 grid grid-cols-3 gap-1 rounded-full bg-mist p-1">
                            @foreach ($schedule['options'] as $index => $option)
                                <label
                                    class="flex cursor-pointer items-center justify-center rounded-full px-2 py-2 text-small font-medium transition-colors duration-200
                                           has-[:focus-visible]:outline has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                    x-bind:class="chosen === {{ $index }} ? 'bg-accent text-paper shadow-sm' : 'text-ink-soft hover:text-ink'"
                                >
                                    <input type="radio" name="why-us-schedule" value="{{ $index }}" class="sr-only" x-model.number="chosen" @checked($loop->first)>
                                    {{ $option['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- A sample four-week month; paydays fill in. --}}
                    <div aria-hidden="true">
                        <div class="grid grid-cols-7 gap-1 text-center text-micro text-muted">
                            @foreach (['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $weekday)
                                <span>{{ $weekday }}</span>
                            @endforeach
                        </div>

                        <div class="mt-2 grid grid-cols-7 gap-1">
                            @for ($day = 1; $day <= 28; $day++)
                                <span
                                    class="figure-nums flex h-8 items-center justify-center rounded-md text-small transition-[background-color,color,transform] duration-300"
                                    x-bind:class="options[chosen].days.includes({{ $day }}) ? 'bg-accent font-medium text-paper' : 'bg-mist text-ink-soft'"
                                >{{ $day }}</span>
                            @endfor
                        </div>
                    </div>

                    <p class="text-small text-ink-soft" aria-live="polite">
                        <span class="figure-nums font-medium text-ink" x-text="options[chosen].days.length">{{ count($schedule['options'][0]['days']) }}</span>
                        <span x-text="options[chosen].days.length === 1 ? 'payment' : 'payments'">payments</span> a month,
                        on dates agreed before you accept.
                    </p>

                    <x-ui.button :href="$whyUs['action']['href']" pill class="group/button w-fit gap-3 pr-1.5 pl-5">
                        Ask about this schedule
                        <span class="inline-flex size-8 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover/button:translate-x-0.5">
                            <x-ui.icon name="arrow-right" />
                        </span>
                    </x-ui.button>
                </div>

                <div class="flex flex-col gap-2 rounded-[0.625rem] bg-ink/45 p-5 text-paper ring-1 ring-paper/15 ring-inset backdrop-blur-xl">
                    <h3 data-word-reveal class="text-[length:clamp(1.25rem,1rem+0.6vw,1.5rem)] leading-snug font-normal tracking-[-0.015em]">
                        {{ $scheduleBenefit['title'] }}
                    </h3>
                    <p class="text-body text-paper/80">
                        {{ $scheduleBenefit['stat'] }} {{ $scheduleBenefit['stat_label'] }}. {{ $scheduleBenefit['body'] }}
                    </p>
                </div>
            </article>

            {{-- Advisory: split, so the photograph's subject is never under the text. --}}
            <article class="group grid overflow-hidden rounded-xl bg-mist sm:grid-cols-[minmax(0,1fr)_minmax(0,0.85fr)] lg:col-span-7">
                <div class="flex flex-col justify-between gap-8 p-6 sm:p-8">
                    <div class="flex flex-col gap-4">
                        <p class="flex items-baseline gap-2">
                            <span class="figure-nums text-[length:clamp(2.5rem,2rem+1.4vw,3.25rem)] leading-none tracking-[-0.04em] text-accent">{{ $advisoryBenefit['stat'] }}</span>
                            <span class="text-small text-muted">{{ $advisoryBenefit['stat_label'] }}</span>
                        </p>
                        <div class="flex flex-col gap-2">
                            <h3 data-word-reveal class="text-[length:clamp(1.25rem,1rem+0.6vw,1.5rem)] leading-snug font-normal tracking-[-0.015em] text-ink">
                                {{ $advisoryBenefit['title'] }}
                            </h3>
                            <p class="text-body text-ink-soft">{{ $advisoryBenefit['body'] }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($advisory['topics'] as $topic)
                                <span class="inline-flex items-center gap-2 rounded-full bg-paper py-1.5 pr-3.5 pl-2 text-small text-ink">
                                    <span class="flex size-5 items-center justify-center rounded-full bg-accent text-paper">
                                        <x-ui.icon name="check" class="size-3" />
                                    </span>
                                    {{ $topic }}
                                </span>
                            @endforeach
                        </div>

                        <a
                            href="{{ $advisory['link']['href'] }}"
                            class="group/link inline-flex w-fit items-center gap-2 text-small font-medium text-accent"
                        >
                            {{ $advisory['link']['label'] }}
                            <x-ui.icon name="arrow-right" class="transition-transform duration-200 group-hover/link:translate-x-1" />
                        </a>
                    </div>
                </div>

                <div class="relative m-2 min-h-64 overflow-hidden rounded-lg sm:ml-0">
                    <img
                        src="{{ asset('images/why-us/shopkeeper-720.webp') }}"
                        srcset="{{ asset('images/why-us/shopkeeper-720.webp') }} 720w, {{ asset('images/why-us/shopkeeper-1440.webp') }} 1440w"
                        sizes="(min-width: 64rem) 26vw, (min-width: 40rem) 45vw, 100vw"
                        alt=""
                        width="720"
                        height="600"
                        loading="lazy"
                        decoding="async"
                        class="absolute inset-0 size-full object-cover object-[50%_40%] transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.05]"
                    >
                </div>
            </article>

            {{-- Privacy, over a portrait. --}}
            <article class="group relative isolate flex min-h-[24rem] flex-col justify-end overflow-hidden rounded-xl bg-ink p-3 sm:p-4 lg:col-span-3">
                <img
                    src="{{ asset('images/why-us/privacy-1400.webp') }}"
                    alt=""
                    width="1400"
                    height="2099"
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 -z-10 size-full object-cover object-[50%_15%] transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]"
                >

                <div class="flex flex-col gap-1.5 rounded-[0.625rem] bg-ink/45 p-5 text-paper ring-1 ring-paper/15 ring-inset backdrop-blur-xl">
                    <div class="flex items-center gap-3">
                        <p class="figure-nums text-[length:clamp(2.25rem,1.8rem+1.2vw,3rem)] leading-none tracking-[-0.045em]">{{ $privacyBenefit['stat'] }}</p>
                        <p class="text-small leading-snug text-paper/75">{{ $privacyBenefit['stat_label'] }}</p>
                    </div>
                    <h3 data-word-reveal class="mt-2 text-body font-medium">{{ $privacyBenefit['title'] }}</h3>
                </div>
            </article>

            {{-- Decision: the tile that asks. --}}
            <a
                href="{{ $whyUs['action']['href'] }}"
                class="group relative isolate flex min-h-[24rem] flex-col justify-between gap-8 overflow-hidden rounded-xl bg-accent p-6 text-paper transition-colors hover:bg-accent-strong sm:p-8 lg:col-span-4"
            >
                {{-- The figure, set large and cropped by the tile's edge. --}}
                <span aria-hidden="true" class="figure-nums pointer-events-none absolute -right-4 -bottom-16 -z-10 text-[16rem] leading-none tracking-[-0.06em] text-paper/[0.07] transition-transform duration-700 group-hover:-translate-y-2">
                    {{ $decision['stat'] }}
                </span>

                <div class="flex items-start justify-between gap-4">
                    <p class="flex items-baseline gap-2">
                        <span class="figure-nums text-[length:clamp(3rem,2.2rem+2.2vw,4.25rem)] leading-none tracking-[-0.045em]">{{ $decision['stat'] }}</span>
                        <span class="text-lead text-paper/80">{{ $decision['stat_label'] }}</span>
                    </p>
                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-paper text-accent transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5">
                        <x-ui.icon name="arrow-up-right" />
                    </span>
                </div>

                <div class="flex flex-col gap-3">
                    <p data-word-reveal class="max-w-xs text-lead text-paper">{{ $decisionPromise }}.</p>
                    <p class="text-small font-medium text-paper/75 underline decoration-paper/40 underline-offset-4 group-hover:decoration-paper">
                        {{ $whyUs['action']['label'] }}
                    </p>
                </div>
            </a>
        </div>
    </x-ui.container>
</x-ui.section>
