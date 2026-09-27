{{--
    A monetary figure.

    Every amount on the site renders through this component so that the
    currency symbol, grouping and numeral styling are defined once. Figures use
    tabular numerals so columns of amounts align and values do not shift width
    as they change.

    Props
      amount    integer, in major currency units
      symbol    override the configured currency symbol
--}}
@props([
    'amount' => 0,
    'symbol' => null,
])

@php
    $symbol ??= config('company.currency.symbol');
@endphp

<span {{ $attributes->merge(['class' => 'figure-nums whitespace-nowrap']) }}>
    <span class="currency">{{ $symbol }}</span>{{ number_format((float) $amount) }}
</span>
