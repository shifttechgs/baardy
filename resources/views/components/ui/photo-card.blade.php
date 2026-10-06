{{--
    A card on a photograph, its content on a frosted panel: the treatment the
    homepage uses (why-us, testimonials), as one component for the inner pages.

    The photograph fills the card and the panel sits at the foot of it. The
    panel's /50 fill keeps the text readable where backdrop-filter is
    unsupported; the blur is the upgrade. Extra top padding on the card (pt-*)
    leaves more of the photograph showing above the panel.

    Props
      image     path under public/, e.g. images/products/salary-1600.webp
      alt       describes the photograph; empty (decorative) by default
      position  object-position for the photograph
      panel     extra classes for the frosted panel
--}}
@props(['image', 'alt' => '', 'position' => '50% 40%', 'panel' => ''])

<div {{ $attributes->class('group/photo relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-ink p-3 text-paper sm:p-4') }}>
    <img
        src="{{ asset($image) }}"
        alt="{{ $alt }}"
        width="1600"
        height="1067"
        loading="lazy"
        decoding="async"
        class="absolute inset-0 -z-10 size-full object-cover transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/photo:scale-[1.04]"
        style="object-position: {{ $position }}"
    >
    <div aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-t from-ink/50 via-ink/0 via-60% to-ink/0"></div>

    <div class="{{ trim('flex flex-col gap-6 rounded-xl bg-ink/50 p-5 backdrop-blur-xl backdrop-saturate-150 sm:p-6 '.$panel) }}">
        {{ $slot }}
    </div>
</div>
