{{--
    Eligibility / readiness check.

    ---------------------------------------------------------------------------
    THIS DOES NOT DECIDE ANYTHING, BY DESIGN.

    It never returns "you qualify" or "you do not qualify". No eligibility
    criteria have been verified for this business, so any pass/fail rule here
    would be a lending rule invented by the site -- on a regulated lender that
    is a compliance problem, not a placeholder.

    What it does is tell a visitor which documents to bring. That is worth
    doing on its own terms: the page promises a decision in one working day on
    COMPLETE applications, and the FAQ says incomplete ones take longer, so the
    most useful thing this section can do is get someone to arrive complete.

    The checkboxes drive a progress count and nothing else. Ticking them all
    unlocks no decision -- the call to action is available throughout, because
    a missing document is a reason to call, not a reason to be blocked.

    TO CONNECT REAL CRITERIA LATER:
      - keep the document lists in config/marketing.php -> eligibility
      - if the client supplies real gates (minimum trading period, income
        floor, excluded sectors), they belong in the APPLICATION behind an
        affordability assessment, not in a public pre-screen
    ---------------------------------------------------------------------------

    LAYOUT. A portrait on the left carries "who can apply" on a frosted panel,
    so the column is a picture rather than three lines and a void; the
    checklist card sits on the right.

    THE CARD. A sliding segmented control picks the applicant type; a progress
    ring fills as documents are ticked and turns into a check when the list is
    complete; each checkbox is a real (visually hidden) input behind a custom
    box whose tick draws itself. Switching applicant type replays the list's
    `.rise` entrance, staggered, so the change reads as new content arriving.

    PHOTOGRAPH: self-hosted Unsplash stock (Sincerely Media,
    unsplash.com/photos/SWOLvJ4-ZTQ). Atmosphere, not a customer.
--}}
@php
    $eligibility = config('marketing.eligibility');
    $profiles = $eligibility['profiles'];
@endphp

{{-- Paper, not mist: how-it-works carries the tinted band directly above. --}}
<x-ui.section id="eligibility">
    <x-ui.container>
        <x-ui.section-header
            label="Before you apply"
            heading="Know what to bring, and get your answer faster"
        >
            We ask for the same few things every time. Pick what describes you, tick off what
            you already have, and you will know whether you are ready to send a complete
            application.
        </x-ui.section-header>

        <div class="mt-14 grid gap-4 lg:mt-20 lg:grid-cols-12">

            {{-- Who can apply, on a portrait --}}
            <div
                data-reveal
                class="group relative isolate flex min-h-[42rem] flex-col justify-end overflow-hidden rounded-lg bg-mist p-4 sm:min-h-[36rem] sm:p-6 lg:col-span-5 lg:min-h-[30rem]"
            >
                <img
                    src="{{ asset('images/eligibility/applicant-560.webp') }}"
                    srcset="{{ asset('images/eligibility/applicant-560.webp') }} 560w, {{ asset('images/eligibility/applicant-960.webp') }} 960w"
                    sizes="(min-width: 64rem) 30rem, 100vw"
                    width="560"
                    height="700"
                    alt="A woman on a phone call smiling as she reads through a document"
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 -z-10 size-full object-cover object-[50%_30%] transition-transform
                           duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]"
                >
                <div aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-t from-ink/60 via-ink/0 via-50% to-ink/0"></div>

                {{-- Frosted panel. The /90 fill keeps the list readable where
                     backdrop-filter is unsupported; the blur is the upgrade. --}}
                <div class="rounded-md bg-paper/90 p-6 backdrop-blur-md sm:p-7">
                    <h3 class="text-h3 font-medium tracking-[-0.02em] text-ink">
                        Who can apply
                    </h3>

                    <ul class="mt-5 flex flex-col gap-3 text-body text-ink-soft">
                        @foreach ($eligibility['who'] as $who)
                            <li class="flex items-start gap-3">
                                <span class="mt-[0.2em] flex size-5 shrink-0 items-center justify-center rounded-full bg-accent text-paper">
                                    <x-ui.icon name="check" class="size-3" />
                                </span>
                                <span>{{ $who }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-5 border-t border-line pt-4 text-small text-muted">
                        Being on this list is not an offer of credit. What you can borrow, and on what
                        terms, is decided during review.
                    </p>
                </div>
            </div>

            {{-- The interactive checklist --}}
            <div
                data-reveal
                x-data="{
                    profile: '{{ $profiles[0]['key'] }}',
                    keys: {{ Js::from(collect($profiles)->pluck('key')) }},
                    checked: {},
                    documents: {{ Js::from(collect($profiles)->keyBy('key')->map->documents) }},
                    get index() {
                        return Math.max(this.keys.indexOf(this.profile), 0);
                    },
                    get current() {
                        return this.documents[this.profile] ?? [];
                    },
                    get ready() {
                        return this.current.filter((d, i) => this.checked[this.profile + ':' + i]).length;
                    },
                    get total() {
                        return this.current.length;
                    },
                    get complete() {
                        return this.total > 0 && this.ready === this.total;
                    },
                }"
                class="flex flex-col overflow-hidden rounded-lg border border-line bg-paper lg:col-span-7"
            >
                {{-- Applicant type: a segmented control with a sliding thumb. The
                     radios stay real inputs, so arrow keys and screen readers
                     behave exactly as they would on a plain radio group. --}}
                <fieldset class="border-b border-line p-6 sm:p-9">
                    <legend class="sr-only">What describes you?</legend>
                    <p aria-hidden="true" class="text-small font-medium text-ink">What describes you?</p>

                    <div
                        class="relative mt-4 grid rounded-md bg-mist p-1"
                        style="grid-template-columns: repeat({{ count($profiles) }}, minmax(0, 1fr))"
                    >
                        <span
                            aria-hidden="true"
                            class="absolute inset-y-1 left-1 rounded-sm bg-accent shadow-sm transition-transform duration-500
                                   ease-[cubic-bezier(0.16,1,0.3,1)]"
                            style="width: calc((100% - 0.5rem) / {{ count($profiles) }})"
                            x-bind:style="'width: calc((100% - 0.5rem) / {{ count($profiles) }}); transform: translateX(' + (index * 100) + '%)'"
                        ></span>

                        @foreach ($profiles as $profile)
                            <label
                                class="relative z-10 flex cursor-pointer items-center justify-center rounded-sm px-2 py-2.5
                                       text-center text-small leading-tight font-medium transition-colors duration-300
                                       has-[:focus-visible]:outline has-[:focus-visible]:outline-2
                                       has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                x-bind:class="profile === '{{ $profile['key'] }}' ? 'text-paper' : 'text-ink-soft hover:text-ink'"
                            >
                                <input
                                    type="radio"
                                    name="eligibility-profile"
                                    value="{{ $profile['key'] }}"
                                    class="sr-only"
                                    x-model="profile"
                                >
                                {{ $profile['label'] }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="flex flex-1 flex-col gap-6 p-6 sm:p-9">

                    {{-- Progress: a ring that fills per document and becomes a check
                         when the list is complete. --}}
                    <div class="flex items-center gap-5">
                        <div class="relative size-16 shrink-0">
                            <svg viewBox="0 0 64 64" class="size-16 -rotate-90" aria-hidden="true">
                                <circle cx="32" cy="32" r="28" fill="none" stroke-width="4" class="stroke-line" />
                                <circle
                                    cx="32" cy="32" r="28" fill="none" stroke-width="4" stroke-linecap="round"
                                    pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
                                    class="stroke-accent transition-[stroke-dashoffset] duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                    x-bind:style="'stroke-dashoffset:' + (total ? 1 - ready / total : 1)"
                                />
                            </svg>

                            <span
                                x-show="! complete"
                                class="figure-nums absolute inset-0 flex items-center justify-center text-small font-medium text-ink"
                            ><span x-text="ready">0</span>/<span x-text="total">{{ count($profiles[0]['documents']) }}</span></span>

                            <span
                                x-show="complete"
                                x-cloak
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="scale-50 opacity-0"
                                x-transition:enter-end="scale-100 opacity-100"
                                class="absolute inset-0 flex items-center justify-center text-accent"
                            >
                                <x-ui.icon name="check" class="size-6" />
                            </span>
                        </div>

                        <div class="flex flex-col gap-0.5">
                            <h3 class="text-h3 font-medium tracking-[-0.02em] text-ink">What to bring</h3>
                            <p class="text-small text-muted" aria-live="polite">
                                <span x-show="! complete">
                                    <span x-text="ready">0</span> of <span x-text="total">{{ count($profiles[0]['documents']) }}</span> ready
                                </span>
                                <span x-show="complete" x-cloak class="font-medium text-accent">Everything we usually ask for</span>
                            </p>
                        </div>
                    </div>

                    <ul class="flex flex-col gap-1.5">
                        <template x-for="(doc, i) in current" x-bind:key="profile + ':' + i">
                            <li class="rise" x-bind:style="'animation-delay:' + (i * 55) + 'ms'">
                                <label
                                    class="group/row flex cursor-pointer items-center gap-4 rounded-md border px-4 py-3.5
                                           transition-colors duration-200 has-[:focus-visible]:outline has-[:focus-visible]:outline-2
                                           has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                    x-bind:class="checked[profile + ':' + i]
                                        ? 'border-accent/20 bg-accent-tint'
                                        : 'border-transparent hover:border-line hover:bg-mist'"
                                >
                                    <input
                                        type="checkbox"
                                        class="sr-only"
                                        x-model="checked[profile + ':' + i]"
                                    >

                                    <span
                                        aria-hidden="true"
                                        class="flex size-6 shrink-0 items-center justify-center rounded-sm border transition-colors duration-200"
                                        x-bind:class="checked[profile + ':' + i]
                                            ? 'border-accent bg-accent'
                                            : 'border-line-strong bg-paper group-hover/row:border-ink'"
                                    >
                                        {{-- The tick draws itself: pathLength normalises the
                                             stroke to 1 so the dash maths is just 0 or 1. --}}
                                        <svg viewBox="0 0 18 18" class="size-4 text-paper" fill="none" stroke="currentColor"
                                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M3.5 9.5 7 13l7.5-8"
                                                pathLength="1"
                                                stroke-dasharray="1"
                                                class="transition-[stroke-dashoffset] duration-300 ease-out"
                                                x-bind:style="'stroke-dashoffset:' + (checked[profile + ':' + i] ? 0 : 1)"
                                            />
                                        </svg>
                                    </span>

                                    <span
                                        class="flex-1 text-body transition-colors duration-200"
                                        x-bind:class="checked[profile + ':' + i] ? 'text-ink-soft' : 'text-ink'"
                                        x-text="doc"
                                    ></span>

                                    <span
                                        aria-hidden="true"
                                        class="figure-nums text-micro text-muted"
                                        x-text="String(i + 1).padStart(2, '0')"
                                    ></span>
                                </label>
                            </li>
                        </template>
                    </ul>

                    <div class="mt-auto flex flex-col gap-5 border-t border-line pt-6">
                        {{-- The message changes, the action does not. A missing
                             document is a reason to call, never a blocked path. --}}
                        <p class="text-small text-ink-soft">
                            <span x-show="complete" x-cloak>
                                That is everything we usually ask for. Your application should be
                                complete, which is the version that gets a decision fastest.
                            </span>
                            <span x-show="! complete">
                                You can still apply without everything on this list. Anything missing
                                is something we will ask you for during review.
                            </span>
                        </p>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <x-ui.button :href="config('company.cta.primary.href')" size="lg" class="group">
                                {{ config('company.cta.primary.label') }}
                                <x-ui.icon
                                    name="arrow-right"
                                    class="transition-transform duration-200 group-hover:translate-x-0.5"
                                />
                            </x-ui.button>

                            <x-ui.button href="#faq" variant="secondary" size="lg">
                                Read the questions
                            </x-ui.button>
                        </div>

                        <x-ui.disclosure>
                            An indicative list, not a decision. The application tells you exactly what
                            applies to you, and lending is subject to affordability assessment and
                            approval.
                        </x-ui.disclosure>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
