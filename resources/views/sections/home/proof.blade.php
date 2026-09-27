{{--
    Proof — the interim occupant of the social-proof slot.

    ---------------------------------------------------------------------------
    WHY THIS EXISTS.

    This slot held three testimonials. They were placeholder quotes, and the
    page had to say so: every card read "[Customer name]" above a disclosure
    that these were not real customers. Deep in the page that was survivable.
    Directly after the products rail it is not -- it tells the visitors who are
    closest to applying that no borrower will put their name to this lender.

    So until consented quotes exist, the slot carries things that are true and
    that the reader can check for themselves: the licence, the years on the
    register, the two offices. In a market with 332 licensed credit-only
    lenders and a great many unlicensed ones, a licence number someone can look
    up is stronger proof than a quote they cannot.

    Every figure is read from config, so none can drift out of step with the
    footer or the hero.

    TO GO BACK TO TESTIMONIALS: swap this include for `testimonials` in
    pages/home.blade.php. That partial and its copy are untouched.
    ---------------------------------------------------------------------------
--}}
@php
    $compliance = config('company.compliance');

    $figures = [
        'licence' => $compliance['licence'],
        'years' => now()->year - (int) $compliance['licensed_since'],
        'branches' => count(config('company.branches')),
    ];
@endphp

<x-ui.section id="proof">
    <x-ui.container>
        <x-ui.section-header
            label="Standing"
            heading="Three things you can check before you borrow"
        >
            Anyone can say they are trustworthy. These are the ones you can confirm yourself,
            on a public register or by walking through a door.
        </x-ui.section-header>

        <div
            data-reveal
            data-reveal-stagger
            class="mt-14 grid gap-px border-y border-line bg-line lg:mt-20 lg:grid-cols-3"
        >
            @foreach (config('marketing.proof') as $item)
                <div class="flex flex-col gap-6 bg-paper p-8 lg:p-12">
                    <p class="figure-nums text-display leading-none font-medium tracking-[-0.04em] text-accent">
                        {{ $figures[$item['key']] }}
                    </p>

                    <div class="flex flex-col gap-3">
                        <h3 class="text-h3 font-medium tracking-[-0.015em] text-ink">
                            {{ $item['title'] }}
                        </h3>

                        <p class="text-body text-muted">
                            {{ $item['body'] }}
                        </p>
                    </div>

                    @if ($item['check'])
                        {{-- The verification link is the point of the section: an
                             invitation to go and check rather than a claim to be
                             taken on faith. External, so it opens in a new tab. --}}
                        <a
                            href="{{ $compliance['register_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group mt-auto inline-flex w-fit items-center gap-2 rounded-xs border-b
                                   border-accent/30 pb-0.5 text-small font-medium text-accent
                                   transition-colors hover:border-accent"
                        >
                            {{ $item['check'] }}
                            <x-ui.icon
                                name="arrow-right"
                                class="transition-transform duration-200 group-hover:translate-x-0.5"
                            />
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- The highest-intent moment on the page: straight after proof. --}}
        <div class="mt-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <x-ui.button :href="config('company.cta.primary.href')" size="lg" class="group">
                {{ config('company.cta.primary.label') }}
                <x-ui.icon
                    name="arrow-right"
                    class="transition-transform duration-200 group-hover:translate-x-0.5"
                />
            </x-ui.button>

            <p class="max-w-md text-small text-muted">
                Customer testimonials will appear here once borrowers have given consent to
                be quoted by name.
            </p>
        </div>
    </x-ui.container>
</x-ui.section>
