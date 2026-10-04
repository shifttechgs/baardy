{{--
    /loans -- every loan, each linking to its own page.

    The hub the loan pages hang from: a card for each loan, then the same
    facts side by side so someone choosing between them can compare without
    opening six pages. Amounts and terms are the ones in config/marketing.php.
--}}
@extends('layouts.marketing')

@section('title', 'Loans in Zimbabwe: Salary, School Fees, Farming and SME | Baardy')

@section('description', 'Salary-based, educational, agricultural, women and youth empowerment loans and SME bridging finance from a licensed Zimbabwean lender, with branches in Harare and Bulawayo.')

@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => 'Loans from '.config('company.legal_name'),
        'url' => url()->current(),
        'hasPart' => $loans->map(fn (array $loan): array => ['@type' => 'Service', 'name' => $loan['name'], 'url' => route('loans.show', $loan['slug'])])->all(),
    ];
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <x-layout.page-header
        eyebrow="Loans"
        title="Six loans, matched to how you earn"
        lead="Each has its own range, term and paperwork, and every cost is in writing before you commit."
        image="images/hero/market-2400.webp"
        alt="A produce market in Zimbabwe, the people Baardy lends to"
        position="50% 60%"
    />

    <x-ui.section :rule="false" aria-label="All loans" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide>
            <div data-reveal-stagger class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 lg:gap-4">
                @foreach ($loans as $loan)
                    <x-ui.loan-card :loan="$loan" :promo="$promotions[$loan['name']] ?? null" />
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-ui.section :rule="false" tone="mist" aria-labelledby="compare-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="compare-heading"
                eyebrow="Compare"
                title="The loans side by side"
                lead="Your own limit is set when your application is assessed."
            />

            <div data-reveal class="mt-12 overflow-x-auto rounded-2xl bg-paper lg:mt-16">
                <table class="w-full min-w-[40rem] text-left text-body">
                    <caption class="sr-only">Amount, term and who each loan is for</caption>
                    <thead>
                        <tr class="border-b border-line text-small text-muted">
                            <th scope="col" class="px-6 py-4 font-medium">Loan</th>
                            <th scope="col" class="px-6 py-4 font-medium">Amount</th>
                            <th scope="col" class="px-6 py-4 font-medium">Term</th>
                            <th scope="col" class="px-6 py-4 font-medium">Best for</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr class="border-b border-line last:border-b-0">
                                <th scope="row" class="px-6 py-4 font-medium text-ink">
                                    <a href="{{ route('loans.show', $loan['slug']) }}" class="transition-colors hover:text-accent">{{ $loan['name'] }}</a>
                                </th>
                                <td class="figure-nums px-6 py-4 whitespace-nowrap text-ink-soft"><x-ui.money :amount="$loan['min']" /> &ndash; <x-ui.money :amount="$loan['max']" /></td>
                                <td class="figure-nums px-6 py-4 whitespace-nowrap text-ink-soft">{{ $loan['term'] }}</td>
                                <td class="px-6 py-4 text-ink-soft">{{ $loan['best_for'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
