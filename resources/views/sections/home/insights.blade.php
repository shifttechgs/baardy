{{--
    Insights -- the three newest articles from config('insights.articles').

    The written side of the advisory service: practical guidance on
    budgeting, borrowing and seasonal finance.

    Laid out like the other sections: label and heading left, one line and an
    action right. Below, an editorial split after stitch.money/blog -- the
    newest article large on the left, the next two stacked on the right,
    every one with its text below the photograph (x-ui.article-card).
--}}
@php
    $articles = collect(config('insights.articles'))->take(3);
    $lead = $articles->first();
    $more = $articles->slice(1);
@endphp

<x-ui.section id="insights" :rule="false" aria-labelledby="insights-heading" class="pt-10! sm:pt-14! lg:pt-20!">
    <x-ui.container wide>

        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-body font-medium text-accent">Insights</p>
                <h2 data-word-reveal
                    id="insights-heading"
                    class="mt-4 text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink"
                >
                    Advice from across the counter
                </h2>
            </div>

            <div class="flex flex-col items-start gap-5 lg:col-span-4 lg:col-start-9 lg:items-end lg:text-right">
                <p data-word-reveal class="max-w-sm text-lead text-ink-soft">
                    Short, practical guides on budgeting, borrowing and seasonal finance. The same
                    advice our team gives at the branch.
                </p>

                <x-ui.button :href="route('insights.index')" variant="secondary" pill class="group gap-3 pr-1.5 pl-5">
                    View all insights
                    <span class="inline-flex size-8 items-center justify-center rounded-full bg-ink text-paper transition-transform duration-300 group-hover:translate-x-0.5">
                        <x-ui.icon name="arrow-right" />
                    </span>
                </x-ui.button>
            </div>
        </div>

        <div data-reveal-cards class="mt-12 grid gap-x-5 gap-y-12 lg:mt-16 lg:grid-cols-12">
            @if ($lead)
                <x-ui.article-card :article="$lead" lead sizes="(min-width: 64rem) 55vw, 100vw" class="lg:col-span-7" />
            @endif

            @if ($more->isNotEmpty())
                <div class="grid gap-x-5 gap-y-12 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-1 lg:gap-y-10">
                    @foreach ($more as $article)
                        <x-ui.article-card :article="$article" sizes="(min-width: 64rem) 36vw, (min-width: 40rem) 50vw, 100vw" />
                    @endforeach
                </div>
            @endif
        </div>
    </x-ui.container>
</x-ui.section>
