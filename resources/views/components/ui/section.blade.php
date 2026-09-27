{{--
    A page section.

    Sections carry the vertical rhythm (80 / 120 / 160px) and the hairline rule
    that separates them. The rule is the page's primary structural device --
    sections are divided by a line, not by alternating background colours.

    It is on by default and switched off in three places:

      hero      first section on the page, so there is nothing to divide from
      products  the scroll-driven rail sets its own top edge
      cta       an inset dark panel that provides its own separation

    Props
      id       anchor target for in-page navigation
      rule     draw the top hairline
      tone     'paper' (default) or 'mist' for the rare tinted band
      size     'default' | 'tight'
--}}
@props([
    'id' => null,
    'rule' => true,
    'tone' => 'paper',
    'size' => 'default',
])

@php
    $padding = $size === 'tight'
        ? 'py-14 sm:py-16 lg:py-20'
        : 'py-20 sm:py-28 lg:py-40';

    $classes = [
        $padding,
        $tone === 'mist' ? 'bg-mist' : 'bg-paper',
        $rule ? 'border-t border-line' : '',
    ];
@endphp

<section
    @if ($id) id="{{ $id }}" @endif
    {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}
>
    {{ $slot }}
</section>
