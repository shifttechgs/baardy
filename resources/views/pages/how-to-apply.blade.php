{{--
    /how-to-apply -- how to apply for a loan, and what to bring.

    Answers the questions people type before they ever pick a lender ("how do
    I apply for a loan in Zimbabwe", "what documents do I need"): the answer
    first, in a short paragraph an assistant can quote, then the steps and the
    documents. Everything here is the client's own process (config/marketing.php
    -> steps, eligibility.profiles, faqs); no rate, age or rule is added.
--}}
@extends('layouts.marketing')

@section('title', 'How to Apply for a Loan in Zimbabwe | Baardy Micro Capital')

@section('description', 'How to apply for a loan in Zimbabwe: visit a Baardy branch in Harare or Bulawayo with your ID and payslip. See the steps and documents to bring.')

@php
    $steps = config('marketing.steps');
    $profiles = config('marketing.eligibility.profiles');
    $faqs = collect(config('marketing.faqs'))->take(3)->values();
    $loans = collect(config('marketing.products'))->filter(fn (array $product): bool => isset($product['slug']));
    $photos = [['images/products/salary-1600.webp', '65% 30%'], ['images/products/sme-1600.webp', '50% 40%']];
@endphp

@section('content')
    <x-seo.schema :data="[\App\Support\StructuredData::organization(), [
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn (array $faq): array => ['@type' => 'Question', 'name' => $faq['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']]])->all(),
    ]]" />

    <x-layout.page-header
        :crumbs="[['How to apply']]"
        eyebrow="How to apply"
        title="How to apply for a loan in Zimbabwe"
        lead="Come in with your papers, leave with an answer. Everything is explained and written down."
        image="images/hero/vendor-2400.webp"
        alt="A market vendor, one of the people we lend to"
        position="50% 40%"
    />

    <x-ui.section :rule="false" aria-labelledby="answer-label" class="pt-0! pb-16! sm:pb-20! lg:pb-24!">
        <x-ui.container wide>
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-x-8">
                <x-ui.eyebrow id="answer-label" data-reveal class="self-start lg:col-span-2">The short answer</x-ui.eyebrow>
                <div data-reveal class="flex flex-col gap-6 lg:col-span-8">
                    <p class="text-[length:clamp(1.25rem,1rem+0.9vw,1.625rem)] leading-[1.35] tracking-[-0.02em] text-ink">
                        To apply for a loan with {{ config('company.legal_name') }}, visit our Harare or Bulawayo branch with a valid national ID and your latest payslip. Businesses also bring company documents and financials. Our credit team assesses your application, and complete applications get a decision within one working day.
                    </p>
                    <p class="max-w-2xl text-[1.0625rem] leading-[1.6] text-ink-soft">
                        Not ready to visit yet? <a href="{{ route('contact') }}#contact" class="text-accent underline underline-offset-4">Send an enquiry</a> and a person will call or WhatsApp you back.
                    </p>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-ui.section :rule="false" tone="mist" aria-labelledby="steps-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading id="steps-heading" eyebrow="The steps" title="Three steps, on paper at every one" />

            <div data-reveal class="relative isolate mt-12 flex overflow-hidden rounded-2xl bg-ink p-3 sm:p-4 lg:mt-16 lg:p-5">
                <img src="{{ asset('images/hero/market-2400.webp') }}" alt="" width="2400" height="1602" loading="lazy" decoding="async" class="absolute inset-0 -z-10 size-full object-cover object-[50%_60%]">
                <ol data-reveal-stagger class="grid w-full gap-3 pt-24 sm:pt-40 lg:grid-cols-3 lg:gap-4">
                    @foreach ($steps as $step)
                        <li class="flex flex-col justify-between gap-10 rounded-xl bg-ink/50 p-6 text-paper backdrop-blur-xl backdrop-saturate-150 sm:p-8">
                            <span class="figure-nums flex size-9 items-center justify-center rounded-full bg-paper/15 text-small">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="flex flex-col gap-2">
                                <h3 class="text-[length:clamp(1.25rem,1.05rem+0.7vw,1.5rem)] leading-[1.2] font-normal tracking-[-0.02em]">{{ $step['title'] }}</h3>
                                <p class="text-body leading-[1.5] text-paper/80">{{ $step['body'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-ui.section :rule="false" aria-labelledby="documents-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="documents-heading"
                eyebrow="What to bring"
                title="Documents needed for a loan in Zimbabwe"
                lead="A complete application is the fastest one. If yours needs anything else, our team will tell you."
            />

            <div data-reveal-stagger class="mt-12 grid gap-3 lg:mt-16 lg:grid-cols-2 lg:gap-4">
                @foreach ($profiles as $profile)
                    <x-ui.photo-card :image="$photos[$loop->index % 2][0]" :position="$photos[$loop->index % 2][1]" class="min-h-[28rem] pt-40 sm:pt-56">
                        <h3 class="text-[length:clamp(1.25rem,1.05rem+0.7vw,1.5rem)] leading-[1.2] font-normal tracking-[-0.02em]">{{ $profile['label'] }}</h3>
                        <ul class="flex flex-col gap-3 text-body text-paper/85">
                            @foreach ($profile['documents'] as $document)
                                <li class="flex gap-3">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="mt-1 size-4 shrink-0 text-highlight"><path d="m3.5 8.5 3 3 6-7" /></svg>
                                    {{ $document }}
                                </li>
                            @endforeach
                        </ul>
                    </x-ui.photo-card>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-ui.section :rule="false" tone="mist" aria-labelledby="faq-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <div class="grid gap-x-12 gap-y-10 lg:grid-cols-12">
                <div data-reveal class="flex flex-col gap-5 lg:col-span-4">
                    <x-ui.eyebrow>Questions</x-ui.eyebrow>
                    <h2 id="faq-heading" class="text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink">Before you visit</h2>
                    <p class="max-w-sm text-body leading-[1.4] text-ink-soft">More answers are on our <a href="{{ route('faq') }}" class="text-accent underline underline-offset-4">questions page</a>.</p>
                </div>
                <div data-reveal-stagger class="flex flex-col gap-3 lg:col-span-8">
                    @foreach ($faqs as $faq)
                        <x-ui.accordion-item :question="$faq['question']" :number="str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)" :open="$loop->first" group="apply-faq">
                            {{ $faq['answer'] }}
                        </x-ui.accordion-item>
                    @endforeach
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-ui.section :rule="false" aria-labelledby="loans-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading id="loans-heading" eyebrow="Choose a loan" title="Which loan are you applying for?" lead="Each has its own range, term and paperwork." />

            <div data-reveal-stagger class="mt-12 grid gap-3 sm:grid-cols-2 lg:mt-16 lg:grid-cols-3 lg:gap-4">
                @foreach ($loans as $loan)
                    <x-ui.loan-card :loan="$loan" />
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
