{{--
    FAQ.

    Laid out like the other sections: label and heading left, one line
    right. Below, the questions run down the right as rounded tiles while the
    left column holds a "still have a question" card -- sticky on desktop, so
    the route to a person follows the reader down the answers.

    WhatsApp comes first because it is how most people here would rather ask;
    the number is the branch line labelled "Call/WhatsApp" in
    config/company.php, so it changes in one place. The card deliberately
    stays short: the full contact section follows directly below. Its
    photograph is self-hosted Unsplash stock (Sincerely Media,
    unsplash.com/photos/SWOLvJ4-ZTQ) -- atmosphere, not a customer.

    Accordion rows are native <details> elements -- see x-ui.accordion-item.
    The first row is open on load so the section shows an answer, not just a
    list of closed questions. On mouse and trackpad, resting on a question
    opens it (`hoverAccordion` in app.js); a tap or click works everywhere.
--}}
@php
    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));

    $phone = config('company.contact.phone');
@endphp

<x-ui.section id="faq" :rule="false" tone="mist" aria-labelledby="faq-heading" class="py-16! sm:py-20! lg:py-24!">
    <x-ui.container wide>

        <div data-reveal class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="flex items-center gap-1.5 text-body text-ink">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-3.5"><path d="m6 3.5 4.5 4.5L6 12.5" /></svg>
                    Questions
                </p>
                <h2 data-word-reveal
                    id="faq-heading"
                    class="mt-5 text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em] text-ink"
                >
                    What people ask<br class="hidden sm:inline"> before they borrow
                </h2>
            </div>

            <p data-word-reveal class="max-w-sm text-lead leading-[1.4] text-ink-soft lg:col-span-4 lg:col-start-9 lg:justify-self-end lg:text-right">
                The things borrowers ask most often. If yours is not here, a person will answer it.
            </p>
        </div>

        <div class="mt-12 grid gap-x-12 gap-y-10 lg:mt-16 lg:grid-cols-12">

            <div
                x-data="hoverAccordion"
                x-on:mousemove="intend($event)"
                x-on:mouseleave="cancel()"
                data-reveal-stagger
                class="flex flex-col gap-3 lg:col-span-8 lg:col-start-5"
            >
                @foreach (config('marketing.faqs') as $faq)
                    <x-ui.accordion-item
                        :question="$faq['question']"
                        :number="str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)"
                        :open="$loop->first"
                    >
                        {{ $faq['answer'] }}
                    </x-ui.accordion-item>
                @endforeach
            </div>

            {{-- Still have a question: after the list in the markup, moved left
                 with `order` on desktop so the answers are read first. --}}
            <aside
                data-reveal
                aria-labelledby="faq-ask-heading"
                class="relative isolate flex min-h-[34rem] flex-col justify-end overflow-hidden rounded-xl bg-ink p-3 text-paper sm:p-4
                       lg:sticky lg:top-28 lg:order-first lg:col-span-4 lg:min-h-[40rem] lg:self-start"
            >
                {{-- The photograph, untinted; the text sits on the site's frosted
                     panel (as on the hero, the loans rail and the trust card)
                     rather than on a colour wash over the person. --}}
                <img
                    src="{{ asset('images/eligibility/applicant-560.webp') }}"
                    srcset="{{ asset('images/eligibility/applicant-560.webp') }} 560w, {{ asset('images/eligibility/applicant-960.webp') }} 960w"
                    sizes="(min-width: 64rem) 30vw, 100vw"
                    width="560"
                    height="700"
                    alt=""
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 -z-20 size-full object-cover object-[50%_12%]"
                >
                <div class="flex flex-col gap-6 rounded-[0.625rem] bg-ink/45 p-5 ring-1 ring-paper/15 ring-inset backdrop-blur-xl sm:p-6">
                    <div class="flex flex-col gap-2">
                        <p id="faq-ask-heading" data-word-reveal class="text-[1.5rem] leading-tight tracking-[-0.02em]">Still have a question?</p>
                        <p class="text-lead text-paper/85">
                            Ask the team directly.
                            <span class="whitespace-nowrap">{{ config('company.contact.phone_label') }}.</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-paper/15 pt-5">
                        <a
                            href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                            class="flex items-center gap-2 text-small text-paper/85 transition-colors hover:text-paper"
                        >
                            <x-ui.icon name="phone" class="text-paper/70" />
                            <span>Call <span class="figure-nums font-medium text-paper">{{ $phone }}</span></span>
                        </a>

                        @if ($whatsapp)
                            <x-ui.whatsapp-cta :number="$whatsapp['tel']" label="WhatsApp us" class="ml-auto" />
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </x-ui.container>
</x-ui.section>
