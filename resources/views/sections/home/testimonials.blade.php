{{--
    Testimonials -- two customer stories and the reviews, in one section.

    After trova-travel.framer.website's testimonials, and sitting with this
    site's "Why borrow from us" grid: a calm bento of one photograph card and
    quiet light cards. No quotation-mark flourishes, no colour-block card.

        left     the first story, on a photograph, its quote on frosted glass
        right    the second story on a light card, and under it the reviews,
                 a row looping to the left (a marquee)

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

    PHOTOGRAPHS. A story's optional `photo` is the photograph behind its quote:
    for a real story, the customer's own, with their consent. A story without
    one is a light card. Reviewers get initials on a tint, never a stock face
    beside a real name.
--}}
@php
    $pick = function (string $key, ?int $limit = null): array {
        $all = collect(config("marketing.{$key}"));
        $consented = $all->filter(fn (array $item): bool => ($item['consented'] ?? false) === true);
        $preview = $consented->isEmpty() && $all->isNotEmpty() && config('marketing.testimonials_preview');
        $items = ($preview ? $all : $consented)->values();

        return [$limit ? $items->take($limit) : $items, $preview];
    };

    [$stories, $storiesArePreview] = $pick('customer_stories', 2);
    [$reviews, $reviewsArePreview] = $pick('testimonials');

    // The loop animates exactly -50% of its width, so it holds two identical
    // halves; a half repeats the reviews until it is wider than a large
    // screen (at least six cards).
    $perHalf = max(6, $reviews->count());
    $half = $reviews->isEmpty()
        ? collect()
        : collect(range(0, $perHalf - 1))->map(fn (int $i): array => $reviews[$i % $reviews->count()]);

    // 50px a second: one half is ~23rem per card.
    $duration = round($perHalf * 23 * 16 / 50).'s';

    // A story with a photograph leads on the left; the rest follow on the right.
    $lead = $stories->first(fn (array $story): bool => filled($story['photo'] ?? null));
    $rest = $stories->reject(fn (array $story): bool => $story === $lead)->values();

    // Each story's button opens the enquiry form with its loan already chosen.
    $storyUrl = fn (array $story): string => route('contact', array_filter(['interest' => $story['loan'] ?? null])).'#contact';

    $initials = fn (string $name): string => collect(preg_split('/\s+/', trim($name)))
        ->take(2)
        ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');

    $pin = '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-3.5 shrink-0"><path d="M8 14s4.5-3.7 4.5-7.2a4.5 4.5 0 1 0-9 0C3.5 10.3 8 14 8 14Z" /><circle cx="8" cy="6.8" r="1.6" /></svg>';
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

                <p class="max-w-sm text-body leading-[1.4] text-ink-soft lg:col-span-4 lg:col-start-9 lg:ml-auto lg:text-right">
                    The part people mention most is not the money. It is being told plainly, and in writing.
                </p>
            </div>

            <div class="mt-10 grid gap-3 lg:mt-14 lg:grid-cols-12 lg:gap-4">

                {{-- The story on a photograph --}}
                @if ($lead)
                    <figure data-reveal class="group/photo relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden rounded-2xl bg-ink text-paper lg:col-span-5 lg:min-h-[34rem]">
                        <img
                            src="{{ asset($lead['photo']) }}"
                            alt=""
                            width="1600"
                            height="1067"
                            loading="lazy"
                            decoding="async"
                            class="absolute inset-0 -z-20 size-full object-cover transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/photo:scale-[1.04]"
                        >
                        <span aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-t from-ink/55 via-ink/10 to-transparent"></span>

                        <div class="m-3 flex flex-col gap-5 rounded-xl bg-ink/40 p-5 backdrop-blur-xl backdrop-saturate-150 sm:m-4 sm:p-7">
                            @isset ($lead['loan'])
                                <span class="w-fit rounded-full bg-paper/15 px-3 py-1 text-micro font-medium">{{ $lead['loan'] }}</span>
                            @endisset
                            <blockquote class="text-[length:clamp(1.25rem,1rem+0.9vw,1.625rem)] leading-[1.3] font-medium text-pretty">
                                <p>&ldquo;{{ $lead['story'] }}&rdquo;</p>
                            </blockquote>
                            <figcaption class="flex flex-col">
                                <span class="text-body">{{ $lead['name'] }}</span>
                                <span class="flex items-center gap-1.5 text-small text-paper/75">{!! $pin !!} {{ $lead['role'] }}</span>
                            </figcaption>
                            <x-ui.button :href="$storyUrl($lead)" variant="inverse" arrow class="sm:w-fit">
                                Enquire about this loan
                            </x-ui.button>
                        </div>
                    </figure>
                @endif

                {{-- The other story, then the reviews --}}
                <div @class(['flex min-w-0 flex-col gap-3 lg:gap-4', 'lg:col-span-7' => $lead, 'lg:col-span-12' => ! $lead])>
                    @foreach ($lead ? $rest : $stories as $story)
                        <figure data-reveal class="flex flex-col gap-8 rounded-2xl bg-paper p-6 sm:p-8">
                            <div class="flex flex-col gap-3">
                                @isset ($story['loan'])
                                    <span class="w-fit rounded-full bg-accent-tint px-3 py-1 text-micro font-medium text-accent">{{ $story['loan'] }}</span>
                                @endisset
                                @isset ($story['title'])
                                    <h3 class="text-[length:clamp(1.5rem,1.1rem+1.2vw,2rem)] leading-[1.2] font-medium tracking-[-0.02em] text-ink">{{ $story['title'] }}</h3>
                                @endisset
                                <blockquote class="max-w-xl text-body leading-[1.5] text-ink-soft">
                                    <p>{{ $story['story'] }}</p>
                                </blockquote>
                            </div>
                            <figcaption class="flex items-center gap-3">
                                <span aria-hidden="true" class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-accent-tint text-small font-medium text-accent">{{ $initials($story['name']) }}</span>
                                <span class="flex min-w-0 flex-col">
                                    <span class="text-body text-ink">{{ $story['name'] }}</span>
                                    <span class="flex items-center gap-1.5 text-small text-muted">{!! $pin !!} {{ $story['role'] }}</span>
                                </span>
                            </figcaption>
                            <x-ui.button :href="$storyUrl($story)" arrow class="sm:w-fit">
                                Enquire about this loan
                            </x-ui.button>
                        </figure>
                    @endforeach

        {{-- Reviews: a row looping to the left, under the story. Nothing to click or
             swipe; hovering pauses it (app.css, `.marquee`) and reduced motion stills
             it. Only the first card of each review is exposed to assistive technology. --}}
                @if ($reviews->isNotEmpty())
                    <div
                        data-reveal
                        class="marquee min-w-0 lg:flex-1
                               [mask-image:linear-gradient(to_right,transparent,black_6%,black_94%,transparent)]"
                    >
                        <p class="sr-only">Reviews</p>
                        <div class="marquee-track flex h-full w-max gap-3 lg:gap-4" style="--marquee-duration: {{ $duration }}">
                            @foreach ([false, true] as $isSecondHalf)
                                @foreach ($half as $cardIndex => $review)
                                    @php
                                        $isExposed = ! $isSecondHalf && $cardIndex < $reviews->count();
                                    @endphp

                                    <figure
                                        class="flex w-[17rem] shrink-0 flex-col justify-between gap-6 rounded-2xl bg-paper p-6 sm:w-[20rem]"
                                        @unless ($isExposed) aria-hidden="true" @endunless
                                    >
                                        <blockquote class="text-body leading-[1.45] text-pretty text-ink-soft">
                                            <p>&ldquo;{{ $review['quote'] }}&rdquo;</p>
                                        </blockquote>

                                        <figcaption class="flex items-center gap-3">
                                            <span aria-hidden="true" class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-accent-tint text-small font-medium text-accent">{{ $review['initials'] ?? $initials($review['name']) }}</span>
                                            <span class="flex min-w-0 flex-col">
                                                <span class="text-body text-ink">{{ $review['name'] }}</span>
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
            </div>
        </x-ui.container>

    </x-ui.section>
@endif
