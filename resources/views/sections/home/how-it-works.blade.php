{{--
    How it works.

    After trova-travel.framer.website's "How It Works": plain, no images.

      left    sticky as you scroll: the eyebrow, the heading, one sentence, the
              ask, and the quiet second way in (WhatsApp)
      right   the steps as light cards. Each card is sticky at the same line,
              so scrolling carries the next step up over the one before it,
              like a deck being dealt (CSS only: `sticky`, no script).

    This is a genuine sequence, which is what justifies the step numbers -- the
    order is load-bearing information, not decoration. The client's profile
    lists four steps; the branch visit and the documents happen on the same
    trip, so they are one step here (see marketing.steps). `meta` stays in
    config for when it is wanted; the page shows each step's title and text.

    Below lg nothing sticks: the steps are a plain list under the heading.
--}}
@php
    $steps = config('marketing.steps');

    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));
@endphp

<x-ui.section id="how-it-works" :rule="false" aria-labelledby="how-it-works-heading" class="py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide>
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-x-8">

            {{-- Left: sticky --}}
            <div data-reveal class="lg:col-span-5 lg:self-start lg:sticky lg:top-24">
                <p class="flex items-center gap-1.5 text-body text-ink">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-3.5"><path d="m6 3.5 4.5 4.5L6 12.5" /></svg>
                    How it works
                </p>

                <h2
                    id="how-it-works-heading"
                    data-word-reveal
                    class="mt-5 text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-ink"
                >
                    From application<br class="hidden sm:inline"> to funds
                </h2>

                <p class="mt-4 max-w-md text-body leading-[1.4] text-ink-soft">
                    The same steps for every loan. Bring the right documents, and you will know where you stand.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3">
                    <x-ui.button :href="config('company.cta.primary.href')" arrow>{{ config('company.cta.primary.label') }}</x-ui.button>

                    @if ($whatsapp)
                        <x-ui.whatsapp-cta :number="$whatsapp['tel']" label="WhatsApp us before you visit" tone="mist" />
                    @endif
                </div>
            </div>

            {{-- Right: the steps, dealt one over another --}}
            <ol data-reveal-stagger class="flex flex-col gap-4 pb-10 lg:col-span-7 lg:gap-6 lg:pb-24">
                @foreach ($steps as $i => $step)
                    <li
                        class="rounded-2xl bg-mist p-6 sm:p-8 lg:sticky lg:top-(--stack-top)"
                        style="--stack-top: calc(6rem + {{ $i }} * 0.75rem)"
                    >
                        <div class="flex gap-6 sm:gap-10">
                            <span class="figure-nums w-8 shrink-0 pt-0.5 text-[1.25rem] leading-none text-ink">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="flex max-w-lg flex-col gap-2">
                                <h3 class="text-[1.5rem] leading-[1.3] font-medium text-ink">{{ $step['title'] }}</h3>
                                <p class="text-body leading-[1.4] text-ink-soft">{{ $step['body'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </x-ui.container>
</x-ui.section>
