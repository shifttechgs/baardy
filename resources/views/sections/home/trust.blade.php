{{--
    Standing -- the section under the hero.

    Laid out after the reference (siwacap.com): a photograph on the left with
    a glass card over it, and on the right a short statement, one link, and
    three large figures sitting level with the photograph's foot.

    Every figure is true and computed from config (see marketing.trust), so
    nothing here needs an "illustrative" disclaimer, and the one link goes to
    the public register the licence can be checked on. This section also
    carries what the "proof" section used to, which is why that section is no
    longer on the homepage -- one home for each fact.

    MOTION
      photograph   unmasks upward and settles from a slight zoom (.reveal-media)
      glass card   rises in once the photograph has landed
      statement    lights word by word as it is scrolled ([data-word-reveal])
      figures      blur in one after another and count up from zero
                   ([data-count-on-view], app.js)
    Reduced motion: everything is simply there.
--}}
@php
    $trust = config('marketing.trust');
    $compliance = config('company.compliance');

    $figures = [
        'years' => now()->year - (int) $compliance['licensed_since'],
        'since' => (int) $compliance['licensed_since'],
        'licence' => (int) $compliance['licence'],
        'products' => count(config('marketing.products')),
        'branches' => count(config('company.branches')),
    ];

    $imageSources = $trust['image']['sources'];
    $srcset = collect($imageSources)
        ->map(fn (int $width, string $path): string => asset($path).' '.$width.'w')
        ->implode(', ');
@endphp

<x-ui.section id="trust" :rule="false" aria-labelledby="trust-heading" class="relative overflow-hidden pb-12! sm:pb-14! lg:pb-16!">

    {{-- The soft wave behind the figures, as in the reference. Decorative. --}}
    <svg
        aria-hidden="true"
        viewBox="0 0 1440 520"
        preserveAspectRatio="none"
        class="pointer-events-none absolute inset-x-0 bottom-0 z-0 h-[70%] w-full text-mist [mask-image:linear-gradient(to_bottom,black_35%,transparent)]"
    >
        <path fill="currentColor" fill-opacity="0.9" d="M0 420C220 330 420 300 640 330s420 110 800-40v230H0Z" />
        <path fill="currentColor" fill-opacity="0.6" d="M0 300C260 380 520 240 800 250s460 120 640 40v230H0Z" />
    </svg>

    <x-ui.container wide class="relative">
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-x-10">

            {{-- The photograph, with the register card over it --}}
            <figure data-reveal class="reveal-media relative overflow-hidden rounded-xl bg-ink lg:col-span-4 min-[85rem]:col-span-5">
                <img
                    src="{{ asset(array_key_last($imageSources)) }}"
                    srcset="{{ $srcset }}"
                    sizes="(min-width: 1024px) 40vw, 100vw"
                    width="{{ max($imageSources) }}"
                    height="{{ max($imageSources) }}"
                    alt="{{ $trust['image']['alt'] }}"
                    loading="lazy"
                    decoding="async"
                    class="reveal-media-img aspect-square size-full object-cover lg:aspect-[15/16]"
                >

                <div aria-hidden="true" class="absolute inset-0 bg-linear-to-t from-ink/70 via-ink/10 to-transparent"></div>

                <figcaption
                    class="reveal-card absolute bottom-5 left-5 w-[min(20rem,calc(100%-2.5rem))] rounded-[0.625rem] bg-paper/10 p-6
                           text-paper ring-1 ring-paper/20 ring-inset backdrop-blur-xl sm:bottom-8 sm:left-8"
                >
                    <p class="text-h3 font-normal tracking-[-0.015em]">
                        {{ $trust['card']['title'] }}
                    </p>

                    <dl class="mt-5 grid grid-cols-2 gap-6">
                        @foreach ($trust['card']['facts'] as $fact)
                            <div class="flex flex-col">
                                <dt class="order-2 mt-1 text-small text-paper/85">{{ $fact['label'] }}</dt>
                                <dd class="figure-nums order-1 text-[2rem] leading-none tracking-[-0.03em]">
                                    @isset ($fact['prefix'])<span class="text-lead text-paper/70">{{ $fact['prefix'] }}</span>@endisset<span data-count-to="{{ $figures[$fact['key']] }}" data-count-on-view>{{ $figures[$fact['key']] }}</span>
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </figcaption>
            </figure>

            {{-- The statement, the link, and the figures --}}
            <div class="flex flex-col justify-between gap-14 lg:col-span-8 lg:col-start-5 lg:py-2 min-[85rem]:col-span-6 min-[85rem]:col-start-7">
                <div>
                    <h2
                        id="trust-heading"
                        data-word-reveal
                        class="max-w-[22ch] text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink"
                    >{{ $trust['heading'] }}</h2>

                    <p data-word-reveal class="mt-6 text-body leading-relaxed text-ink-soft min-[85rem]:text-[1.0625rem]">{{ $trust['body'] }}</p>

                    <a
                        href="{{ $compliance['register_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group mt-10 inline-flex items-center gap-2 border-b border-ink pb-1.5 text-small font-medium text-ink
                               transition-colors hover:border-accent hover:text-accent"
                    >
                        {{ $trust['link'] }}
                        <x-ui.icon
                            name="arrow-up-right"
                            class="transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                        />
                    </a>
                </div>

                <dl data-reveal data-reveal-stagger class="reveal-blur grid grid-cols-3 gap-6">
                    @foreach ($trust['facts'] as $fact)
                        <div class="flex flex-col">
                            <dt class="order-2 mt-4 text-small text-ink-soft">{{ $fact['label'] }}</dt>
                            <dd class="figure-nums order-1 text-[length:clamp(2.75rem,1.4rem+3.4vw,4.5rem)] leading-none font-normal tracking-[-0.04em] text-accent">
                                <span data-count-to="{{ $figures[$fact['key']] }}" data-count-on-view>{{ $figures[$fact['key']] }}</span>{{ $fact['suffix'] ?? '' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
