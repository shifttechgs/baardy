{{--
    Principles -- what the lender commits to.

    Laid out after the reference's "Aligned. Flexible." section: a two-tone
    heading left, one line and an action right, then a numbered list of rows
    divided by hairlines. It holds the slot the placeholder testimonials did.

    INTERACTION. On pointer devices a row fills with the brand purple on hover
    or keyboard focus, its detail line slides in, and a photograph of a
    borrower rises in at the right edge. Nothing is hidden from anyone who
    cannot hover: the detail is in the markup, read by screen readers, and
    shown permanently below lg where there is no hover.

    Titles marked `promise` in config come from hero.promises, so the wording
    and its qualifiers live in one place.
--}}
@php
    $principles = config('marketing.principles');
    $promises = config('marketing.hero.promises');
@endphp

<x-ui.section id="principles" :rule="false" aria-labelledby="principles-heading" class="pt-10! sm:pt-14! lg:pt-20!">
    <x-ui.container wide>

        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <h2 data-word-reveal
                id="principles-heading"
                class="text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink lg:col-span-7"
            >
                {{ $principles['heading'][0] }}<br>
                <span class="text-accent">{{ $principles['heading'][1] }}</span>
            </h2>

            <div class="flex flex-col items-start gap-5 lg:col-span-4 lg:col-start-9 lg:items-end lg:text-right">
                <p data-word-reveal class="max-w-sm text-lead text-ink-soft">{{ $principles['lead'] }}</p>

                <x-ui.button :href="$principles['action']['href']" pill class="group gap-3 pr-1.5 pl-5">
                    {{ $principles['action']['label'] }}
                    <span class="inline-flex size-8 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5">
                        <x-ui.icon name="arrow-right" />
                    </span>
                </x-ui.button>
            </div>
        </div>

        <ol data-reveal data-reveal-stagger class="mt-12 border-t border-line lg:mt-16">
            @foreach ($principles['items'] as $index => $item)
                @php
                    $title = isset($item['promise']) ? $promises[$item['promise']] : $item['title'];
                @endphp

                <li class="border-b border-line">
                    <div
                        tabindex="0"
                        class="group relative isolate grid grid-cols-[3rem_1fr] items-center gap-x-4 gap-y-2 rounded-xl px-4 py-6
                               outline-none sm:grid-cols-[4.5rem_1fr] sm:px-6 lg:grid-cols-[5rem_minmax(0,1.35fr)_minmax(0,1fr)_7rem] lg:py-6"
                    >
                        {{-- The fill: scales in from the row's left edge. --}}
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 -z-10 origin-left scale-x-0 rounded-xl bg-accent transition-transform duration-500
                                   ease-[cubic-bezier(0.65,0,0.35,1)] lg:group-hover:scale-x-100 lg:group-focus-visible:scale-x-100"
                        ></span>

                        <span class="figure-nums text-lead text-muted transition-colors duration-300 lg:group-hover:text-paper/60 lg:group-focus-visible:text-paper/60">
                            {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <h3 data-word-reveal class="text-[length:clamp(1.25rem,1rem+0.6vw,1.5rem)] leading-snug [text-wrap:pretty] font-normal tracking-[-0.015em] text-ink transition-colors duration-300 lg:group-hover:text-paper lg:group-focus-visible:text-paper">
                            {{ $title }}
                        </h3>

                        <p
                            class="col-start-2 text-body text-muted transition-all duration-500 lg:col-start-3 lg:text-small lg:translate-x-4 lg:text-paper/85 lg:opacity-0
                                   lg:group-hover:translate-x-0 lg:group-hover:opacity-100 lg:group-focus-visible:translate-x-0 lg:group-focus-visible:opacity-100"
                        >
                            {{ $item['detail'] }}
                        </p>

                        {{-- The photograph, rising in at the right edge. --}}
                        <span
                            aria-hidden="true"
                            class="hidden h-20 w-28 translate-y-3 overflow-hidden rounded-lg opacity-0 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]
                                   lg:col-start-4 lg:block lg:justify-self-end lg:group-hover:translate-y-0 lg:group-hover:opacity-100
                                   lg:group-focus-visible:translate-y-0 lg:group-focus-visible:opacity-100"
                        >
                            <img
                                src="{{ asset($item['image']) }}"
                                alt=""
                                width="224"
                                height="160"
                                loading="lazy"
                                decoding="async"
                                class="size-full scale-110 object-cover transition-transform duration-700 lg:group-hover:scale-100"
                            >
                        </span>
                    </div>
                </li>
            @endforeach
        </ol>
    </x-ui.container>
</x-ui.section>
