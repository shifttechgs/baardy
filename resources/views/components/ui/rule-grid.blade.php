{{--
    The bordered grid.

    The page's card groups are built as ruled grids -- the way a term sheet or
    a statement is ruled -- rather than as separate floating cards. A 1px gap
    over a line-coloured background draws the interior rules, which avoids
    doubled borders entirely and keeps every rule exactly one pixel.

    Cells are <x-ui.rule-cell> (or any element with a paper background).

    Props
      cols  responsive column classes for the grid
--}}
@props(['cols' => 'sm:grid-cols-2 lg:grid-cols-4'])

<div {{ $attributes->merge([
    'class' => 'grid gap-px border-y border-line bg-line '.$cols,
]) }}>
    {{ $slot }}
</div>
