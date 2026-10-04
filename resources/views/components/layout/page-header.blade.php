{{--
    Page header -- the top of every inner page, after trova-travel.framer.website.

    An eyebrow and a large two-line heading on the left, one short sentence
    right-aligned at the foot of the heading, and under them one wide rounded
    photograph (optional). Sits on the flat header; the layout reserves the
    header's height above it.

    Props
      eyebrow    the label over the heading
      title      the <h1>
      lead       the sentence on the right
      image      published path of a wide photograph (optional)
      alt        its description
      position   its object-position
      width      its pixel width / height, for layout stability
--}}
@props([
    'eyebrow',
    'title',
    'lead' => null,
    'image' => null,
    'alt' => '',
    'position' => '50% 50%',
    'width' => 2400,
    'height' => 1600,
])

<section aria-labelledby="page-title" @class(['bg-paper pt-6 sm:pt-8', 'pb-14 lg:pb-20' => $image, 'pb-10 lg:pb-14' => ! $image])>
    <x-ui.container wide>
        <div class="grid gap-6 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-8">
                <x-ui.eyebrow class="rise [animation-delay:60ms]">{{ $eyebrow }}</x-ui.eyebrow>
                <h1
                    id="page-title"
                    class="rise mt-5 text-[length:clamp(2.5rem,4.45vw,4rem)] leading-[1.05] font-normal tracking-[-0.03em] text-balance text-ink [animation-delay:120ms]"
                >{{ $title }}</h1>
            </div>

            @if (filled($lead))
                <p class="rise max-w-sm text-body leading-[1.4] text-ink-soft [animation-delay:200ms] lg:col-span-4 lg:ml-auto lg:text-right">{{ $lead }}</p>
            @endif
        </div>

        @if ($image)
            <figure class="rise mt-10 overflow-hidden rounded-2xl bg-mist [animation-delay:280ms] lg:mt-14">
                <img
                    src="{{ asset($image) }}"
                    alt="{{ $alt }}"
                    width="{{ $width }}"
                    height="{{ $height }}"
                    fetchpriority="high"
                    decoding="async"
                    class="aspect-[4/3] w-full object-cover sm:aspect-[16/8] lg:aspect-[16/7]"
                    style="object-position: {{ $position }}"
                >
            </figure>
        @endif

        {{ $slot }}
    </x-ui.container>
</section>
