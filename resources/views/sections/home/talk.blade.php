{{--
    Talk to a person -- the homepage's way into the contact page.

    A short section, not the whole contact page: a heading, one card for each
    office (where it is, one number to call, WhatsApp where there is one),
    and a purple card that opens /contact. The full panel -- tabs, map,
    every number and the enquiry form -- lives on /contact so the landing page
    stays light. All details come from config/company.php -> branches.
--}}
@php
    $offices = config('company.branches');
@endphp

<x-ui.section id="talk" :rule="false" aria-labelledby="talk-heading" class="py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide>
        <x-ui.section-heading
            id="talk-heading"
            eyebrow="Contact us"
            title="Talk to a person"
            lead="Visit an office, call or WhatsApp us, or send a message and we will call you back."
        />

        <div data-reveal-stagger class="mt-10 grid gap-3 md:grid-cols-3 lg:mt-14 lg:gap-4">
            @foreach ($offices as $office)
                @php
                    $call = $office['phones'][0];
                    $whatsapp = collect($office['phones'])->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));
                @endphp
                <article class="flex flex-col gap-10 rounded-2xl bg-mist p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="text-small text-accent">{{ $office['role'] }}</p>
                            <h3 class="text-[length:clamp(1.75rem,1.3rem+1.4vw,2.25rem)] leading-[1.1] tracking-[-0.02em] text-ink">{{ $office['name'] }}</h3>
                        </div>
                        <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-full bg-paper text-accent">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-5">
                                <path d="M10 18s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" />
                                <circle cx="10" cy="8" r="2.2" />
                            </svg>
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col gap-5">
                        <address class="text-body leading-snug text-ink-soft not-italic">
                            @foreach (array_slice($office['address'], 0, 2) as $line)
                                <span class="block">{{ $line }}</span>
                            @endforeach
                        </address>

                        <div class="mt-auto flex flex-col gap-2 border-t border-line-strong/60 pt-5">
                            <a href="tel:{{ $call['tel'] }}" class="inline-flex items-center gap-2.5 text-body text-ink transition-colors hover:text-accent">
                                <x-ui.icon name="phone" class="size-4 text-accent" />
                                <span class="figure-nums">{{ $call['display'] }}</span>
                            </a>
                            @if ($whatsapp)
                                <x-ui.whatsapp-cta :number="$whatsapp['tel']" :label="'WhatsApp '.$office['name']" class="mt-2" />
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach

            {{-- The way on: the contact page, with the map and the enquiry form. --}}
            <a
                href="{{ route('contact') }}"
                class="group flex min-h-72 flex-col justify-between gap-10 rounded-2xl bg-accent p-6 text-paper transition-colors duration-300 hover:bg-accent-strong sm:p-8"
            >
                <span class="flex flex-col gap-3">
                    <span class="text-[length:clamp(1.5rem,1.1rem+1.2vw,2rem)] leading-[1.15] font-medium tracking-[-0.02em]">See both offices on the map, or send an enquiry</span>
                    <span class="text-body text-paper/75">A person will call you back.</span>
                </span>
                <span class="inline-flex items-center gap-3 text-body">
                    <span class="inline-flex size-11 items-center justify-center rounded-full bg-paper text-accent transition-transform duration-300 group-hover:translate-x-1">
                        <x-ui.icon name="arrow-right" />
                    </span>
                    Contact us
                </span>
            </a>
        </div>
    </x-ui.container>
</x-ui.section>
