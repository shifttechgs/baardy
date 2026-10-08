{{--
    A customer story on a big brand-colour card, its words on a frosted panel:
    a short title, the story, an optional closing line, who it is about, and a
    way into the enquiry form.

    The card carries the customer's own photograph when the story has one
    (with their consent). Without one it is brand colour with soft shapes, so
    nothing implies a photograph of the customer.

    A story may also carry an `illustration` ([src, alt, position]): a photograph
    that sets the scene without being of the customer. On a wide card it fills
    the space beside the panel. Choose one with no identifiable face, so it is
    not taken for the customer.

    Only ever given a consented story (see sections/home/testimonials). A
    story's `pull` is a closing line set apart under the paragraphs; blank
    lines in `story` start a new paragraph.

    Props
      story  a customer story from marketing.customer_stories
      url    where the button goes
      wide   a banner: the panel keeps a readable width and sits at the left
--}}
@props(['story', 'url', 'wide' => false])

<figure {{ $attributes->class(['group/story relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden rounded-2xl bg-accent text-paper', 'lg:min-h-[30rem] lg:flex-row lg:items-stretch lg:justify-start' => $wide]) }}>
    @if (filled($story['photo'] ?? null))
        <img
            src="{{ asset($story['photo']) }}"
            alt=""
            width="1600"
            height="1067"
            loading="lazy"
            decoding="async"
            class="absolute inset-0 -z-20 size-full object-cover transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/story:scale-[1.04]"
        >
        <span aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-t from-ink/55 via-ink/10 to-transparent"></span>
    @else
        <span aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-br from-accent via-accent to-accent-strong"></span>
        <span aria-hidden="true" class="absolute -top-24 -right-20 -z-10 size-80 rounded-full bg-paper/[0.07]"></span>
        <span aria-hidden="true" class="absolute top-24 -left-16 -z-10 size-56 rounded-full bg-paper/[0.05]"></span>
        <span aria-hidden="true" class="absolute right-10 bottom-1/3 -z-10 size-24 rounded-full bg-highlight/25"></span>
    @endif

    <div @class(['m-3 flex flex-col gap-5 rounded-xl bg-ink/30 p-5 backdrop-blur-xl backdrop-saturate-150 sm:m-4 sm:p-7', 'lg:m-6 lg:max-w-2xl lg:p-9' => $wide])>
        @isset ($story['loan'])
            <span class="w-fit rounded-full bg-paper/15 px-3 py-1 text-micro font-medium">{{ $story['loan'] }}</span>
        @endisset
        @isset ($story['title'])
            <h3 class="text-[length:clamp(1.5rem,1.1rem+1.2vw,2rem)] leading-[1.2] font-medium tracking-[-0.02em] text-balance">{{ $story['title'] }}</h3>
        @endisset
        <div class="flex flex-col gap-3 text-body leading-[1.5] text-paper/90">
            @foreach (preg_split('/\n{2,}/', $story['story']) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
        @isset ($story['pull'])
            <p class="border-t border-paper/20 pt-4 text-body leading-[1.4] font-medium">{{ $story['pull'] }}</p>
        @endisset
        <figcaption class="flex flex-col">
            <span class="text-body">{{ $story['name'] }}</span>
            <span class="text-small text-paper/75">{{ $story['role'] }}</span>
        </figcaption>
        <x-ui.button :href="$url" variant="inverse" arrow class="sm:w-fit">
            {{ isset($story['loan']) ? 'Enquire about this loan' : 'Talk to us about a loan' }}
        </x-ui.button>
    </div>

    @if ($wide && filled($story['illustration']['src'] ?? null))
        <div class="relative m-3 hidden min-h-64 flex-1 overflow-hidden rounded-xl bg-ink/20 lg:m-6 lg:ml-0 lg:block">
            <img
                src="{{ asset($story['illustration']['src']) }}"
                alt="{{ $story['illustration']['alt'] ?? '' }}"
                width="2400"
                height="1602"
                loading="lazy"
                decoding="async"
                class="absolute inset-0 size-full object-cover"
                style="object-position: {{ $story['illustration']['position'] ?? '50% 50%' }}"
            >
        </div>
    @endif
</figure>
