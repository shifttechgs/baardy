{{--
    Who we are -- the section under the hero.

    Laid out after trova-travel.framer.website's "Who We Are", trimmed to two
    columns so the page does not stack a second large photograph under the
    hero's portrait. Left, a large heading, a short paragraph,
    one button (Our story, to /about) and, at the foot, the licence line in
    place of that site's avatars-and-rating. Right, ruled rows of figures, set
    large and stretched to the height of the statement.

    The loans themselves are not repeated here: the hero's cards and the
    "What we lend" section below already carry them.

    Every figure is true and computed from config (marketing.trust), so
    nothing here needs an "illustrative" disclaimer. There is deliberately no
    star rating or customer count: none is sourced. The licence line links to
    the public RBZ register, where it can be checked.

    MOTION
      figures      count up from zero as they scroll into view
                   ([data-count-on-view], app.js)
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
@endphp

<x-ui.section id="trust" :rule="false" aria-labelledby="trust-heading" class="relative overflow-hidden py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide class="relative">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-x-20 xl:gap-x-28">

            {{-- Left: the statement --}}
            <div class="flex flex-col justify-between gap-12">
                <div>
                    <h2
                        id="trust-heading"
                        data-word-reveal
                        class="text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink"
                    >{{ $trust['heading'] }}</h2>

                    <p class="mt-4 max-w-md text-lead leading-[1.4] text-ink-soft">{{ $trust['body'] }}</p>
                </div>

                {{-- The foot of the column: the next step, and the licence it rests on. --}}
                <div data-reveal class="flex flex-wrap items-center gap-x-10 gap-y-6">
                    <x-ui.button :href="route('about')" arrow>
                        Our story
                    </x-ui.button>

                    <div class="flex items-center gap-4">
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
            </div>

            {{-- Right: the figures, stretched to the height of the statement --}}
            <dl data-reveal class="flex flex-col">
                @foreach ($trust['facts'] as $fact)
                    <div class="flex flex-1 items-baseline justify-between gap-6 border-b border-ink/70 py-6 first:pt-0 last:border-b-0 last:pb-0">
                        <dt class="text-lead text-ink-soft">{{ $fact['label'] }}</dt>
                        <dd class="figure-nums text-[clamp(2.5rem,4.4vw,4rem)] leading-none tracking-[-0.03em] text-ink">
                            <span data-count-to="{{ $figures[$fact['key']] }}" data-count-on-view>{{ $figures[$fact['key']] }}</span>{{ $fact['suffix'] ?? '' }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </x-ui.container>
</x-ui.section>
