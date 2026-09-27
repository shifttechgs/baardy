{{--
    A single aggregate figure in the trust strip.

    The figure leads, the label explains it, and the note qualifies the basis
    of measurement -- which is what makes a statistic credible rather than
    decorative.

    Props
      value     the figure itself, pre-formatted
      label     what it measures
      note      the basis of measurement
      currency  prefix the figure with the configured currency symbol
--}}
@props([
    'value' => '',
    'label' => '',
    'note' => null,
    'currency' => false,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-2']) }}>
    {{-- Symbol and value must stay on one source line. A newline between them
         renders as a space, which reads as "$ 310m". It went unnoticed while
         the symbol was the placeholder currency sign. --}}
    <p class="figure-nums text-h2 leading-none font-medium text-ink">
        @if ($currency)<span class="currency">{{ config('company.currency.symbol') }}</span>@endif{{ $value }}
    </p>

    <p class="text-body font-medium text-ink-soft">
        {{ $label }}
    </p>

    @if ($note)
        <p class="text-small text-muted">
            {{ $note }}
        </p>
    @endif
</div>
