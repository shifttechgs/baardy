{{--
    /insights -- every article, newest first.

    After stitch.money/blog: a quiet hero, category pills, then the newest
    article large on the left with the next two stacked beside it, and any
    older ones in a three-column grid below. Every card keeps its text below
    the photograph (x-ui.article-card).

    FILTERING. The pills filter on the client (Alpine) by `category`, since
    every article is already on the page. With a category chosen the layout
    becomes a plain grid of the matches. Without JavaScript the pills are
    inert and every article shows.
--}}
@extends('layouts.marketing')

@section('title', 'Insights | '.config('company.name'))

@section('description', 'Practical guidance on budgeting, borrowing and running a small business in Zimbabwe, from '.config('company.legal_name').'.')

@php
    $articles = collect($articles)->values();
    $categories = $articles->pluck('category')->unique()->values();
@endphp

@section('content')
    <x-layout.page-header
        eyebrow="Insights"
        title="Advice from across the counter"
        lead="Short, practical guides on budgeting, borrowing and seasonal finance. The same advice our team gives at the branch."
    />

    <x-ui.section :rule="false" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide x-data="{ category: 'All' }">
            {{-- Category pills --}}
            <div role="group" aria-label="Filter by topic" class="rise flex flex-wrap gap-2 [animation-delay:240ms]">
                @foreach (collect(['All'])->concat($categories) as $category)
                    <button
                        type="button"
                        x-on:click="category = {{ Js::from($category) }}"
                        x-bind:aria-pressed="category === {{ Js::from($category) }} ? 'true' : 'false'"
                        class="rounded-full border px-4 py-1.5 text-small uppercase tracking-[0.04em] transition-colors duration-200"
                        x-bind:class="category === {{ Js::from($category) }} ? 'border-ink bg-ink text-paper' : 'border-line-strong text-ink hover:border-ink'"
                    >
                        {{ $category }}
                    </button>
                @endforeach
            </div>

            {{-- All: the lead article featured, the next two beside it. --}}
            <div x-show="category === 'All'" class="mt-10 flex flex-col gap-y-16 lg:mt-12">
                <div class="grid gap-x-5 gap-y-12 lg:grid-cols-12">
                    @if ($articles->isNotEmpty())
                        <x-ui.article-card :article="$articles->first()" lead sizes="(min-width: 64rem) 55vw, 100vw" class="lg:col-span-7" />
                    @endif

                    <div class="grid gap-x-5 gap-y-12 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-1 lg:gap-y-10">
                        @foreach ($articles->slice(1, 2) as $article)
                            <x-ui.article-card :article="$article" sizes="(min-width: 64rem) 36vw, (min-width: 40rem) 50vw, 100vw" />
                        @endforeach
                    </div>
                </div>

                @if ($articles->count() > 3)
                    <div class="grid gap-x-5 gap-y-12 border-t border-line pt-16 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($articles->slice(3) as $article)
                            <x-ui.article-card :article="$article" />
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- One topic: a plain grid of its articles. --}}
            <div x-show="category !== 'All'" x-cloak class="mt-10 grid gap-x-5 gap-y-12 sm:grid-cols-2 lg:mt-12 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <x-ui.article-card
                        :article="$article"
                        x-show="category === {{ Js::from($article['category']) }}"
                    />
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
