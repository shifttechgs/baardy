{{--
    Repayment estimate -- INTERFACE ONLY.

    ---------------------------------------------------------------------------
    THERE IS NO REPAYMENT CALCULATION IN THIS PROJECT, BY DESIGN.

    Alpine here holds input state and nothing else: it echoes back the amount
    and term the visitor selected. It does not compute anything, because any
    formula would need an interest rate and a fee schedule that have not been
    set, and publishing an invented repayment figure on a lending site is a
    regulatory problem, not a placeholder.

    The repayment field therefore renders a pending state rather than a
    fabricated number. That is also how a careful lender behaves: the figure is
    confirmed after affordability review, in writing.

    TO CONNECT REAL LOGIC LATER:
      - post the amount / term / product to a server-side quote endpoint
      - render the returned figure into [data-repayment-output]
      - keep the "not a quote" disclosure until a binding offer is issued
    ---------------------------------------------------------------------------
--}}
@php
    $calc = config('marketing.calculator');
    $symbol = config('company.currency.symbol');
@endphp

<x-ui.section id="calculator">
    <x-ui.container>
        <x-ui.section-header
            label="Estimate"
            heading="Work out the shape of a facility before you apply"
        >
            Set an amount and a term to see how a facility would be structured. The cost is
            confirmed after review, so nothing here is a quote or an offer of credit.
        </x-ui.section-header>

        <div
            x-data="{
                amount: {{ $calc['amount']['default'] }},
                term: {{ $calc['default_term'] }},
                purpose: '{{ $calc['purposes'][0] }}',
                get formattedAmount() {
                    return this.amount.toLocaleString('en-US');
                },
            }"
            class="mt-14 overflow-hidden rounded-lg border border-line lg:mt-20"
        >
            <div class="grid lg:grid-cols-12">

                {{-- Inputs --}}
                <div class="flex flex-col gap-10 bg-paper p-8 sm:p-12 lg:col-span-7">

                    <x-ui.field id="loan-amount" label="How much do you need?">
                        <output
                            for="loan-amount"
                            class="figure-nums block text-h2 font-medium text-ink"
                        >
                            <span class="currency">{{ $symbol }}</span><span x-text="formattedAmount">{{ number_format($calc['amount']['default']) }}</span>
                        </output>

                        <input
                            id="loan-amount"
                            type="range"
                            class="range mt-2"
                            min="{{ $calc['amount']['min'] }}"
                            max="{{ $calc['amount']['max'] }}"
                            step="{{ $calc['amount']['step'] }}"
                            x-model.number="amount"
                            x-bind:aria-valuetext="'{{ $symbol }}' + formattedAmount"
                            aria-describedby="loan-amount-hint"
                        >

                        <div class="flex justify-between text-micro text-muted">
                            <span class="figure-nums"><span class="currency">{{ $symbol }}</span>{{ number_format($calc['amount']['min']) }}</span>
                            <span class="figure-nums"><span class="currency">{{ $symbol }}</span>{{ number_format($calc['amount']['max']) }}</span>
                        </div>

                        <x-slot:hint>
                            Drag to set an amount. Larger facilities are available on application.
                        </x-slot:hint>
                    </x-ui.field>

                    {{-- Term: a radio group, so arrow keys work and the choice is announced --}}
                    <fieldset class="flex flex-col gap-3">
                        <legend class="text-small font-medium text-ink">Over how long?</legend>

                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach ($calc['terms'] as $term)
                                <label
                                    class="cursor-pointer rounded-sm border px-4 py-2.5 text-small font-medium
                                           transition-colors has-[:focus-visible]:outline has-[:focus-visible]:outline-2
                                           has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                    x-bind:class="term === {{ $term }}
                                        ? 'border-accent bg-accent-tint text-accent'
                                        : 'border-line-strong text-ink-soft hover:border-ink'"
                                >
                                    <input
                                        type="radio"
                                        name="loan-term"
                                        value="{{ $term }}"
                                        x-model.number="term"
                                        class="sr-only"
                                    >
                                    <span class="figure-nums">{{ $term }} months</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <x-ui.field id="loan-purpose" label="What is it for?">
                        <select
                            id="loan-purpose"
                            x-model="purpose"
                            class="h-12 w-full rounded-sm border border-line-strong bg-paper px-4 text-body
                                   text-ink transition-colors hover:border-ink"
                        >
                            @foreach ($calc['purposes'] as $purpose)
                                <option value="{{ $purpose }}">{{ $purpose }}</option>
                            @endforeach
                        </select>
                    </x-ui.field>
                </div>

                {{-- Summary --}}
                <div class="flex flex-col gap-8 border-t border-line bg-mist p-8 sm:p-12 lg:col-span-5 lg:border-t-0 lg:border-l">
                    <h3 class="text-small font-medium text-ink">Your estimate</h3>

                    <dl class="flex flex-col divide-y divide-line border-y border-line">
                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="text-small text-muted">Amount</dt>
                            <dd class="figure-nums text-body font-medium text-ink">
                                <span class="currency">{{ $symbol }}</span><span x-text="formattedAmount"></span>
                            </dd>
                        </div>

                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="text-small text-muted">Term</dt>
                            <dd class="figure-nums text-body font-medium text-ink">
                                <span x-text="term"></span> months
                            </dd>
                        </div>

                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="text-small text-muted">Purpose</dt>
                            <dd class="text-body font-medium text-ink" x-text="purpose"></dd>
                        </div>
                    </dl>

                    {{-- Pending until a real quote endpoint exists. See the note above. --}}
                    <div class="flex flex-col gap-2">
                        <p class="text-small text-muted">Estimated repayment</p>

                        <p
                            data-repayment-output
                            class="figure-nums text-h2 leading-none font-medium text-line-strong"
                            aria-describedby="repayment-pending"
                        >
                            &mdash;
                        </p>

                        <p id="repayment-pending" class="text-small text-muted">
                            Your repayment is calculated during review and confirmed in your
                            written offer, with every fee included.
                        </p>
                    </div>

                    <div class="mt-auto flex flex-col gap-4">
                        <x-ui.button :href="config('company.cta.primary.href')" size="lg" class="w-full">
                            {{ config('company.cta.primary.label') }}
                        </x-ui.button>

                        <x-ui.disclosure>
                            Not a quote and not an offer of credit. Lending is subject to
                            affordability assessment and approval.
                        </x-ui.disclosure>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
