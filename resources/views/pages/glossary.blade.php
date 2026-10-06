{{--
    /glossary -- plain-language meanings of the words used around a loan.

    Definitions are general and carry no Baardy-specific rate, fee or rule.
    Marked up as a DefinedTermSet so search and AI engines can quote each
    term on its own.
--}}
@extends('layouts.marketing')

@section('title', 'Loan Glossary: Plain-Language Terms | Baardy Micro Capital')

@section('description', 'What credit-only microfinance, bridging finance, appraisal, disbursement and early settlement mean, explained in plain language.')

@php
    $terms = [
        ['term' => 'Appraisal', 'definition' => 'The lender\'s check of your application and documents, to see whether the loan can be approved and what you can comfortably repay. At Baardy, our credit team carries it out and a person reviews every application.'],
        ['term' => 'Bridging finance', 'definition' => 'Short-term borrowing that carries a business across a gap, for example between paying a supplier and being paid by a customer.'],
        ['term' => 'Credit-only microfinance institution', 'definition' => 'A lender licensed to make loans but not to take deposits. Baardy Micro Capital is licensed as a credit-only microfinance institution by the Reserve Bank of Zimbabwe.'],
        ['term' => 'Disbursement', 'definition' => 'The release of the loan money to the borrower once the loan has been approved.'],
        ['term' => 'Early settlement', 'definition' => 'Paying off a loan before the end of its agreed term. At Baardy there is no early-settlement penalty, and you pay less interest.'],
        ['term' => 'Payslip', 'definition' => 'The statement of pay an employer gives an employee. Lenders use the latest payslip to see a regular income.'],
        ['term' => 'Principal', 'definition' => 'The amount you borrow, before interest and fees are added.'],
        ['term' => 'Repayment schedule', 'definition' => 'The agreed plan for paying a loan back: how much, and on which dates. At Baardy it can be weekly, fortnightly or monthly, and you agree it before you accept the loan.'],
        ['term' => 'Term', 'definition' => 'The length of time you have to repay a loan.'],
    ];

    $schema = [
        '@type' => 'DefinedTermSet',
        'name' => 'Loan terms explained',
        'url' => url()->current(),
        'hasDefinedTerm' => collect($terms)->map(fn (array $item): array => ['@type' => 'DefinedTerm', 'name' => $item['term'], 'description' => $item['definition']])->all(),
    ];
@endphp

@section('content')
    <x-seo.schema :data="[$schema]" />

    <x-layout.page-header
        :crumbs="[['Glossary']]"
        eyebrow="Glossary"
        title="Loan terms, in plain language"
        lead="The words you will meet when you borrow, and what each one means for you."
    />

    <x-ui.section :rule="false" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide>
            <dl class="border-t border-line">
                @foreach ($terms as $item)
                    <div data-reveal class="grid gap-3 border-b border-line py-8 lg:grid-cols-12 lg:gap-x-8">
                        <dt id="{{ Str::slug($item['term']) }}" class="scroll-mt-28 text-[length:clamp(1.25rem,1.05rem+0.7vw,1.5rem)] leading-[1.25] font-normal tracking-[-0.02em] text-ink lg:col-span-4">{{ $item['term'] }}</dt>
                        <dd class="max-w-2xl text-[1.0625rem] leading-[1.65] text-ink-soft lg:col-span-8">{{ $item['definition'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
