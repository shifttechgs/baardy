{{--
    Visit us -- where the offices are.

    ONE PANEL, TWO BRANCHES. A segmented switch (Harare | Bulawayo) sits on top
    of a single tinted panel. Beneath it, the chosen branch's details are on
    the left -- address, its own numbers, and the actions -- and its map is on
    the right. Switching cross-fades both. Nothing else on the page changes.

    DETAILS. The address links to Google Maps. A number labelled WhatsApp in
    config also gets a WhatsApp link; the rest are call links. The main
    action carries the reader on to the enquiry form with that branch already
    chosen (`branchLink`, app.js); directions are the quiet second action.

    MAP. Google's keyless embed, greyscale with a brand tint, so it sits in the
    palette. It is asked for by building and street (config `map_query`), not
    the floor and office. Under it is a quiet loading state, so the panel never
    looks broken while Google's page arrives; a frosted note over its foot names
    the branch and opens it in Google Maps.

    The Bulawayo building name is flagged in config as needing the client's
    confirmation before launch.
--}}
@php
    $offices = config('company.branches');
@endphp

<x-ui.section
    id="visit"
    :rule="false"
    :aria-labelledby="($showHeading ?? true) ? 'visit-heading' : null"
    :aria-label="($showHeading ?? true) ? null : 'Our offices'"
    @class(['scroll-mt-24 py-16! sm:py-20! lg:py-24!', 'pt-0!' => ! ($showHeading ?? true)])
>
    <x-ui.container wide>
        @if ($showHeading ?? true)
            <x-ui.section-heading
                id="visit-heading"
                eyebrow="Visit us"
                title="Come and see us in person"
                lead="Walk in to either office and talk to a person. No appointment is needed to ask about a loan."
            />
        @endif

        <div
            x-data="{ active: 0 }"
            data-reveal
            @class(['rounded-3xl bg-mist p-3 sm:p-4', 'mt-10 lg:mt-14' => ($showHeading ?? true)])
        >
            {{-- The switch --}}
            <div role="tablist" aria-label="Choose an office" class="flex w-full gap-1 rounded-xl bg-paper p-1 sm:w-fit">
                @foreach ($offices as $office)
                    <button
                        type="button"
                        role="tab"
                        id="office-tab-{{ $loop->index }}"
                        aria-controls="office-panel-{{ $loop->index }}"
                        x-on:click="active = {{ $loop->index }}"
                        x-bind:aria-selected="active === {{ $loop->index }} ? 'true' : 'false'"
                        x-bind:class="active === {{ $loop->index }} ? 'bg-accent text-paper shadow-sm' : 'text-ink-soft hover:text-ink'"
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-body font-medium transition-colors duration-300 sm:flex-none sm:px-7"
                    >
                        {{ $office['name'] }}
                        <span class="hidden text-small font-normal opacity-70 sm:inline">{{ $office['role'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="mt-3 grid gap-3 lg:grid-cols-12 lg:gap-4">

                {{-- Details of the chosen branch --}}
                <div class="grid lg:col-span-5">
                    @foreach ($offices as $office)
                        @php
                            $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($office['map_query'] ?? implode(', ', $office['address']));
                        @endphp
                        <article
                            id="office-panel-{{ $loop->index }}"
                            role="tabpanel"
                            aria-labelledby="office-tab-{{ $loop->index }}"
                            x-show="active === {{ $loop->index }}"
                            x-transition:enter="transition duration-500 ease-out"
                            x-transition:enter-start="translate-y-2 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            @if (! $loop->first) x-cloak @endif
                            class="flex flex-col gap-7 rounded-2xl bg-paper p-6 [grid-area:1/1] sm:p-8"
                        >
                            <div class="flex flex-col gap-1">
                                <p class="text-small text-accent">{{ $office['role'] }}</p>
                                <h3 class="text-[length:clamp(1.75rem,1.3rem+1.4vw,2.25rem)] leading-[1.1] tracking-[-0.02em] text-ink">{{ $office['name'] }}</h3>
                            </div>

                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="group/address flex gap-3 rounded-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                                <span class="mt-0.5 inline-flex size-9 shrink-0 items-center justify-center rounded-lg bg-accent-tint text-accent">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-5">
                                        <path d="M10 18s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" />
                                        <circle cx="10" cy="8" r="2.2" />
                                    </svg>
                                </span>
                                <span class="flex flex-col gap-1.5">
                                    <address class="text-body leading-snug text-ink not-italic transition-colors group-hover/address:text-accent">
                                        @foreach ($office['address'] as $line)
                                            <span class="block">{{ $line }}</span>
                                        @endforeach
                                    </address>
                                    <span class="inline-flex items-center gap-1.5 text-small text-accent">
                                        View on Google Maps <x-ui.icon name="arrow-up-right" class="size-3.5" />
                                    </span>
                                </span>
                            </a>

                            <ul class="flex flex-col divide-y divide-line border-y border-line">
                                @foreach ($office['phones'] as $phone)
                                    <li class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 py-3">
                                        <a href="tel:{{ $phone['tel'] }}" class="inline-flex items-center gap-3 text-body text-ink transition-colors hover:text-accent">
                                            <x-ui.icon name="phone" class="size-4 text-accent" />
                                            <span class="figure-nums">{{ $phone['display'] }}</span>
                                            <span class="text-small text-muted">{{ $phone['label'] }}</span>
                                        </a>
                                        @if (str_contains($phone['label'], 'WhatsApp'))
                                            <x-ui.whatsapp-cta :number="$phone['tel']" :label="'WhatsApp '.$office['name']" tone="mist" />
                                        @endif
                                    </li>
                                @endforeach
                            </ul>

                            <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                                {{-- On this page it scrolls to the enquiry form with this branch
                                     already chosen; elsewhere it opens the contact page there. --}}
                                <x-ui.button
                                    :href="route('contact', ['branch' => $office['name']]).'#contact'"
                                    x-data="branchLink('{{ $office['name'] }}')"
                                    x-on:click="go($event)"
                                    arrow
                                >
                                    Message the {{ $office['name'] }} branch
                                </x-ui.button>

                                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-body text-ink-soft transition-colors hover:text-accent">
                                    Get directions
                                    <x-ui.icon name="arrow-up-right" class="size-4" />
                                    <span class="sr-only">to the {{ $office['name'] }} office (opens in a new tab)</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- The map of the chosen branch --}}
                <div class="relative isolate h-[24rem] overflow-hidden rounded-2xl bg-line lg:col-span-7 lg:h-auto lg:min-h-[34rem]">
                    {{-- Under the map: a quiet loading state, so the panel never looks broken
                         while Google's page arrives. The map covers it once it paints. --}}
                    <div aria-hidden="true" class="absolute inset-0 -z-10 flex flex-col items-center justify-center gap-3 bg-linear-to-br from-accent-tint to-line text-accent/70">
                        <span class="relative flex size-14 items-center justify-center rounded-full bg-paper/80">
                            <span class="absolute inset-0 animate-ping rounded-full bg-paper/60 [animation-duration:2s] motion-reduce:hidden"></span>
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="relative size-6">
                                <path d="M10 18s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" />
                                <circle cx="10" cy="8" r="2.2" />
                            </svg>
                        </span>
                        <span class="text-small">Loading map&hellip;</span>
                    </div>

                    @foreach ($offices as $office)
                        <iframe
                            title="Map of the {{ $office['name'] }} office"
                            src="https://www.google.com/maps?q={{ rawurlencode($office['map_query'] ?? implode(', ', $office['address'])) }}&z=16&output=embed"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                            class="absolute inset-0 size-full border-0 grayscale contrast-[1.05] saturate-0 transition-opacity duration-700"
                            x-bind:class="active === {{ $loop->index }} ? 'opacity-100' : 'pointer-events-none opacity-0'"
                            x-bind:aria-hidden="active === {{ $loop->index }} ? 'false' : 'true'"
                            x-bind:tabindex="active === {{ $loop->index }} ? 0 : -1"
                        ></iframe>
                    @endforeach

                    {{-- The brand tint, over the greyscale map; clicks pass through. --}}
                    <span aria-hidden="true" class="pointer-events-none absolute inset-0 bg-accent/25 mix-blend-multiply"></span>
                    <span aria-hidden="true" class="pointer-events-none absolute inset-0 shadow-[inset_0_0_120px_0_rgb(21_16_25/0.22)]"></span>

                    {{-- The branch on show, on frosted glass. --}}
                    <div class="pointer-events-none absolute inset-x-3 bottom-3 sm:inset-x-4 sm:bottom-4">
                        @foreach ($offices as $office)
                            @php
                                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($office['map_query'] ?? implode(', ', $office['address']));
                            @endphp
                            <div
                                x-show="active === {{ $loop->index }}"
                                x-transition:enter="transition duration-500 ease-out"
                                x-transition:enter-start="translate-y-3 opacity-0"
                                x-transition:enter-end="translate-y-0 opacity-100"
                                x-transition:leave="transition duration-200 ease-in absolute inset-x-0 bottom-0"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                @if (! $loop->first) x-cloak @endif
                                class="pointer-events-auto flex flex-wrap items-center justify-between gap-3 rounded-xl bg-ink/55 p-3.5 text-paper backdrop-blur-xl backdrop-saturate-150 sm:p-4"
                            >
                                <span class="flex flex-col">
                                    <span class="text-body font-medium">{{ $office['name'] }} &middot; {{ $office['role'] }}</span>
                                    <span class="text-small text-paper/75">{{ $office['address'][1] }}</span>
                                </span>
                                <x-ui.button :href="$mapsUrl" target="_blank" rel="noopener noreferrer" variant="inverse" arrow>
                                    Open in Google Maps
                                </x-ui.button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-10 flex flex-col items-start gap-3 text-body text-ink-soft sm:flex-row sm:items-center sm:justify-between lg:mt-12">
            <span>Cannot make it in? Tell us what you need and the nearest branch will call you back.</span>
            <x-ui.button :href="route('contact').'#contact'" variant="secondary" arrow>
                Send an enquiry
            </x-ui.button>
        </p>
    </x-ui.container>
</x-ui.section>
