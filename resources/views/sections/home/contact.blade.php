{{--
    Get in touch.

    Direct channels (WhatsApp, phone) on the left, an enquiry form on the
    right -- after the FAQ, so a reader whose question was not answered lands
    straight on a way to ask it. This section owns the #contact anchor, so
    every "Start an application" button on the site arrives at a real form
    rather than at the closing call-to-action panel.

    The actual application happens in a branch (see "How it works"); this
    form starts the conversation: a person calls or WhatsApps back.

    PROGRESSIVE ENHANCEMENT. The form is a normal POST to enquiries.store and
    works with JavaScript off (validation errors and the thank-you come back
    through the session). With Alpine it submits in place: inline errors, a
    loading state, and an animated confirmation instead of a page reload.

    Branch addresses and the per-branch phone list are deliberately NOT shown
    here (removed at the client's request, as they were from the footer).
    The branches still appear as the form's "Nearest branch" choice.
--}}
@php
    $interests = \App\Http\Requests\StoreEnquiryRequest::interests();
    $branches = config('company.branches');

    // The closing call to action's slip passes its two answers in the query
    // string (?interest=&branch=). Only values the form itself offers are
    // taken; anything else is ignored.
    $chosenInterest = old('interest', in_array(request()->query('interest'), $interests, true) ? request()->query('interest') : null);
    $chosenBranch = old('branch', in_array(request()->query('branch'), array_column($branches, 'name'), true) ? request()->query('branch') : $branches[0]['name']);

    $whatsapp = collect($branches)
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));

    $phone = config('company.contact.phone');

    $input = 'h-12 w-full rounded-md border bg-paper px-4 text-body text-ink transition-colors duration-150 '
        .'placeholder:text-muted/70 hover:border-ink focus:border-accent';
@endphp

<x-ui.section id="contact" tone="mist" :rule="false" aria-labelledby="contact-heading" class="py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide>
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-x-12">

            {{-- Heading, then the direct channels pushed to the foot of the
                 column so they line up with the bottom of the form. --}}
            <div data-reveal class="flex flex-col gap-8 lg:col-span-5 lg:justify-between">
                <div>
                    <p class="text-body font-medium text-accent">Get in touch</p>
                    <h2 data-word-reveal
                        id="contact-heading"
                        class="mt-4 text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink"
                    >
                        Tell us what you need. A person will call you back.
                    </h2>
                    <p data-word-reveal class="mt-4 max-w-md text-lead text-ink-soft">
                        Leave your details and our team will call or WhatsApp you, or reach us directly.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @if ($whatsapp)
                        <a
                            href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group flex items-center gap-3.5 rounded-xl bg-accent p-4 text-paper transition-colors hover:bg-accent-strong"
                        >
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-paper/15">
                                <x-ui.icon name="chat" />
                            </span>
                            <span class="flex min-w-0 flex-1 flex-col">
                                <span class="text-small text-paper/70">WhatsApp</span>
                                <span class="figure-nums truncate text-body font-medium text-paper">{{ $whatsapp['display'] }}</span>
                            </span>
                            <x-ui.icon name="arrow-up-right" class="text-paper/70 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </a>
                    @endif

                    <a
                        href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                        class="group flex items-center gap-3.5 rounded-xl bg-paper p-4 transition-shadow duration-300 hover:shadow-[0_18px_40px_-26px_rgb(21_16_25/0.35)]"
                    >
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-mist text-ink">
                            <x-ui.icon name="phone" />
                        </span>
                        <span class="flex min-w-0 flex-1 flex-col">
                            <span class="truncate text-small text-muted">Call &middot; {{ config('company.contact.phone_label') }}</span>
                            <span class="figure-nums truncate text-body font-medium text-ink">{{ $phone }}</span>
                        </span>
                        <x-ui.icon name="arrow-up-right" class="text-muted transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-ink" />
                    </a>
                </div>
            </div>

            {{-- Enquiry form --}}
            <div
                data-reveal
                x-data="{
                    sending: false,
                    sent: {{ Js::from(session('enquiry_sent')) }},
                    errors: {},
                    async submit(event) {
                        this.sending = true;
                        this.errors = {};

                        try {
                            const response = await fetch(event.target.action, {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new FormData(event.target),
                            });
                            const data = await response.json().catch(() => ({}));

                            if (response.ok) {
                                this.sent = data.message;
                                event.target.reset();
                            } else if (response.status === 422) {
                                this.errors = data.errors ?? {};
                                this.$nextTick(() => this.$el.querySelector('[aria-invalid=true]')?.focus());
                            } else {
                                this.errors = { form: ['We could not send that just now. Please call or WhatsApp us instead.'] };
                            }
                        } catch {
                            this.errors = { form: ['We could not send that just now. Please call or WhatsApp us instead.'] };
                        }

                        this.sending = false;
                    },
                }"
                class="relative overflow-hidden rounded-xl bg-paper p-6 sm:p-8 lg:col-span-7 lg:p-10"
            >
                {{-- Confirmation. Replaces the form rather than sitting above it,
                     so there is no way to send the same enquiry twice by accident. --}}
                <div
                    x-show="sent"
                    x-cloak
                    x-transition:enter="transition duration-500 ease-out"
                    x-transition:enter-start="opacity-0 translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex min-h-[26rem] flex-col items-start justify-center gap-6"
                    role="status"
                >
                    <span class="flex size-16 items-center justify-center rounded-full bg-accent text-paper">
                        <svg viewBox="0 0 18 18" class="size-7" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3.5 9.5 7 13l7.5-8" pathLength="1" stroke-dasharray="1"
                                  class="transition-[stroke-dashoffset] delay-200 duration-500 ease-out"
                                  x-bind:style="'stroke-dashoffset:' + (sent ? 0 : 1)" />
                        </svg>
                    </span>

                    <div class="flex flex-col gap-2">
                        <h3 class="text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink">Message received</h3>
                        <p class="max-w-md text-lead text-muted" x-text="sent"></p>
                    </div>

                    <button type="button" x-on:click="sent = null" class="text-small font-medium text-accent underline-offset-4 hover:underline">
                        Send another enquiry
                    </button>
                </div>

                <form
                    x-show="! sent"
                    method="POST"
                    action="{{ route('enquiries.store') }}"
                    x-on:submit.prevent="submit"
                    class="flex flex-col gap-5"
                    novalidate
                >
                    @csrf

                    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
                        <h3 data-word-reveal class="text-[1.5rem] leading-tight font-normal tracking-[-0.02em] text-ink">Send us an enquiry</h3>
                        <p class="text-small text-muted"><span class="text-accent">*</span> Required</p>
                    </div>

                    <div
                        x-show="errors.form"
                        x-cloak
                        class="rounded-md border border-danger/30 bg-danger/5 px-4 py-3 text-small text-danger"
                        role="alert"
                        x-text="errors.form?.[0]"
                    ></div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach ([['name', 'Full name', 'text', 'name', true], ['phone', 'Phone or WhatsApp', 'tel', 'tel', true]] as [$field, $label, $type, $autocomplete, $required])
                            <div class="flex flex-col gap-2">
                                <label for="enquiry-{{ $field }}" class="text-small font-medium text-ink">
                                    {{ $label }}@if ($required)<span class="text-accent"> *</span>@endif
                                </label>
                                <input
                                    id="enquiry-{{ $field }}"
                                    name="{{ $field }}"
                                    type="{{ $type }}"
                                    autocomplete="{{ $autocomplete }}"
                                    value="{{ old($field) }}"
                                    @if ($required) required @endif
                                    class="{{ $input }}"
                                    x-bind:class="errors.{{ $field }} ? 'border-danger' : 'border-line-strong'"
                                    x-bind:aria-invalid="errors.{{ $field }} ? 'true' : 'false'"
                                    aria-describedby="enquiry-{{ $field }}-error"
                                >
                                <p id="enquiry-{{ $field }}-error" class="text-small text-danger empty:hidden" x-text="errors.{{ $field }}?.[0]">@error($field){{ $message }}@enderror</p>
                            </div>
                        @endforeach

                    <div class="flex flex-col gap-2">
                        <label for="enquiry-email" class="text-small font-medium text-ink">
                            Email <span class="font-normal text-muted">(optional)</span>
                        </label>
                        <input
                            id="enquiry-email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            class="{{ $input }}"
                            x-bind:class="errors.email ? 'border-danger' : 'border-line-strong'"
                            x-bind:aria-invalid="errors.email ? 'true' : 'false'"
                            aria-describedby="enquiry-email-error"
                        >
                        <p id="enquiry-email-error" class="text-small text-danger empty:hidden" x-text="errors.email?.[0]">@error('email'){{ $message }}@enderror</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="enquiry-interest" class="text-small font-medium text-ink">
                            What is it about?<span class="text-accent"> *</span>
                        </label>
                        <div class="relative">
                            <select
                                id="enquiry-interest"
                                name="interest"
                                required
                                class="{{ $input }} appearance-none pr-11"
                                x-bind:class="errors.interest ? 'border-danger' : 'border-line-strong'"
                                x-bind:aria-invalid="errors.interest ? 'true' : 'false'"
                                aria-describedby="enquiry-interest-error"
                            >
                                <option value="" disabled @selected(! $chosenInterest)>Choose one</option>
                                @foreach ($interests as $interest)
                                    <option value="{{ $interest }}" @selected($chosenInterest === $interest)>{{ $interest }}</option>
                                @endforeach
                            </select>
                            <x-ui.icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-muted" />
                        </div>
                        <p id="enquiry-interest-error" class="text-small text-danger empty:hidden" x-text="errors.interest?.[0]">@error('interest'){{ $message }}@enderror</p>
                    </div>
                    </div>

                    <fieldset class="flex flex-col gap-2">
                        <legend class="mb-2 text-small font-medium text-ink">
                            Nearest branch<span class="text-accent"> *</span>
                        </legend>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($branches as $branch)
                                <label
                                    class="flex h-12 cursor-pointer items-center justify-center gap-2 rounded-md border border-line-strong
                                           text-body text-ink transition-colors hover:border-ink
                                           has-[:checked]:border-accent has-[:checked]:bg-accent-tint has-[:checked]:text-accent
                                           has-[:focus-visible]:outline has-[:focus-visible]:outline-2
                                           has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                >
                                    <input
                                        type="radio"
                                        name="branch"
                                        value="{{ $branch['name'] }}"
                                        class="sr-only"
                                        @checked($chosenBranch === $branch['name'])
                                    >
                                    {{ $branch['name'] }}
                                </label>
                            @endforeach
                        </div>
                        <p class="text-small text-danger empty:hidden" x-text="errors.branch?.[0]">@error('branch'){{ $message }}@enderror</p>
                    </fieldset>

                    <div class="flex flex-col gap-2">
                        <label for="enquiry-message" class="text-small font-medium text-ink">
                            Anything we should know? <span class="font-normal text-muted">(optional)</span>
                        </label>
                        <textarea
                            id="enquiry-message"
                            name="message"
                            rows="3"
                            maxlength="2000"
                            class="{{ $input }} h-auto resize-y py-3"
                            x-bind:class="errors.message ? 'border-danger' : 'border-line-strong'"
                            placeholder="For example: what the money is for, or the best time to call."
                        >{{ old('message') }}</textarea>
                        <p class="text-small text-danger empty:hidden" x-text="errors.message?.[0]">@error('message'){{ $message }}@enderror</p>
                    </div>

                    {{-- Honeypot. Off-screen rather than display:none, which some
                         bots skip; hidden from assistive technology and tab order. --}}
                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label for="enquiry-website">Website</label>
                        <input id="enquiry-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="flex flex-col gap-4 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="max-w-xs text-small text-muted">
                            We use these details only to respond to your enquiry.
                            <a href="{{ route('legal.privacy') }}" class="text-ink underline decoration-line-strong underline-offset-4 hover:decoration-ink">Privacy notice</a>
                        </p>

                        <x-ui.button type="submit" pill size="lg" class="group min-w-48 gap-3 pr-1.5 pl-6" x-bind:disabled="sending">
                            <span x-show="! sending">Send enquiry</span>
                            <span
                                x-show="! sending"
                                class="inline-flex size-10 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5"
                            >
                                <x-ui.icon name="arrow-right" />
                            </span>
                            <span x-show="sending" x-cloak class="flex items-center gap-2 pr-4.5">
                                <span class="size-4 animate-spin rounded-full border-2 border-paper/30 border-t-paper" aria-hidden="true"></span>
                                Sending&hellip;
                            </span>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
