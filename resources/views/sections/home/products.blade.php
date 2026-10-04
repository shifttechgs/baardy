{{--
    Loan products.

    Presentation only -- no eligibility logic, no pricing, no calculation. The
    amount ranges and terms come from config/marketing.php and are rendered as
    a description list, which is what a term sheet is: paired terms and values.

    Every loan is a two-column card: its details (what it is, amount, term, who
    it is for, the ask) on the left, and a full-resolution photograph of the
    person it is for on the right (stacked on a phone). The loans stack as you scroll DOWN the page: each card slides up over
    the last. This replaced the earlier horizontal, scroll-pinned panel rail,
    keeping its scroll-driven reveal but on the vertical axis (client request).
--}}
@php
    $products = config('marketing.products');

    // Live promotions tagged on a loan (admin panel), keyed by loan name.
    $promotions = \App\Models\Promotion::byProduct();
@endphp

<x-ui.section id="products" :rule="false" class="pt-10! pb-12! sm:pt-14! sm:pb-14! lg:pt-20! lg:pb-16!">
    <x-ui.container wide>

        {{-- Heading left; one line and the action right. --}}
        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-body font-medium text-accent">What we lend</p>
                <h2 data-word-reveal class="mt-4 text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink">
                    {{ ucfirst(\Illuminate\Support\Number::spell(count($products))) }} loans, matched to<br class="hidden sm:inline"> how you actually earn
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
            The loans, as a stack of cards that arrive as you scroll down.

            Each card is sticky: scrolling carries the next loan up over the
            one before it, which settles back (a little smaller, a little
            darker) as it is covered. Vertical at every size. Pure CSS does
            the stacking; `productStack` (app.js) only drives the settling,
            and is skipped for reduced motion, when the cards simply stack.
        --}}
        <div x-data="productStack" class="mt-12 lg:mt-16">
            @foreach ($products as $i => $product)
                <article
                    data-stack-card
                    class="relative mb-5 origin-top overflow-hidden rounded-2xl bg-mist will-change-transform sm:mb-8 lg:sticky lg:top-(--stack-top) lg:h-[min(34rem,calc(100svh-8.5rem))]"
                    style="--stack-top: calc(5.5rem + {{ $i }} * 0.9rem);"
                >
                    <div class="grid h-full gap-3 p-3 sm:p-4 lg:grid-cols-2 lg:gap-4">

                        {{-- Details --}}
                        <div class="order-2 flex flex-col justify-between gap-8 px-3 pb-3 sm:px-4 lg:order-1 lg:p-8">
                            <div class="flex flex-col gap-4">
                                <p class="figure-nums text-small text-muted">0{{ $i + 1 }} <span class="text-muted/60">/ 0{{ count($products) }}</span></p>

                                @isset ($promotions[$product['name']])
                                    <a href="{{ route('promotions.show', $promotions[$product['name']]) }}" class="w-fit rounded-full bg-highlight px-3 py-1 text-micro font-medium text-paper">
                                        Limited offer &middot; {{ $promotions[$product['name']]->summary }}
                                    </a>
                                @endisset

                                <h3 class="text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.1] font-normal tracking-[-0.03em] text-ink">{{ $product['name'] }}</h3>
                                <p class="max-w-md text-body leading-[1.4] text-ink-soft">{{ $product['summary'] }}</p>
                            </div>

                            <div class="flex flex-col gap-6">
                                <dl class="grid grid-cols-2 gap-4 border-t border-ink/15 pt-5">
                                    <div class="flex flex-col gap-0.5">
                                        <dt class="text-micro text-muted">Amount</dt>
                                        <dd class="text-body font-medium text-ink">
                                            <x-ui.money :amount="$product['min']" />
                                            <span class="text-muted">&ndash;</span>
                                            <x-ui.money :amount="$product['max']" />
                                        </dd>
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <dt class="text-micro text-muted">Term</dt>
                                        <dd class="figure-nums text-body font-medium text-ink">{{ $product['term'] }}</dd>
                                    </div>
                                    <div class="col-span-2 flex flex-col gap-0.5">
                                        <dt class="text-micro text-muted">Best for</dt>
                                        <dd class="text-body text-ink-soft">{{ $product['best_for'] }}</dd>
                                    </div>
                                </dl>

                                <x-ui.button :href="config('company.cta.primary.href')" arrow class="sm:w-fit sm:self-start">
                                    <span class="sm:hidden">Enquire now</span>
                                    <span class="hidden sm:inline">Enquire about {{ Str::lower($product['name']) }}</span>
                                </x-ui.button>
                            </div>
                        </div>

                        {{-- The photograph, at full resolution --}}
                        <div class="relative order-1 aspect-[16/10] overflow-hidden rounded-xl bg-line lg:order-2 lg:aspect-auto">
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
                        </div>
                    </div>

                    {{-- Dims as the next card covers this one. --}}
                    <div aria-hidden="true" data-stack-shade class="pointer-events-none absolute inset-0 bg-ink opacity-0"></div>
                </article>
            @endforeach
        </div>

        {{-- The "illustrative placeholders" disclosure was removed at the
             client's request (2026-09-26). The ranges and terms in
             config/marketing.php are still NOT client-confirmed -- replace them
             with the client's real figures before launch. --}}
    </x-ui.container>
</x-ui.section>
