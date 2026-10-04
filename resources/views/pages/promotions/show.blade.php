{{--
    /promotions/{slug} -- one promotion, with its full terms.

    This is the page every placement links to and the link shared on
    WhatsApp and social posts, so it carries everything: what the offer is,
    when it runs and how long is left, the terms in full, and the one action.
    The action goes to the enquiry form with the loan preselected and the
    tracking code carried through (Promotion::enquiryUrl()).

    THE ACTION appears three times, each with the same code: under the
    headline, in a card beside the body that stays in view on large screens,
    and as the closing band. Beside the button there is always a way to ask a
    person: WhatsApp, opened with a message naming the offer, and the head
    office line. (No branch addresses or numbers, per the client.)

    After the end date the page stays up and says the promotion has ended,
    with no call to action for the offer, so an old shared link never lands on
    a 404 or on an offer that no longer stands. It points to what is running
    now instead.

    Body and terms are plain text from the admin panel, escaped, with blank
    lines turned into paragraphs.
--}}
@extends('layouts.marketing')

@php
    $paragraphs = fn (string $text): array => array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $text))));
    $ended = $promotion->hasEnded();

    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));

    $phone = config('company.contact.phone');
    $phoneHref = 'tel:'.preg_replace('/\s+/', '', $phone);
@endphp

@section('title', $promotion->title.' | '.config('company.name'))

@section('description', $promotion->summary)

@section('content')
    <x-ui.section :rule="false" class="pt-10 sm:pt-14 lg:pt-16">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="rise text-small text-muted">
                <a href="{{ route('home') }}" class="transition-colors hover:text-accent">Home</a>
                <span aria-hidden="true" class="mx-2">/</span>
                <a href="{{ route('promotions.index') }}" class="transition-colors hover:text-accent">Promotions</a>
            </nav>

            <div class="mt-10 grid gap-12 lg:mt-14 lg:grid-cols-12 lg:items-center lg:gap-x-16">
                <header class="flex flex-col gap-5 lg:col-span-7">
                    <p class="rise flex flex-wrap items-center gap-2 text-small [animation-delay:60ms]">
                        @if ($ended)
                            <span class="rounded-full bg-mist px-3 py-1 font-medium text-muted">This promotion has ended</span>
                        @else
                            <span class="rounded-full bg-highlight-tint px-3 py-1 font-medium text-highlight">Limited offer</span>
                            <span class="rounded-full bg-accent-tint px-3 py-1 font-medium text-accent">{{ $promotion->timeLeftLabel() }}</span>
                        @endif
                    </p>

                    <h1 class="rise text-[length:clamp(2.25rem,1.4rem+3vw,3.75rem)] leading-[1.05] font-light tracking-[-0.035em] text-balance text-ink [animation-delay:120ms]">
                        {{ $promotion->title }}
                    </h1>

                    <p class="rise max-w-2xl text-lead text-ink-soft [animation-delay:180ms]">{{ $promotion->summary }}</p>

                    {{-- The facts, as a ruled row. --}}
                    <dl class="rise figure-nums grid max-w-xl grid-cols-2 gap-x-8 gap-y-4 border-y border-line py-5 text-small [animation-delay:220ms] sm:grid-cols-3">
                        <div class="flex flex-col gap-1">
                            <dt class="text-muted">Loan</dt>
                            <dd class="font-medium text-ink">{{ $promotion->product ?? 'All loans' }}</dd>
                        </div>
                        <div class="flex flex-col gap-1">
                            <dt class="text-muted">{{ $ended ? 'Ended' : 'Ends' }}</dt>
                            <dd class="font-medium text-ink"><time datetime="{{ $promotion->ends_at->toDateString() }}">{{ $promotion->ends_at->format('j F Y') }}</time></dd>
                        </div>
                        <div class="col-span-2 flex flex-col gap-1 sm:col-span-1">
                            <dt class="text-muted">Started</dt>
                            <dd class="font-medium text-ink"><time datetime="{{ $promotion->starts_at->toDateString() }}">{{ $promotion->starts_at->format('j F Y') }}</time></dd>
                        </div>
                    </dl>

                    @unless ($ended)
                        <div class="rise mt-1 flex flex-wrap items-center gap-x-5 gap-y-4 [animation-delay:260ms]">
                            <x-ui.button :href="$promotion->enquiryUrl()" arrow size="lg">{{ $promotion->cta_label }}</x-ui.button>

                            @if ($whatsapp)
                                <x-ui.whatsapp-cta :number="$whatsapp['tel']" :text="$promotion->whatsappMessage()" label="Ask on WhatsApp" tone="mist" />
                            @endif

                            <a href="#terms" class="text-small text-ink-soft underline decoration-line-strong underline-offset-4 hover:decoration-ink">Read the terms</a>
                        </div>
                    @else
                        <div class="rise mt-1 flex flex-wrap items-center gap-4 [animation-delay:260ms]">
                            <x-ui.button :href="route('promotions.index')" arrow size="lg">See what is on now</x-ui.button>
                        </div>
                    @endunless
                </header>

                @if ($promotion->imageUrl())
                    <div class="rise overflow-hidden rounded-xl bg-mist [animation-delay:200ms] lg:col-span-5">
                        <img src="{{ $promotion->imageUrl() }}" alt="{{ $promotion->imageAlt() }}" width="1200" height="900" fetchpriority="high"
                             @class(['aspect-[4/3] w-full object-cover', 'grayscale' => $ended])>
                    </div>
                @endif
            </div>

            <div class="mt-14 grid gap-12 border-t border-line pt-12 lg:mt-16 lg:grid-cols-12 lg:items-start lg:gap-x-16">
                <div class="article-body lg:col-span-7">
                    @foreach ($paragraphs($promotion->body) as $paragraph)
                        <p>{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach
                </div>

                <aside class="flex flex-col gap-4 lg:sticky lg:top-28 lg:col-span-5">
                    @unless ($ended)
                        {{-- The action, beside the words that justify it. --}}
                        <div class="relative isolate overflow-hidden rounded-2xl bg-accent p-6 text-paper sm:p-8">
                            <img
                                src="{{ asset('images/baardy-mark.png') }}"
                                alt=""
                                aria-hidden="true"
                                class="pointer-events-none absolute -right-16 -bottom-20 -z-10 size-72 opacity-[0.07] mix-blend-luminosity grayscale"
                            >

                            <p class="figure-nums text-small text-paper/70">{{ $promotion->timeLeftLabel() }} &middot; ends {{ $promotion->ends_at->format('j M') }}</p>
                            <h2 class="mt-2 text-[length:clamp(1.5rem,1.2rem+1vw,2rem)] leading-[1.15] font-normal tracking-[-0.02em]">Take up this offer</h2>

                            <ol class="mt-5 flex flex-col gap-3 text-small text-paper/85">
                                <li class="flex gap-3"><span class="figure-nums text-paper/60">1</span> Tell us your name and number.</li>
                                <li class="flex gap-3"><span class="figure-nums text-paper/60">2</span> A person from our team calls you back.</li>
                                <li class="flex gap-3"><span class="figure-nums text-paper/60">3</span> You see the full cost before you commit to anything.</li>
                            </ol>

                            <div class="mt-7 flex flex-col items-start gap-4">
                                <x-ui.button :href="$promotion->enquiryUrl()" variant="inverse" arrow size="lg">{{ $promotion->cta_label }}</x-ui.button>

                                <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                                    @if ($whatsapp)
                                        <x-ui.whatsapp-cta :number="$whatsapp['tel']" :text="$promotion->whatsappMessage()" label="WhatsApp us" />
                                    @endif
                                    <a href="{{ $phoneHref }}" class="group inline-flex items-center gap-2 text-body text-paper/90 transition-colors hover:text-paper">
                                        <x-ui.icon name="phone" class="size-4" />
                                        {{ $phone }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl bg-mist p-6 sm:p-8">
                            <h2 class="text-lead text-ink">This offer has ended</h2>
                            <p class="mt-2 text-body text-ink-soft">The page stays up so shared links still work. See what is running now, or talk to us about a loan.</p>
                            <div class="mt-5 flex flex-wrap items-center gap-4">
                                <x-ui.button :href="route('promotions.index')" arrow>See what is on now</x-ui.button>
                                <a href="{{ route('contact') }}#contact" class="text-small text-ink-soft underline decoration-line-strong underline-offset-4 hover:decoration-ink">Make an enquiry</a>
                            </div>
                        </div>
                    @endunless

                    <div id="terms" class="scroll-mt-28 rounded-xl bg-mist p-6 sm:p-8">
                        <h2 class="text-lead text-ink">Terms and conditions</h2>
                        <div class="mt-4 flex flex-col gap-3 text-small text-ink-soft">
                            @foreach ($paragraphs($promotion->terms) as $paragraph)
                                <p>{!! nl2br(e($paragraph)) !!}</p>
                            @endforeach
                            <p>
                                Every loan is subject to application, affordability assessment and approval. See our
                                <a href="{{ route('legal.terms') }}" class="underline underline-offset-4">terms of use</a>
                                and
                                <a href="{{ route('legal.responsible-lending') }}" class="underline underline-offset-4">responsible lending</a>.
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </x-ui.container>
    </x-ui.section>

    @if ($others->isNotEmpty())
        <x-ui.section :rule="false" class="pt-0">
            <x-ui.container>
                <h2 class="text-[length:clamp(1.5rem,1.1rem+1vw,2rem)] font-light tracking-[-0.025em] text-ink">Other promotions</h2>
                <div class="mt-8 grid gap-x-5 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($others as $other)
                        <x-ui.promotion-card :promotion="$other" />
                    @endforeach
                </div>
            </x-ui.container>
        </x-ui.section>
    @endif

    @unless ($ended)
        {{-- The closing action. The generic homepage slip is not used here: it
             does not carry the promotion's tracking code, so an enquiry begun
             from the foot of this page would not be credited to it. --}}
        <x-ui.section :rule="false" aria-labelledby="promo-close-heading" class="pt-10! sm:pt-14! lg:pt-20!">
            <x-ui.container wide>
                <div data-reveal class="relative isolate grid gap-8 overflow-hidden rounded-2xl bg-accent px-6 py-12 text-paper sm:px-10 sm:py-14 lg:grid-cols-12 lg:items-center lg:px-14 lg:py-16">
                    <img
                        src="{{ asset('images/baardy-mark.png') }}"
                        alt=""
                        aria-hidden="true"
                        class="pointer-events-none absolute -right-40 -bottom-48 -z-10 size-[34rem] opacity-[0.06] mix-blend-luminosity grayscale"
                    >

                    <div class="lg:col-span-7">
                        <x-ui.eyebrow class="text-paper/70">{{ $promotion->timeLeftLabel() }}</x-ui.eyebrow>
                        <h2 id="promo-close-heading" class="mt-5 text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em]">
                            Ready to take up {{ $promotion->title }}?
                        </h2>
                        <p class="mt-5 max-w-lg text-body leading-[1.4] text-paper/75">
                            A person from our team calls you. Applications are made in person at a branch,
                            and nothing is committed until you see the full cost.
                        </p>
                    </div>

                    <div class="flex flex-col items-start gap-4 lg:col-span-5 lg:items-end">
                        <x-ui.button :href="$promotion->enquiryUrl()" variant="inverse" arrow size="lg">{{ $promotion->cta_label }}</x-ui.button>
                        @if ($whatsapp)
                            <x-ui.whatsapp-cta :number="$whatsapp['tel']" :text="$promotion->whatsappMessage()" label="WhatsApp us" />
                        @endif
                    </div>
                </div>
            </x-ui.container>
        </x-ui.section>
    @else
        @include('sections.home.cta')
    @endunless
@endsection
