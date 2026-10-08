{{--
    /loans/{slug} -- one page per loan.

    Each loan gets a page of its own so it can be found, linked and ranked for
    what it is ("educational loans Zimbabwe") instead of living as one card in
    a homepage rail. After the other inner pages: the wide header with the
    loan's photograph, an overview beside a term-sheet card, then who and what
    it is for, what to bring, the questions people ask, and the other loans.

    Words come from config/loans.php, facts from config/marketing.php; see the
    note at the top of config/loans.php about what may and may not be claimed.

    SEO: its own title and description, one h1, a visible breadcrumb with the
    matching BreadcrumbList, a Service and a FAQPage in JSON-LD. The FAQ
    marked up is exactly the FAQ shown.
--}}
@extends('layouts.marketing')

@section('title', $loan['title'])

@section('description', $loan['meta'])

@php
    $symbol = config('company.currency.symbol');
    $range = $symbol.number_format($loan['min']).' to '.$symbol.number_format($loan['max']);
    // Lower-case each item for the middle of a sentence, except proper nouns.
    $audience = collect($loan['audience'])->map(fn (string $item): string => str_starts_with($item, 'Public Service Commission') ? $item : Str::lcfirst($item));
    $cities = collect(config('company.branches'))->pluck('name')->filter()->unique()->values();
    $reviewed = \Illuminate\Support\Carbon::parse(config('seo.reviewed'));
    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));
    $enquiryUrl = route('contact', ['interest' => $loan['name']]).'#contact';

    // The questions, written from what the client has confirmed (see config/loans.php).
    $faqs = [
        [
            'question' => 'Who can apply for '.Str::lower($loan['name']).'?',
            'answer' => $loan['name'].' are for '.$audience->slice(0, -1)->implode(', ').($audience->count() > 1 ? ' and ' : '').$audience->last().'. Applications are made in person at one of our branches, and a person reviews every one.',
        ],
        [
            'question' => 'What do I need to bring?',
            'answer' => $documentsAreComplete
                ? 'Bring '.Str::lcfirst(collect($documents)->slice(0, -1)->implode(', ')).' and '.Str::lcfirst(collect($documents)->last()).'. If your application needs anything else, our team will tell you during appraisal.'
                : 'Bring a valid national ID. If your application needs anything else, our team will tell you during appraisal.',
        ],
        [
            'question' => 'How much can I borrow, and for how long?',
            'answer' => 'Amounts for this loan range from '.$range.', over '.$loan['term'].'. Your own limit is set during credit appraisal and depends on what your income or turnover can support. You see it in writing, with every fee and the total you will repay, before you accept.',
        ],
        [
            'question' => 'How quickly will I get a decision?',
            'answer' => 'Complete applications get a decision within one working day. An application that is missing documents takes longer, so it is worth bringing everything on your first visit.',
        ],
        [
            'question' => 'Can I repay early?',
            'answer' => 'Yes. If you settle early you pay less interest, and there is no early-settlement penalty.',
        ],
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Service',
                'name' => $loan['name'],
                'serviceType' => 'Loan',
                'description' => $loan['meta'],
                'url' => url()->current(),
                'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe'],
                'provider' => \App\Support\StructuredData::organizationRef(),
            ],
            \App\Support\StructuredData::breadcrumbs([['Home', route('home')], ['Loans', route('loans.index')], [$loan['name'], url()->current()]]),
            [
                '@type' => 'FAQPage',
                'mainEntity' => collect($faqs)->map(fn (array $faq): array => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
                ])->all(),
            ],
        ],
    ];

    $tick = '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="mt-1 size-4 shrink-0 text-accent"><path d="m3.5 8.5 3 3 6-7" /></svg>';
    $highlightTick = str_replace('text-accent', 'text-highlight', $tick);
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <x-layout.page-header
        eyebrow="Loans"
        :title="$loan['headline']"
        :lead="$loan['summary']"
        :image="$loan['image']['src']"
        :alt="$loan['image']['alt']"
        :position="$loan['image']['position']"
        :width="1600"
        :height="1067"
    >
        <nav aria-label="Breadcrumb" class="rise mt-6 text-small text-muted [animation-delay:340ms]">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <li><a href="{{ route('home') }}" class="transition-colors hover:text-accent">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('loans.index') }}" class="transition-colors hover:text-accent">Loans</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="text-ink">{{ $loan['name'] }}</li>
            </ol>
        </nav>
    </x-layout.page-header>

    {{-- Overview, beside the term sheet --}}
    <x-ui.section :rule="false" aria-labelledby="overview-label" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide>
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-x-8">
                <x-ui.eyebrow id="overview-label" data-reveal class="self-start lg:col-span-2">Overview</x-ui.eyebrow>

                <div data-reveal class="flex flex-col gap-8 lg:col-span-6">
                    <p class="text-[length:clamp(1.25rem,1rem+0.9vw,1.625rem)] leading-[1.35] tracking-[-0.02em] text-ink">{{ $loan['intro'][0] }}</p>
                    @foreach (array_slice($loan['intro'], 1) as $paragraph)
                        <p class="max-w-xl text-[1.0625rem] leading-[1.6] text-ink-soft sm:text-[1.125rem]">{{ $paragraph }}</p>
                    @endforeach
                </div>

                <x-ui.photo-card data-reveal image="images/why-us/privacy-1400.webp" position="50% 15%" panel="gap-6 sm:p-7" class="self-start pt-40 sm:pt-52 lg:sticky lg:top-28 lg:col-span-4">
                    @isset ($promotion)
                        <a href="{{ route('promotions.show', $promotion) }}" class="w-fit rounded-full bg-highlight px-3 py-1 text-micro font-medium text-paper">
                            Limited offer &middot; {{ $promotion->summary }}
                        </a>
                    @endisset

                    <p class="text-small font-medium text-paper">At a glance</p>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-5 border-t border-paper/20 pt-5">
                        <div class="col-span-2 flex flex-col gap-0.5">
                            <dt class="text-micro text-paper/60">Amount</dt>
                            <dd class="text-lead font-medium text-paper">
                                <x-ui.money :amount="$loan['min']" /> <span class="text-paper/60">&ndash;</span> <x-ui.money :amount="$loan['max']" />
                            </dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-micro text-paper/60">Term</dt>
                            <dd class="figure-nums text-body font-medium text-paper">{{ $loan['term'] }}</dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-micro text-paper/60">Decision</dt>
                            <dd class="text-body font-medium text-paper">In one working day</dd>
                        </div>
                        <div class="col-span-2 flex flex-col gap-0.5">
                            <dt class="text-micro text-paper/60">Best for</dt>
                            <dd class="text-body text-paper/80">{{ $loan['best_for'] }}</dd>
                        </div>
                    </dl>
                    <p class="text-micro leading-[1.5] text-paper/60">Your own limit is set when your application is assessed. A decision in one working day applies to complete applications.</p>

                    <div class="flex flex-col gap-3">
                        <x-ui.button :href="$enquiryUrl" arrow>Enquire about this loan</x-ui.button>
                        @if ($whatsapp)
                            <x-ui.whatsapp-cta :number="$whatsapp['tel']" :text="'Hello Baardy Micro Capital, I would like to know more about '.$loan['name'].'.'" label="Ask on WhatsApp" tone="paper" />
                        @endif
                    </div>
                </x-ui.photo-card>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- Quick answers: each is a question people ask, with its answer first --}}
    <x-ui.section :rule="false" aria-labelledby="answers-label" class="pt-0! pb-16! sm:pb-20! lg:pb-24!">
        <x-ui.container wide>
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-x-8">
                <x-ui.eyebrow id="answers-label" data-reveal class="self-start lg:col-span-2">Quick answers</x-ui.eyebrow>

                <div class="flex flex-col lg:col-span-10">
                    <section data-reveal class="grid gap-3 border-t border-line py-8 lg:grid-cols-10 lg:gap-x-8">
                        <h2 class="text-[length:clamp(1.25rem,1.05rem+0.7vw,1.5rem)] leading-[1.25] font-normal tracking-[-0.02em] text-balance text-ink lg:col-span-4">What is {{ $loan['noun'] }}?</h2>
                        <p class="max-w-2xl text-[1.0625rem] leading-[1.65] text-ink-soft lg:col-span-6">{{ $loan['definition'] }}</p>
                    </section>

                    <section data-reveal class="grid gap-3 border-t border-line py-8 lg:grid-cols-10 lg:gap-x-8">
                        <h2 class="text-[length:clamp(1.25rem,1.05rem+0.7vw,1.5rem)] leading-[1.25] font-normal tracking-[-0.02em] text-balance text-ink lg:col-span-4">{{ $loan['where_question'] }}</h2>
                        <p class="max-w-2xl text-[1.0625rem] leading-[1.65] text-ink-soft lg:col-span-6">
                            At {{ config('company.legal_name') }}, in person at our {{ $cities->slice(0, -1)->implode(', ') }}{{ $cities->count() > 1 ? ' or ' : '' }}{{ $cities->last() }} {{ $cities->count() > 1 ? 'offices' : 'office' }}. Bring your documents, and a person assesses your application. Complete applications get a decision within one working day, and the full cost is written down before you accept.
                        </p>
                    </section>

                    <section data-reveal class="grid gap-3 border-t border-b border-line py-8 lg:grid-cols-10 lg:gap-x-8">
                        <h2 class="text-[length:clamp(1.25rem,1.05rem+0.7vw,1.5rem)] leading-[1.25] font-normal tracking-[-0.02em] text-balance text-ink lg:col-span-4">What documents do I need for {{ $loan['noun'] }}?</h2>
                        <p class="max-w-2xl text-[1.0625rem] leading-[1.65] text-ink-soft lg:col-span-6">
                            {{ $documentsAreComplete ? 'Bring '.Str::lcfirst(collect($documents)->slice(0, -1)->implode(', ')).' and '.Str::lcfirst(collect($documents)->last()).'.' : 'Bring a valid national ID.' }}
                            If your application needs anything else, our team will tell you during appraisal.
                        </p>
                    </section>

                    <p class="figure-nums pt-6 text-small text-muted">Last reviewed <time datetime="{{ $reviewed->toDateString() }}">{{ $reviewed->format('j F Y') }}</time></p>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- What it is for, and who it is for --}}
    <x-ui.section :rule="false" tone="mist" aria-labelledby="fit-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="fit-heading"
                eyebrow="Is it for you"
                :title="'What '.Str::lower($loan['name']).' are for'"
                lead="The purposes this loan is meant for, and the people it is meant for."
            />

            <div data-reveal-stagger class="mt-12 grid gap-3 lg:mt-16 lg:grid-cols-2 lg:gap-4">
                <x-ui.photo-card :image="$loan['image']['src']" :alt="$loan['image']['alt']" :position="$loan['image']['position']" class="min-h-[28rem] pt-40 sm:pt-56">
                    <h3 class="text-[length:clamp(1.375rem,1.1rem+0.9vw,1.75rem)] leading-[1.2] font-normal tracking-[-0.02em]">You can use it for</h3>
                    <ul class="flex flex-col gap-3 text-body text-paper/85">
                        @foreach ($loan['uses'] as $use)
                            <li class="flex gap-3">{!! $highlightTick !!} {{ $use }}</li>
                        @endforeach
                    </ul>
                </x-ui.photo-card>

                <div class="flex flex-col gap-6 rounded-2xl bg-paper p-6 sm:p-8">
                    <h3 class="text-[length:clamp(1.375rem,1.1rem+0.9vw,1.75rem)] leading-[1.2] font-normal tracking-[-0.02em] text-ink">It is meant for</h3>
                    <ul class="flex flex-col gap-3 text-body text-ink-soft">
                        @foreach ($loan['audience'] as $person)
                            <li class="flex gap-3">{!! $tick !!} {{ $person }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- What to bring, and how it goes --}}
    <x-ui.section :rule="false" aria-labelledby="apply-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="apply-heading"
                eyebrow="How to apply"
                title="Come in with your papers, leave with an answer"
                lead="Applications are made in person, so you can ask questions and see everything in writing."
            />

            <div class="mt-12 grid gap-3 lg:mt-16 lg:grid-cols-12 lg:gap-4">
                <x-ui.photo-card data-reveal image="images/why-us/shopkeeper-1440.webp" position="50% 35%" class="min-h-[30rem] pt-48 lg:col-span-5">
                    <h3 class="text-[length:clamp(1.375rem,1.1rem+0.9vw,1.75rem)] leading-[1.2] font-normal tracking-[-0.02em]">What to bring</h3>
                    <ul class="flex flex-col gap-3 text-body text-paper/85">
                        @foreach ($documents as $document)
                            <li class="flex gap-3">{!! $highlightTick !!} {{ $document }}</li>
                        @endforeach
                    </ul>
                    @unless ($documentsAreComplete)
                        <p class="text-small leading-[1.5] text-paper/70">If your application needs anything else, our team will tell you during appraisal.</p>
                    @endunless
                </x-ui.photo-card>

                <div data-reveal class="relative isolate flex overflow-hidden rounded-2xl bg-ink p-3 sm:p-4 lg:col-span-7">
                    <img src="{{ asset('images/why-us/schedules-1400.webp') }}" alt="" width="1400" height="932" loading="lazy" decoding="async" class="absolute inset-0 -z-10 size-full object-cover object-[50%_40%]">
                    <ol data-reveal-stagger class="grid w-full gap-3 sm:grid-cols-3">
                        @foreach (config('marketing.steps') as $step)
                            <li class="flex flex-col justify-between gap-10 rounded-xl bg-ink/50 p-5 text-paper backdrop-blur-xl backdrop-saturate-150">
                                <span class="figure-nums flex size-8 items-center justify-center rounded-full bg-paper/15 text-small">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="flex flex-col gap-2">
                                    <p class="text-lead font-medium">{{ $step['title'] }}</p>
                                    <p class="text-body leading-[1.5] text-paper/80">
                                        @if ($loop->first && ! $documentsAreComplete)
                                            Come in to our Harare or Bulawayo branch with your valid national ID, and anything else our team asks for.
                                        @else
                                            {{ $step['body'] }}
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- Questions --}}
    <x-ui.section :rule="false" tone="mist" aria-labelledby="loan-faq-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <div class="grid gap-x-12 gap-y-10 lg:grid-cols-12">
                <div data-reveal class="flex flex-col gap-5 lg:col-span-4">
                    <x-ui.eyebrow>Questions</x-ui.eyebrow>
                    <h2 id="loan-faq-heading" class="text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink">Before you apply</h2>
                    <p class="max-w-sm text-body leading-[1.4] text-ink-soft">If yours is not here, a person will answer it. Call us on <a href="tel:{{ preg_replace('/\s+/', '', config('company.contact.phone')) }}" class="figure-nums text-accent underline underline-offset-4">{{ config('company.contact.phone') }}</a>.</p>
                </div>

                <div data-reveal-stagger class="flex flex-col gap-3 lg:col-span-8">
                    @foreach ($faqs as $faq)
                        <x-ui.accordion-item
                            :question="$faq['question']"
                            :number="str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)"
                            :open="$loop->first"
                            group="loan-faq"
                        >
                            {{ $faq['answer'] }}
                        </x-ui.accordion-item>
                    @endforeach
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- The other loans --}}
    <x-ui.section :rule="false" aria-labelledby="more-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="more-heading"
                eyebrow="More loans"
                title="Other ways we lend"
                lead="Each loan has its own range, term and paperwork."
            />

            <div data-reveal-stagger class="mt-12 grid gap-3 sm:grid-cols-2 lg:mt-16 lg:grid-cols-5 lg:gap-4">
                @foreach ($others as $other)
                    <x-ui.loan-card :loan="$other" :range="false" />
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
