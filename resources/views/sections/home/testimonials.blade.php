{{--
    Testimonials -- the reviews and a customer story, in one section.

    After trova-travel.framer.website's testimonials: calm, editorial, a lot of
    type and a lot of room.

        top      the customer story, a wide brand-colour banner
        below    the reviews, a row looping to the left (a marquee) that
                 runs the full width of the section

    PUBLISHED ONLY WHEN REAL. Only entries with `consented` set to true are
    shown -- marketing.customer_stories for the stories, marketing.testimonials
    for the reviews -- and while neither has any, this partial renders nothing
    at all: no heading, no empty band. The placeholders in config are flagged
    false and can never reach the page. Publishing invented testimonials on a
    lending site is a misrepresentation.

    DEMO PREVIEW. With marketing.testimonials_preview on (and nothing
    consented in a part), that part shows its unconsented placeholders
    instead, each credited to a trade, not a person, and marked as a sample.
    Off by default.

    The marquee animates exactly -50% of its width, so it holds two identical
    halves; a half repeats the reviews until it is wider than a large screen.
    Hovering pauses it (app.css, `.marquee`) and reduced motion stills it.
    Only the first card of each review is exposed to assistive technology.
--}}
@php
    $pick = function (string $key, ?int $limit = null): array {
        $all = collect(config("marketing.{$key}"));
        $consented = $all->filter(fn (array $item): bool => ($item['consented'] ?? false) === true);
        $preview = $consented->isEmpty() && $all->isNotEmpty() && config('marketing.testimonials_preview');
        $items = ($preview ? $all : $consented)->values();

        return [$limit ? $items->take($limit) : $items, $preview];
    };

    [$stories] = $pick('customer_stories', 2);
    [$reviews] = $pick('testimonials');

    $initials = fn (array $review): string => $review['initials'] ?? collect(preg_split('/\s+/', trim($review['name'])))
        ->take(2)
        ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');

    $perHalf = max(6, $reviews->count());
    $half = $reviews->isEmpty()
        ? collect()
        : collect(range(0, $perHalf - 1))->map(fn (int $i): array => $reviews[$i % $reviews->count()]);

    // 50px a second: one half is ~26rem per card.
    $duration = round($perHalf * 26 * 16 / 50).'s';

    // Each story's button opens the enquiry form with its loan already chosen.
    $storyUrl = fn (array $story): string => route('contact', array_filter(['interest' => $story['loan'] ?? null])).'#contact';
@endphp

@if ($stories->isNotEmpty() || $reviews->isNotEmpty())
    <x-ui.section id="testimonials" :rule="false" aria-labelledby="testimonials-heading" tone="mist" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <div data-reveal class="grid gap-6 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-7">
                    <p class="flex items-center gap-1.5 text-body text-ink">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-3.5"><path d="m6 3.5 4.5 4.5L6 12.5" /></svg>
                        What borrowers say
                    </p>
                    <h2
                        id="testimonials-heading"
                        data-word-reveal
                        class="mt-5 text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink"
                    >
                        Knowing where you stand, and when
                    </h2>
                </div>

                <p class="max-w-sm text-lead leading-[1.4] text-ink-soft lg:col-span-4 lg:col-start-9 lg:ml-auto lg:text-right">
                    The part people mention most is not the money. It is being told plainly, and in writing.
                </p>
            </div>

            <div class="mt-10 flex flex-col gap-3 lg:mt-14 lg:gap-4">

                {{-- The story --}}
                @foreach ($stories as $story)
                    <x-ui.story-card data-reveal :story="$story" :url="$storyUrl($story)" wide />
                @endforeach

                {{-- The reviews, looping to the left under the story --}}
                @if ($reviews->isNotEmpty())
                    <div
                        data-reveal
                        class="marquee min-w-0 overflow-hidden
                               [mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)]"
                    >
                        <p class="sr-only">Reviews</p>
                        <div class="marquee-track flex w-max gap-3 lg:gap-4" style="--marquee-duration: {{ $duration }}">
                            @foreach ([false, true] as $isSecondHalf)
                                @foreach ($half as $cardIndex => $review)
                                    @php
                                        $isExposed = ! $isSecondHalf && $cardIndex < $reviews->count();
                                    @endphp

                                    <figure
                                        class="flex w-[19rem] shrink-0 flex-col justify-between gap-8 rounded-2xl bg-paper p-6 sm:w-[24rem] sm:p-8"
                                        @unless ($isExposed) aria-hidden="true" @endunless
                                    >
                                        <blockquote class="text-lead leading-[1.4] text-pretty text-ink">
                                            <p>&ldquo;{{ $review['quote'] }}&rdquo;</p>
                                        </blockquote>

                                        <figcaption class="flex items-center gap-3">
                                            <span aria-hidden="true" class="flex size-11 shrink-0 items-center justify-center rounded-full bg-accent text-small font-medium text-paper">{{ $initials($review) }}</span>
                                            <span class="flex min-w-0 flex-col">
                                                <span class="text-body font-medium text-ink">{{ $review['name'] }}</span>
                                                <span class="text-small text-muted">{{ $review['role'] }}</span>
                                            </span>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </x-ui.section>
@endif
