{{--
    /partners -- work with Baardy.

    No partner names or logos: none have been supplied by the client, and a
    lender must not imply endorsements it does not have. The page invites
    organisations to get in touch instead; enquiries use the contact page
    ("Something else") or WhatsApp. Add a logo strip here, with written
    permission from each partner, once there are partners to show. The
    categories describe who we would like to hear from, not existing
    arrangements.

    Built on the shared inner-page system (x-layout.page-header,
    x-ui.section-heading), after trova-travel.framer.website.
--}}
@extends('layouts.marketing')

@section('title', 'Partners | '.config('company.name'))

@section('description', 'Employers, schools, farming groups, NGOs and business networks: partner with '.config('company.legal_name').' to help the people you serve borrow responsibly.')

@php
    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));

    $contactUrl = route('contact', ['interest' => 'Something else']).'#contact';

    $groups = [
        ['title' => 'Employers', 'body' => 'Help your staff understand their borrowing options, with clear costs and repayments that fit their pay.', 'image' => 'images/products/salary-1600.webp', 'alt' => 'A salaried professional in a suit, smiling', 'position' => '65% 30%'],
        ['title' => 'Schools and education groups', 'body' => 'Support families planning for fees, with advice before they borrow rather than after.', 'image' => 'images/products/education-1600.webp', 'alt' => 'A schoolchild holding up work in a classroom', 'position' => '50% 35%'],
        ['title' => 'Farming groups and cooperatives', 'body' => 'Reach farmers with finance timed to the season, and guidance on planning around it.', 'image' => 'images/products/agriculture-1600.webp', 'alt' => 'A farmer tending a green field of crops', 'position' => '40% 40%'],
        ['title' => 'Business networks and associations', 'body' => 'Connect your members, from women entrepreneurs to small shops, with a lender that explains every cost.', 'image' => 'images/products/women-1600.webp', 'alt' => 'A woman running her own shop, serving a customer', 'position' => '30% 35%'],
        ['title' => 'NGOs and community organisations', 'body' => 'Work with us on financial literacy and access to fair credit for the communities you already serve.', 'image' => 'images/hero/market-1280.webp', 'alt' => 'A market trader beside a stall of fruit', 'position' => '50% 50%'],
    ];

    $principles = [
        ['title' => 'Full cost, in writing', 'body' => 'Every customer sees the full cost of a loan before they commit.', 'icon' => 'M7 3h8l4 4v14H7zM15 3v4h4M10 12h6M10 16h6'],
        ['title' => 'A person decides', 'body' => 'Applications are assessed by a person, with affordability in mind.', 'icon' => 'M3 21v-1a6 6 0 0 1 6-6h0a6 6 0 0 1 6 6v1M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
        ['title' => 'Licensed and regulated', 'body' => 'A credit-only microfinance institution on '.config('company.compliance.regulator').' register since '.config('company.compliance.licensed_since').'.', 'icon' => 'M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3ZM9 12l2.2 2.2L15.5 10'],
    ];
@endphp

@section('content')
    <x-layout.page-header
        eyebrow="Partners"
        title="Better borrowing, together"
        lead="If your organisation serves workers, families, farmers or small businesses, we would like to talk."
        image="images/hero/farmer-2400.webp"
        alt="A farmer tending a green field of crops"
        position="40% 35%"
        :width="2400"
        :height="1602"
    >
        <div class="rise mt-8 flex flex-col gap-3 [animation-delay:340ms] sm:flex-row sm:items-center sm:gap-6">
            <x-ui.button :href="$contactUrl" arrow>Start a conversation</x-ui.button>
            @if ($whatsapp)
                <a href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-body text-ink-soft transition-colors hover:text-accent">
                    Or WhatsApp <span class="figure-nums">{{ $whatsapp['display'] }}</span>
                </a>
            @endif
        </div>
    </x-layout.page-header>

    {{-- Who we would like to hear from --}}
    <x-ui.section :rule="false" tone="mist" aria-labelledby="who-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="who-heading"
                eyebrow="Who we would like to hear from"
                title="Organisations that serve people who borrow"
                lead="If you do not see your organisation here, tell us anyway."
            />

            <div data-reveal-stagger class="mt-10 grid gap-3 sm:grid-cols-2 lg:mt-14 lg:grid-cols-6 lg:gap-4">
                @foreach ($groups as $group)
                    <article class="group/partner flex flex-col gap-4 rounded-2xl bg-paper p-2 pb-6 {{ $loop->index < 3 ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                        <div class="{{ $loop->index < 3 ? 'aspect-[4/3]' : 'aspect-[16/9]' }} overflow-hidden rounded-xl bg-line">
                            <img src="{{ asset($group['image']) }}" alt="{{ $group['alt'] }}" width="1600" height="1067" loading="lazy" decoding="async"
                                 class="size-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/partner:scale-[1.06]"
                                 style="object-position: {{ $group['position'] }}">
                        </div>
                        <div class="flex flex-col gap-2 px-4">
                            <h3 class="text-[1.375rem] leading-[1.3] font-medium text-ink">{{ $group['title'] }}</h3>
                            <p class="text-body leading-[1.4] text-ink-soft">{{ $group['body'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- What every partner can count on --}}
    <x-ui.section :rule="false" aria-labelledby="standards-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="standards-heading"
                eyebrow="Our standards"
                title="What every partner can count on"
                lead="The same promises we make to every borrower."
            />

            <div data-reveal-stagger class="mt-10 grid gap-3 sm:grid-cols-3 lg:mt-14 lg:gap-4">
                @foreach ($principles as $principle)
                    <div class="flex min-h-52 flex-col justify-between gap-8 rounded-2xl bg-mist p-6">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-6 text-accent"><path d="{{ $principle['icon'] }}" /></svg>
                        <div class="flex flex-col gap-1.5">
                            <h3 class="text-[1.375rem] leading-[1.3] font-medium text-ink">{{ $principle['title'] }}</h3>
                            <p class="text-body leading-[1.4] text-ink-soft">{{ $principle['body'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div data-reveal class="mt-3 flex flex-col gap-4 rounded-2xl bg-accent p-8 text-paper sm:flex-row sm:items-center sm:justify-between lg:mt-4 lg:p-10">
                <div class="flex flex-col gap-1">
                    <p class="text-[length:clamp(1.5rem,1.1rem+1.2vw,2rem)] leading-[1.2] font-medium tracking-[-0.02em]">Ready to talk?</p>
                    <p class="text-body text-paper/75">Choose &ldquo;Something else&rdquo; on the form and tell us about your organisation.</p>
                </div>
                <x-ui.button :href="$contactUrl" variant="inverse" arrow>Get in touch</x-ui.button>
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
