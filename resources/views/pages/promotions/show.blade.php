{{--
    /promotions/{slug} -- one promotion, with its full terms.

    This is the page every placement links to and the link shared on
    WhatsApp and social posts, so it carries everything: what the offer is,
    when it runs, the terms in full, and the one action. The action goes to
    the enquiry form with the loan preselected and the tracking code carried
    through (Promotion::enquiryUrl()).

    After the end date the page stays up and says the promotion has ended,
    with no call to action, so an old shared link never lands on a 404 or on
    an offer that no longer stands.

    Body and terms are plain text from the admin panel, escaped, with blank
    lines turned into paragraphs.
--}}
@extends('layouts.marketing')

@php
    $paragraphs = fn (string $text): array => array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $text))));
    $ended = $promotion->hasEnded();
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

            <div class="mt-10 grid gap-12 lg:mt-14 lg:grid-cols-12 lg:gap-x-16">
                <header class="flex flex-col gap-5 lg:col-span-7">
                    <p class="rise flex flex-wrap items-center gap-2 text-small [animation-delay:60ms]">
                        @if ($ended)
                            <span class="rounded-full bg-mist px-3 py-1 font-medium text-muted">This promotion has ended</span>
                        @else
                            <span class="rounded-full bg-highlight-tint px-3 py-1 font-medium text-highlight">Limited offer</span>
                        @endif
                        @if ($promotion->product)
                            <span class="text-muted">{{ $promotion->product }}</span>
                        @endif
                    </p>

                    <h1 class="rise text-[length:clamp(2.25rem,1.4rem+3vw,3.75rem)] leading-[1.05] font-light tracking-[-0.035em] text-balance text-ink [animation-delay:120ms]">
                        {{ $promotion->title }}
                    </h1>

                    <p class="rise max-w-2xl text-lead text-ink-soft [animation-delay:180ms]">{{ $promotion->summary }}</p>

                    <p class="rise figure-nums text-small text-muted [animation-delay:220ms]">
                        Runs
                        <time datetime="{{ $promotion->starts_at->toDateString() }}">{{ $promotion->starts_at->format('j F Y') }}</time>
                        to
                        <time datetime="{{ $promotion->ends_at->toDateString() }}">{{ $promotion->ends_at->format('j F Y') }}</time>
                    </p>

                    @unless ($ended)
                        <div class="rise mt-3 flex flex-wrap items-center gap-4 [animation-delay:260ms]">
                            <x-ui.button :href="$promotion->enquiryUrl()" pill size="lg" class="group gap-3 pr-1.5 pl-6">
                                {{ $promotion->cta_label }}
                                <span class="inline-flex size-10 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5">
                                    <x-ui.icon name="arrow-right" />
                                </span>
                            </x-ui.button>
                            <a href="#terms" class="text-small text-ink-soft underline decoration-line-strong underline-offset-4 hover:decoration-ink">Read the terms</a>
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

            <div class="mt-14 grid gap-12 border-t border-line pt-12 lg:mt-16 lg:grid-cols-12 lg:gap-x-16">
                <div class="article-body lg:col-span-7">
                    @foreach ($paragraphs($promotion->body) as $paragraph)
                        <p>{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach
                </div>

                <aside id="terms" class="scroll-mt-28 lg:col-span-5">
                    <div class="rounded-xl bg-mist p-6 sm:p-8">
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

    @include('sections.home.cta')
@endsection
