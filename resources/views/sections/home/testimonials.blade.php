{{--
    Testimonials -- what borrowers say.

    PUBLISHED ONLY WHEN REAL. Only entries in marketing.testimonials with
    `consented` set to true are shown, and while there are none this partial
    renders nothing at all -- no heading, no empty band. The placeholder
    quotes in config are flagged false and can never reach the page.
    Publishing invented testimonials on a lending site is a misrepresentation.

    DEMO PREVIEW. With marketing.testimonials_preview on, the unconsented
    placeholders are shown instead, each credited to a trade and marked
    "Sample quote" rather than to a person, for a client demo or an awards
    submission. Off by default.

    LAYOUT. After Framer's "TestimonialsLoopCarousel": one row of review
    cards looping to the left, on the same mist band as "How it works" so
    the page keeps one light palette. Nothing to click or
    swipe -- the quotes come to the reader. Hovering pauses it (app.css,
    `.marquee`), and the site-wide reduced-motion rule stills it. The Framer
    original's second row and decorative glow shape are left out.

    LOOPING. The row animates exactly -50% of its own width, so it must hold
    two identical halves. A half repeats the quotes until it is wider than a
    large screen (at least six cards), or a short list would leave a gap on
    the right before the loop point. Only the first card of each quote is
    exposed to assistive technology; every repeat is aria-hidden.

    Avatars are initials on a tint rather than stock photographs: a stock
    face beside a real name would be its own misrepresentation.
--}}
@php
    $all = collect(config('marketing.testimonials'));
    $consented = $all->filter(fn (array $testimonial): bool => ($testimonial['consented'] ?? false) === true);

    $isPreview = $consented->isEmpty() && config('marketing.testimonials_preview');
    $testimonials = ($isPreview ? $all : $consented)->values();

    $perHalf = max(6, $testimonials->count());
    $half = $testimonials->isEmpty()
        ? collect()
        : collect(range(0, $perHalf - 1))->map(fn (int $i): array => $testimonials[$i % $testimonials->count()]);

    // 50px a second, as in the Framer original: one half is ~30rem per card.
    $duration = round($perHalf * 30 * 16 / 50).'s';
@endphp

@if ($testimonials->isNotEmpty())
    <x-ui.section id="testimonials" :rule="false" aria-labelledby="testimonials-heading" tone="mist" class="overflow-hidden py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-7">
                    <p class="text-body font-medium text-accent">What borrowers say</p>
                    <h2 data-word-reveal
                        id="testimonials-heading"
                        class="mt-4 text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink"
                    >
                        Knowing where you stand,<br class="hidden sm:inline"> and when
                    </h2>
                </div>

                <div class="flex flex-col items-start gap-5 lg:col-span-4 lg:col-start-9 lg:items-end lg:text-right">
                    <p data-word-reveal class="max-w-sm text-lead text-ink-soft">
                        The part people mention most is not the money. It is being told plainly, and in writing.
                    </p>

                    <x-ui.button :href="config('company.cta.primary.href')" pill class="group gap-3 pr-1.5 pl-5">
                        {{ config('company.cta.primary.label') }}
                        <span class="inline-flex size-8 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5">
                            <x-ui.icon name="arrow-right" />
                        </span>
                    </x-ui.button>
                </div>
            </div>
        </x-ui.container>

        <div
            data-reveal
            class="marquee mt-12 lg:mt-16
                   [mask-image:linear-gradient(to_right,transparent,black_8%,black_92%,transparent)]"
        >
            <div class="marquee-track flex w-max gap-6" style="--marquee-duration: {{ $duration }}">
                @foreach ([false, true] as $isSecondHalf)
                    @foreach ($half as $cardIndex => $testimonial)
                        @php
                            $isExposed = ! $isSecondHalf && $cardIndex < $testimonials->count();
                        @endphp

                        <figure
                            class="flex w-[20rem] shrink-0 flex-col gap-5 rounded-xl bg-paper p-5 sm:w-[29rem]"
                            @unless ($isExposed) aria-hidden="true" @endunless
                        >
                            <figcaption class="flex items-center gap-3">
                                <span aria-hidden="true" class="flex size-14 shrink-0 items-center justify-center rounded-full bg-accent-tint text-body font-medium text-accent">
                                    {{ $testimonial['initials'] }}
                                </span>
                                <span class="flex min-w-0 flex-col">
                                    <span class="text-body font-medium text-ink">{{ $testimonial['name'] }}</span>
                                    <span class="text-small text-muted">{{ $testimonial['role'] }}</span>
                                </span>
                            </figcaption>

                            <span aria-hidden="true" class="h-px w-full bg-line"></span>

                            <blockquote data-word-reveal class="text-body text-pretty text-ink-soft sm:text-lead">
                                <p>&ldquo;{{ $testimonial['quote'] }}&rdquo;</p>
                            </blockquote>
                        </figure>
                    @endforeach
                @endforeach
            </div>
        </div>
    </x-ui.section>
@endif
