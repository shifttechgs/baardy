{{--
    Who we are -- the section under the hero.

    Laid out after trova-travel.framer.website's "Who We Are": three columns.
    Left, the eyebrow, a large heading, a short paragraph, one button (Our
    story, to /about) and, at the foot, the licence line in place of that
    site's avatars-and-rating. Centre, one tall photograph. Right, ruled rows
    of figures and a card whose three fanned photographs are switched by three
    icon tabs.

    Every figure is true and computed from config (marketing.trust), so
    nothing here needs an "illustrative" disclaimer. There is deliberately no
    star rating or customer count: none is sourced. The licence line links to
    the public RBZ register, where it can be checked.

    MOTION
      photograph   unmasks upward and settles from a slight zoom (.reveal-media)
      figures      count up from zero as they scroll into view
                   ([data-count-on-view], app.js)
      fanned card  the chosen photograph comes to the front (Alpine)
    Reduced motion: everything is simply there.
--}}
@php
    $trust = config('marketing.trust');
    $compliance = config('company.compliance');

    $figures = [
        'years' => now()->year - (int) $compliance['licensed_since'],
        'since' => (int) $compliance['licensed_since'],
        'licence' => (int) $compliance['licence'],
        'products' => count(config('marketing.products')),
        'branches' => count(config('company.branches')),
    ];

    $imageSources = $trust['image']['sources'];
    $srcset = collect($imageSources)
        ->map(fn (int $width, string $path): string => asset($path).' '.$width.'w')
        ->implode(', ');

    // The fanned card: three loans, each with the person it is for. Names are
    // looked up in marketing.products so the copy cannot drift from them.
    $fan = collect(['Salary-Based Loans' => 'briefcase', 'Agricultural Loans' => 'sprout', 'SME Bridging Finance' => 'store'])
        ->map(function (string $icon, string $name): ?array {
            $product = collect(config('marketing.products'))->firstWhere('name', $name);

            return $product ? [
                'name' => $name,
                'icon' => $icon,
                'src' => str_replace('-1600.webp', '-480.webp', $product['image']['src']),
                'alt' => $product['image']['alt'],
                'position' => $product['image']['position'],
            ] : null;
        })
        ->filter()
        ->values();
@endphp

<x-ui.section id="trust" :rule="false" tone="mist" aria-labelledby="trust-heading" class="relative overflow-hidden py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide class="relative">
        <div class="grid gap-12 lg:grid-cols-[1.15fr_1fr_1fr] lg:gap-x-8">

            {{-- Left: the statement --}}
            <div class="flex flex-col justify-between gap-12">
                <div>
                    <p class="flex items-center gap-1.5 text-body text-ink">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-3.5"><path d="m6 3.5 4.5 4.5L6 12.5" /></svg>
                        Who we are
                    </p>

                    <h2
                        id="trust-heading"
                        data-word-reveal
                        class="mt-5 text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink"
                    >{{ $trust['heading'] }}</h2>

                    <p class="mt-4 max-w-md text-body leading-[1.4] text-ink-soft">{{ $trust['body'] }}</p>

                    <x-ui.button :href="route('about')" arrow class="mt-8">
                        Our story
                    </x-ui.button>
                </div>

                <div data-reveal class="flex items-center gap-4">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-full bg-accent text-paper">
                        <x-ui.icon name="shield" class="size-6" />
                    </span>
                    <div class="flex flex-col gap-1">
                        <p class="text-body text-ink">
                            RBZ licence <span class="figure-nums">No. <span data-count-to="{{ $figures['licence'] }}" data-count-on-view>{{ $figures['licence'] }}</span></span>
                        </p>
                        <a
                            href="{{ $compliance['register_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group inline-flex items-center gap-1.5 text-small text-ink-soft underline decoration-line-strong underline-offset-4 transition-colors hover:text-accent"
                        >
                            {{ $trust['link'] }}
                            <x-ui.icon name="arrow-up-right" class="size-3.5 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </a>
                    </div>
                </div>
            </div>

            {{-- Centre: one tall photograph --}}
            <figure data-reveal class="reveal-media relative overflow-hidden rounded-xl bg-ink">
                <img
                    src="{{ asset(array_key_last($imageSources)) }}"
                    srcset="{{ $srcset }}"
                    sizes="(min-width: 1024px) 33vw, 100vw"
                    width="{{ max($imageSources) }}"
                    height="{{ max($imageSources) }}"
                    alt="{{ $trust['image']['alt'] }}"
                    loading="lazy"
                    decoding="async"
                    class="reveal-media-img aspect-[4/5] size-full object-cover object-[50%_35%] lg:aspect-auto lg:h-full lg:min-h-[34rem]"
                >
            </figure>

            {{-- Right: the figures, and the fanned loans --}}
            <div class="flex flex-col gap-8">
                <dl data-reveal class="flex flex-col">
                    @foreach ($trust['facts'] as $fact)
                        <div class="flex items-baseline justify-between gap-4 border-b border-ink/70 py-5 first:pt-0">
                            <dt class="text-lead text-ink-soft">{{ $fact['label'] }}</dt>
                            <dd class="figure-nums text-[1.75rem] leading-none tracking-[-0.02em] text-ink">
                                <span data-count-to="{{ $figures[$fact['key']] }}" data-count-on-view>{{ $figures[$fact['key']] }}</span>{{ $fact['suffix'] ?? '' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <div data-reveal x-data="{ active: 1 }" class="flex flex-col rounded-2xl bg-paper p-4">
                    <h3 class="px-2 pt-2 text-[1.5rem] leading-[1.3] font-medium text-ink">Every loan, a person behind it</h3>
                    <p class="mt-2 max-w-sm px-2 text-body leading-[1.4] text-ink-soft" aria-live="polite">
                        <template x-for="(loan, i) in {{ Js::from($fan->pluck('name')) }}" :key="i">
                            <span x-show="active === i" x-text="loan + ', matched to how its borrower earns.'"></span>
                        </template>
                        <noscript>Loans matched to how each borrower earns.</noscript>
                    </p>

                    {{-- The fan: whichever is chosen sits in front, the others
                         tilt away either side of it. --}}
                    <div class="relative mx-auto mt-4 h-44 w-full max-w-xs">
                        @foreach ($fan as $i => $loan)
                            <img
                                src="{{ asset($loan['src']) }}"
                                alt="{{ $loan['alt'] }}"
                                width="480"
                                height="360"
                                loading="lazy"
                                decoding="async"
                                class="absolute top-1/2 left-1/2 aspect-square w-32 -translate-y-1/2 rounded-xl border-[3px] border-paper object-cover shadow-[0_12px_24px_-12px_rgb(21_16_25/0.45)] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] sm:w-36"
                                style="object-position: {{ $loan['position'] }}"
                                x-bind:class="{
                                    'z-20 -translate-x-1/2 rotate-0 scale-105': active === {{ $i }},
                                    'z-10 -translate-x-[94%] -rotate-6': (({{ $i }} - active + 3) % 3) === 2,
                                    'z-10 -translate-x-[6%] rotate-6': (({{ $i }} - active + 3) % 3) === 1,
                                }"
                            >
                        @endforeach
                    </div>

                    <div role="tablist" aria-label="Choose a loan" class="mt-2 flex items-center justify-center gap-2">
                        @foreach ($fan as $i => $loan)
                            <button
                                type="button"
                                role="tab"
                                x-on:click="active = {{ $i }}"
                                x-bind:aria-selected="active === {{ $i }} ? 'true' : 'false'"
                                aria-label="{{ $loan['name'] }}"
                                class="inline-flex size-10 items-center justify-center rounded-full transition-colors duration-300"
                                x-bind:class="active === {{ $i }} ? 'bg-accent text-paper' : 'text-ink hover:bg-mist'"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-5">
                                    @switch ($loan['icon'])
                                        @case ('briefcase')
                                            <rect x="3" y="8" width="18" height="12" rx="2" /><path d="M9 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18" />
                                            @break
                                        @case ('sprout')
                                            <path d="M12 21v-9M12 12c0-4 3-6 7-6 0 4-3 6-7 6ZM12 15c0-3-2-5-6-5 0 3 2 5 6 5Z" />
                                            @break
                                        @default
                                            <path d="M4 9l1.5-5h13L20 9M4 9v11h16V9M4 9c0 1.7 1.3 3 2.7 3S9.3 10.7 9.3 9c0 1.7 1.3 3 2.7 3s2.7-1.3 2.7-3c0 1.7 1.3 3 2.7 3S20 10.7 20 9M10 20v-5h4v5" />
                                    @endswitch
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
