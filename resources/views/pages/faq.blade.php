{{--
    /faq -- the questions people ask before they borrow.

    The standing home for the answers (FAQPage markup lives here and on the
    loan pages). The first five are config('marketing.faqs'), shared with the
    homepage; the rest are about who we are and are answered from verified
    facts in config/company.php. No rate, age or eligibility rule appears,
    because none has been supplied.
--}}
@extends('layouts.marketing')

@section('title', 'Loan FAQs for Zimbabwe | Baardy Micro Capital')

@section('description', 'Answers to common questions about borrowing from Baardy Micro Capital: documents, repayment, missed payments, early settlement and our licence.')

@php
    $register = config('company.compliance.register_url');

    $about = [
        [
            'question' => 'Is Baardy Micro Capital licensed?',
            'answer' => 'Yes. '.config('company.legal_name').' is a '.config('company.compliance.licence_category').' institution licensed by '.config('company.compliance.regulator').' and on its register since '.config('company.compliance.licensed_since').'. You can check the register yourself on the Reserve Bank of Zimbabwe website.',
        ],
        [
            'question' => 'What does credit-only microfinance mean?',
            'answer' => 'A credit-only microfinance institution lends money but does not take deposits. Baardy Micro Capital lends to individuals, farmers and small businesses.',
        ],
        [
            'question' => 'Where are your offices?',
            'answer' => 'We have offices in Harare and Bulawayo. Applications are made in person at either one, and you can start with an enquiry through our website, by phone or on WhatsApp.',
        ],
        [
            'question' => 'Can I apply online?',
            'answer' => 'Applications are made in person at a branch, so you can ask questions and see everything in writing. You can send an enquiry through the website first, and a person will call or WhatsApp you back.',
        ],
    ];

    $faqs = collect(config('marketing.faqs'))->concat($about)->values();
@endphp

@section('content')
    <x-seo.schema :data="[\App\Support\StructuredData::organization(), [
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn (array $faq): array => ['@type' => 'Question', 'name' => $faq['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']]])->all(),
    ]]" />

    <x-layout.page-header
        :crumbs="[['Questions']]"
        eyebrow="Questions"
        title="What people ask before they borrow"
        lead="The things borrowers ask most often. If yours is not here, a person will answer it."
    />

    <x-ui.section :rule="false" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide>
            <div class="grid gap-x-12 gap-y-10 lg:grid-cols-12">
                <div data-reveal class="flex flex-col gap-5 lg:col-span-4 lg:sticky lg:top-28 lg:self-start">
                    <x-ui.eyebrow>Talk to a person</x-ui.eyebrow>
                    <p class="max-w-sm text-body leading-[1.4] text-ink-soft">Call us on <a href="tel:{{ preg_replace('/\s+/', '', config('company.contact.phone')) }}" class="figure-nums text-accent underline underline-offset-4">{{ config('company.contact.phone') }}</a>, or <a href="{{ route('contact') }}#contact" class="text-accent underline underline-offset-4">send us a message</a>.</p>
                    <p class="max-w-sm text-small leading-[1.5] text-muted">How to apply, step by step: <a href="{{ route('how-to-apply') }}" class="text-accent underline underline-offset-4">how to apply</a>. The loans: <a href="{{ route('loans.index') }}" class="text-accent underline underline-offset-4">see them all</a>.</p>
                </div>

                <div data-reveal-stagger class="flex flex-col gap-3 lg:col-span-8">
                    @foreach ($faqs as $faq)
                        <x-ui.accordion-item :question="$faq['question']" :number="str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)" :open="$loop->first" group="faq-hub">
                            {{ $faq['answer'] }}
                        </x-ui.accordion-item>
                    @endforeach

                    @if (filled($register))
                        <p class="px-2 pt-4 text-small text-muted">Check our licence: <a href="{{ $register }}" target="_blank" rel="noopener" class="text-accent underline underline-offset-4">Reserve Bank of Zimbabwe microfinance register</a>.</p>
                    @endif
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
