{{--
    A cell inside <x-ui.rule-grid>. Supplies the paper background that masks
    the grid's line-coloured gaps, plus consistent internal padding.

    Props
      padding  override the default cell padding
--}}
@props(['padding' => 'p-8 lg:p-10'])

<div {{ $attributes->merge(['class' => 'bg-paper '.$padding]) }}>
    {{ $slot }}
</div>
