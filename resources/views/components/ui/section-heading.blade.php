{{--
    Section heading -- the pattern every section and page uses (after
    trova-travel.framer.website): an eyebrow, a 48px heading on the left, and
    one short sentence on the right, aligned to the heading's foot.

    Props
      eyebrow  the label over the heading
      title    the heading
      lead     the sentence on the right (optional)
      id       id for the heading, for aria-labelledby on the section
      tag      'h2' (default) or 'h1'
--}}
@props(['eyebrow', 'title', 'lead' => null, 'id' => null, 'tag' => 'h2'])

<div data-reveal {{ $attributes->merge(['class' => 'grid gap-6 lg:grid-cols-12 lg:items-end']) }}>
    <div class="lg:col-span-7">
        <x-ui.eyebrow>{{ $eyebrow }}</x-ui.eyebrow>
        <{{ $tag }}
            @if ($id) id="{{ $id }}" @endif
            class="mt-5 text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink"
        >{{ $title }}</{{ $tag }}>
    </div>

    @if (filled($lead))
        <p class="max-w-sm text-body leading-[1.4] text-ink-soft lg:col-span-4 lg:col-start-9 lg:ml-auto lg:text-right">{{ $lead }}</p>
    @endif
</div>
