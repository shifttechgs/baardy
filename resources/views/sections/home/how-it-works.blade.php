{{--
    How it works.

    This is a genuine sequence, which is what justifies the step numbers -- the
    order is load-bearing information, not decoration. The client's profile
    lists four steps; the branch visit and the documents happen on the same
    trip, so they are one step here (see marketing.steps).

    LAYOUT. After the reference's "Bank less, live more" row: three tiles side
    by side, everything visible at once -- nothing pinned, paced by scrolling
    or hidden until reached.

      left    the signature moment: the paper the process actually produces,
              in a stack that follows the steps (`paperStack` in app.js).
              The checklist comes forward and its ticks draw in; the
              appraisal sheet comes forward and the "Approved" stamp lands;
              the disbursement slip slides out. It plays through once when
              first seen (keeps cycling on touch screens), then hovering or
              tapping a step drives it. Leans toward the pointer; still under
              reduced motion. Baardy has no app, so a phone mockup here would
              promise something that does not exist. Decorative,
              aria-hidden; only facts from config appear on it.
      middle  the three steps as stacked rows: icon, step number, title, text
      right   a tall photograph carrying the ask, with both ways in -- the
              enquiry form and WhatsApp -- since step one is a branch visit

    Stacks on phones and tablets: visual, steps, then the photograph tile.

    `meta` is what a step involves, not how long it takes. Real timings go in
    config only once the client confirms them.

    PHOTOGRAPH: self-hosted Unsplash stock (unsplash.com/photos/LJL7wx7PM3Y).
    Atmosphere, not a customer.
--}}
@php
    $steps = config('marketing.steps');

    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));

    /**
     * The papers, in step order: the stack brings one forward per step.
     *
     * @var array<int, array{label: string, rows: array<int, array{0: string, 1: string, 2?: bool}>, stamp?: string}> $papers
     */
    $papers = [
        [
            'label' => 'Document checklist',
            'rows' => [['National ID', 'Everyone', true], ['Latest payslip', 'Everyone', true], ['Company documents', 'SME only', false]],
        ],
        [
            'label' => 'Credit appraisal',
            'rows' => [['Documents', 'Checked'], ['Criteria', 'Met']],
            'stamp' => 'Approved',
        ],
        [
            'label' => 'Disbursement advice',
            'rows' => [['Status', 'Approved'], ['Released', 'On approval'], ['Terms', 'As agreed in writing']],
        ],
    ];
@endphp

<x-ui.section id="how-it-works" tone="mist" :rule="false" aria-labelledby="how-it-works-heading" class="py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide>

        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-body font-medium text-accent">How it works</p>
                <h2 data-word-reveal
                    id="how-it-works-heading"
                    class="mt-4 text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink"
                >
                    From application to funds<br class="hidden sm:inline"> in three steps
                </h2>
            </div>

            <p data-word-reveal class="max-w-sm text-lead text-ink-soft lg:col-span-4 lg:col-start-9 lg:justify-self-end lg:text-right">
                The same steps for every loan. Bring the right documents, and you will know where
                you stand.
            </p>
        </div>

        <div
            x-data="paperStack({{ count($papers) }})"
            data-reveal-cards
            class="mt-12 grid gap-4 lg:mt-16 lg:grid-cols-12 lg:gap-5"
        >

            {{-- The visual: the papers the process produces, driven by the steps. --}}
            <div
                aria-hidden="true"
                x-on:mousemove="lean($event)"
                x-on:mouseleave="settle()"
                class="relative flex min-h-[28rem] flex-col overflow-hidden rounded-xl bg-paper lg:col-span-5 lg:min-h-0"
            >
                <div x-ref="stage" class="paper-stage relative flex-1">
                    @foreach ($papers as $index => $paper)
                        <div
                            class="paper-sheet paper-grain absolute top-1/2 left-1/2 w-[min(30rem,88%)] rounded-[3px] border border-line-strong/70 bg-paper px-5 pt-5 text-ink sm:px-7 sm:pt-6 {{ isset($paper['stamp']) ? 'pb-20' : 'pb-7 sm:pb-8' }}"
                            style="transform: translate(-50%, -50%) rotate({{ [-1.5, 5, -7][$index] }}deg) scale({{ [1, 0.9, 0.84][$index] }}); z-index: {{ 30 - $index * 10 }}"
                            x-bind:style="sheetStyle({{ $index }})"
                        >
                            {{-- Letterhead. --}}
                            <div class="flex items-center justify-between border-b border-ink/80 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('images/baardy-mark.png') }}" alt="" class="size-5">
                                    <span class="text-[0.625rem] font-medium tracking-[0.12em] whitespace-nowrap uppercase sm:text-[0.75rem]">Baardy Micro Capital</span>
                                </div>
                                <span class="figure-nums text-[0.75rem] tracking-[0.08em] text-muted">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}/{{ str_pad(count($papers), 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <p class="mt-5 text-[0.75rem] tracking-[0.12em] text-muted uppercase">{{ $paper['label'] }}</p>

                            <dl class="mt-2 text-small sm:text-body">
                                @foreach ($paper['rows'] as $rowIndex => $row)
                                    <div class="flex items-center gap-3 border-b border-dashed border-line-strong py-2.5 last:border-b-0">
                                        @isset ($row[2])
                                            {{-- A tick that draws itself when the checklist comes forward. --}}
                                            <span class="inline-flex size-[1.125rem] shrink-0 items-center justify-center rounded-[3px] border transition-colors duration-300 {{ $row[2] ? 'border-accent' : 'border-line-strong' }}"
                                                @if ($row[2])
                                                    x-bind:class="active === {{ $index }} ? 'bg-accent' : 'bg-paper'"
                                                    x-bind:style="'transition-delay:' + (active === {{ $index }} ? {{ 350 + $rowIndex * 260 }} : 0) + 'ms'"
                                                @endif
                                            >
                                                @if ($row[2])
                                                    <svg viewBox="0 0 18 18" class="size-3 text-paper" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M3.5 9.5 7 13l7.5-8"
                                                            pathLength="1"
                                                            stroke-dasharray="1"
                                                            class="transition-[stroke-dashoffset] duration-500 ease-out"
                                                            style="stroke-dashoffset: 0"
                                                            x-bind:style="'stroke-dashoffset:' + (active === {{ $index }} ? 0 : 1) + '; transition-delay:' + (active === {{ $index }} ? {{ 400 + $rowIndex * 260 }} : 0) + 'ms'"
                                                        />
                                                    </svg>
                                                @endif
                                            </span>
                                        @endisset
                                        <dt class="flex-1 whitespace-nowrap {{ isset($row[2]) ? 'text-ink' : 'text-muted' }}">{{ $row[0] }}</dt>
                                        <dd class="text-right whitespace-nowrap {{ isset($row[2]) ? 'text-muted' : 'text-ink' }}">{{ $row[1] }}</dd>
                                    </div>
                                @endforeach
                            </dl>

                            @isset ($paper['stamp'])
                                {{-- Lands each time the appraisal sheet comes forward. --}}
                                <span
                                    class="absolute right-5 bottom-5 rounded-[4px] border-2 border-highlight px-3 py-1 text-[0.875rem] font-semibold tracking-[0.18em] text-highlight uppercase opacity-0 mix-blend-multiply"
                                    x-bind:class="{ 'stamp-in': active === {{ $index }} }"
                                >
                                    {{ $paper['stamp'] }}
                                </span>
                            @endisset

                            @if ($loop->last)
                                {{-- A tear-off edge on the disbursement slip. --}}
                                <div class="absolute inset-x-0 -bottom-px h-2 bg-[radial-gradient(circle_at_4px_8px,var(--color-paper)_3px,transparent_3.5px)] bg-[length:8px_8px] bg-repeat-x"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Which document is in front, and how far along. --}}
                <div class="flex items-center gap-3 border-t border-line px-5 py-3.5">
                    <span class="figure-nums text-small text-muted">
                        <span x-text="String(active + 1).padStart(2, '0')">01</span>/{{ str_pad(count($papers), 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="flex flex-1 gap-1.5">
                        @foreach ($papers as $index => $paper)
                            <span
                                class="h-0.5 flex-1 rounded-full transition-colors duration-500"
                                x-bind:class="active >= {{ $index }} ? 'bg-accent' : 'bg-line'"
                            ></span>
                        @endforeach
                    </span>
                    <span class="text-small text-ink" x-text="{{ Js::from(array_column($papers, 'label')) }}[active]">{{ $papers[0]['label'] }}</span>
                </div>
            </div>

            {{-- The steps: hovering or tapping one brings its document forward. --}}
            <ol class="flex flex-col gap-4 lg:col-span-4 lg:gap-5">
                @foreach ($steps as $index => $step)
                    <li
                        x-on:mouseenter="finePointer && choose({{ $index }})"
                        x-on:click="choose({{ $index }})"
                        class="flex flex-1 cursor-default items-start gap-5 rounded-xl bg-paper p-6 transition-[box-shadow,transform] duration-500"
                        x-bind:class="active === {{ $index }} ? '-translate-y-0.5 shadow-[0_22px_44px_-26px_rgb(94_38_129/0.5)]' : 'shadow-none'"
                    >
                        <span
                            class="flex size-12 shrink-0 items-center justify-center rounded-full transition-colors duration-500"
                            x-bind:class="active === {{ $index }} ? 'bg-accent text-paper' : 'bg-accent-tint text-accent'"
                        >
                            <x-ui.icon :name="$step['icon'] ?? 'check'" class="size-5" />
                        </span>

                        <div class="flex flex-col gap-1.5">
                            <p class="figure-nums text-small text-accent">Step {{ $index + 1 }}</p>
                            <h3 data-word-reveal class="text-[length:clamp(1.125rem,1rem+0.4vw,1.3125rem)] leading-snug font-normal tracking-[-0.015em] text-ink">
                                {{ $step['title'] }}
                            </h3>
                            <p class="text-small text-ink-soft">{{ $step['body'] }}</p>
                            <p class="mt-1 text-small text-muted">{{ $step['meta'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            {{-- The ask, on a photograph. --}}
            <div class="relative isolate flex min-h-[26rem] flex-col justify-between overflow-hidden rounded-xl bg-ink p-6 text-paper lg:col-span-3">
                <img
                    src="{{ asset('images/why-us/advisory-1400.webp') }}"
                    alt=""
                    width="1400"
                    height="933"
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 -z-10 size-full object-cover object-[55%_40%]"
                >
                <div aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-b from-ink/55 via-ink/10 to-ink/80"></div>

                <a
                    href="{{ config('company.cta.primary.href') }}"
                    class="group flex items-center justify-between gap-3 text-body font-medium text-paper"
                >
                    {{ config('company.cta.primary.label') }}
                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-paper text-accent transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5">
                        <x-ui.icon name="arrow-up-right" />
                    </span>
                </a>

                <div class="flex flex-col gap-4">
                    <p class="flex items-start gap-2 text-small text-paper/85">
                        <x-ui.icon name="lock" class="mt-0.5 text-paper/70" />
                        Applying does not affect your credit standing.
                    </p>

                    @if ($whatsapp)
                        <x-ui.button
                            href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            variant="glass"
                            pill
                            class="w-full"
                        >
                            <x-ui.icon name="chat" />
                            WhatsApp us before you visit
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
