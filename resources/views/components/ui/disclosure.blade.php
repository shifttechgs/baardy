{{--
    Placeholder-data disclosure.

    Wherever the page shows a figure that is not real, this renders a visible
    note saying so. It exists as a component so the notice cannot drift out of
    sync between sections, and so it is trivial to find and remove every
    instance once verified data replaces the placeholders.

    See docs/COMPANY.md -- no figure on this site describes real lending.
--}}
<p {{ $attributes->merge(['class' => 'flex items-start gap-2 text-micro text-muted']) }}>
    <span aria-hidden="true" class="mt-[0.45em] size-1 shrink-0 rounded-full bg-line-strong"></span>
    <span>{{ $slot }}</span>
</p>
