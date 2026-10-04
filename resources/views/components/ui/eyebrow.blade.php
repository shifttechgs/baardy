{{--
    Eyebrow -- the small label over a heading: an arrow, then the words
    ("> About us"). Used by every section heading and page header so the
    whole site labels things the same way.
--}}
<p {{ $attributes->merge(['class' => 'flex items-center gap-1.5 text-body text-ink']) }}>
    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-3.5"><path d="m6 3.5 4.5 4.5L6 12.5" /></svg>
    {{ $slot }}
</p>
