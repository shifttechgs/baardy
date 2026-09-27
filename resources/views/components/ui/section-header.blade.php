{{--
    The section header pattern used across the page.

    An asymmetric two-column header: the section's name sits in a narrow left
    column, the heading and introduction in a wider right column. The offset is
    the editorial device that gives the page its rhythm -- no all-caps eyebrow,
    no centred stack.

    Props
      label    the section's name, in sentence case
      heading  the section heading (rendered as <h2> by default)
      level    heading tag, for keeping the document outline correct
--}}
@props([
    'label' => null,
    'heading' => null,
    'level' => 'h2',
])

<div data-reveal {{ $attributes->merge(['class' => 'grid gap-x-10 gap-y-6 lg:grid-cols-12']) }}>
    @if ($label)
        <p class="text-body font-medium text-accent lg:col-span-3">
            {{ $label }}
        </p>
    @endif

    <div class="{{ $label ? 'lg:col-span-9' : 'lg:col-span-12' }} max-w-3xl">
        @if ($heading)
            <{{ $level }} class="text-h2 font-medium tracking-[-0.025em] text-ink optical-left">
                {{ $heading }}
            </{{ $level }}>
        @endif

        @if (filled(trim($slot)))
            <div class="mt-5 max-w-2xl text-lead text-muted">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
