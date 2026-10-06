{{--
    /insights/{slug} -- one article.

    The body is a Blade partial (insights/articles/{slug}) rendered inside
    `.article-body`, which sets the long-form reading rhythm (app.css).
--}}
@extends('layouts.marketing')

@php
    $published = \Illuminate\Support\Carbon::parse($article['published']);
    $image = 'images/insights/'.$article['image'];
@endphp

@section('title', ($article['seo_title'] ?? $article['title']).' | '.config('company.name'))

@section('og_type', 'article')

@section('og_image', asset($image.'-1600.webp'))

@section('og_image_alt', $article['alt'])

@push('og')
    <meta property="article:published_time" content="{{ $published->toDateString() }}">
@endpush

@section('description', $article['excerpt'])

@section('content')
    <x-seo.schema :data="[\App\Support\StructuredData::article($article), \App\Support\StructuredData::breadcrumbs([['Home', route('home')], ['Insights', route('insights.index')], [$article['title'], url()->current()]])]" />

    <article>
        <x-ui.section :rule="false" class="pt-10 pb-0 sm:pt-14 sm:pb-0 lg:pt-16 lg:pb-0">
            <x-ui.container>
                <nav aria-label="Breadcrumb" class="rise text-small text-muted">
                    <a href="{{ route('home') }}" class="transition-colors hover:text-accent">Home</a>
                    <span aria-hidden="true" class="mx-2">/</span>
                    <a href="{{ route('insights.index') }}" class="transition-colors hover:text-accent">Insights</a>
                </nav>

                <header class="mx-auto mt-10 flex max-w-3xl flex-col gap-6 lg:mt-14">
                    <p class="rise figure-nums flex flex-wrap items-center gap-x-3 gap-y-1 text-small text-muted [animation-delay:60ms]">
                        <span class="rounded-full bg-accent-tint px-3 py-1 font-medium text-accent">{{ $article['category'] }}</span>
                        <time datetime="{{ $published->toDateString() }}">{{ $published->format('j F Y') }}</time>
                        <span aria-hidden="true">&middot;</span>
                        <span>By {{ \App\Support\StructuredData::BRAND }}</span>
                        <span aria-hidden="true">&middot;</span>
                        <span>{{ $article['minutes'] }} min read</span>
                    </p>

                    <h1 class="rise text-display font-medium tracking-[-0.035em] text-balance text-ink [animation-delay:120ms]">
                        {{ $article['title'] }}
                    </h1>

                    <p class="rise text-lead text-muted [animation-delay:180ms]">
                        {{ $article['excerpt'] }}
                    </p>
                </header>

                <div class="rise mt-12 overflow-hidden rounded-lg bg-mist [animation-delay:240ms] lg:mt-16">
                    <img
                        src="{{ asset($image.'-1600.webp') }}"
                        srcset="{{ asset($image.'-800.webp') }} 800w, {{ asset($image.'-1600.webp') }} 1600w"
                        sizes="(min-width: 75rem) 72rem, 100vw"
                        width="1600"
                        height="900"
                        alt="{{ $article['alt'] }}"
                        fetchpriority="high"
                        class="aspect-[16/9] w-full object-cover"
                    >
                </div>
            </x-ui.container>
        </x-ui.section>

        <x-ui.section :rule="false" class="pt-14 sm:pt-16 lg:pt-20">
            <x-ui.container>
                <div class="article-body mx-auto max-w-2xl">
                    @include('insights.articles.'.$article['slug'])
                </div>

                @php
                    $relatedLoans = collect(config('marketing.products'))->whereIn('slug', $article['loans'] ?? []);
                @endphp
                @if ($relatedLoans->isNotEmpty())
                    <aside class="mx-auto mt-14 max-w-2xl rounded-lg border border-line p-6 sm:p-8">
                        <h2 class="text-body font-medium text-ink">Loans related to this guide</h2>
                        <ul class="mt-4 flex flex-col gap-2">
                            @foreach ($relatedLoans as $related)
                                <li><a href="{{ route('loans.show', $related['slug']) }}" class="text-body text-accent underline decoration-accent/30 underline-offset-4 transition-colors hover:decoration-accent">{{ $related['name'] }}</a> <span class="text-small text-muted">&middot; {{ $related['best_for'] }}</span></li>
                            @endforeach
                        </ul>
                    </aside>
                @endif

                <div class="mx-auto mt-14 flex max-w-2xl flex-col gap-4 rounded-lg bg-mist p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <p class="text-body text-ink">
                        Want to talk this through with a person?
                    </p>
                    <x-ui.button :href="config('company.cta.primary.href')" class="group shrink-0">
                        Get in touch
                        <x-ui.icon name="arrow-right" class="transition-transform duration-200 group-hover:translate-x-0.5" />
                    </x-ui.button>
                </div>
            </x-ui.container>
        </x-ui.section>
    </article>

    @if ($more->isNotEmpty())
        <x-ui.section>
            <x-ui.container>
                <div class="flex items-end justify-between gap-6">
                    <h2 class="text-h2 font-medium tracking-[-0.025em] text-ink">More insights</h2>
                    <a href="{{ route('insights.index') }}" class="group inline-flex items-center gap-2 text-body font-medium text-accent">
                        View all
                        <x-ui.icon name="arrow-right" class="transition-transform duration-200 group-hover:translate-x-1" />
                    </a>
                </div>

                <div data-reveal-pop class="mt-12 grid gap-x-8 gap-y-14 sm:grid-cols-2">
                    @foreach ($more as $next)
                        <x-ui.article-card :article="$next" sizes="(min-width: 40rem) 50vw, 100vw" />
                    @endforeach
                </div>
            </x-ui.container>
        </x-ui.section>
    @endif
@endsection
