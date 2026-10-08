{{--
    /about -- who we are.

    After trova-travel.framer.website's About page: a page header with one
    wide photograph; "Our story" as large type beside a portrait; a photograph
    panel with three white cards; then the register facts. The company profile
    and target market are the client's own words (config/company.php ->
    profile). No staff photographs: none have been supplied, and a stock face
    beside a real role would be a misrepresentation.
--}}
@extends('layouts.marketing')

@section('title', 'About Baardy Micro Capital | Licensed Microfinance, Zimbabwe')

@section('description', 'Zimbabwean-owned, registered microfinance institution since 2015, serving government employees, pensioners and SMEs, with offices in Harare and Bulawayo.')

@php
    $profile = config('company.profile');
    $compliance = config('company.compliance');

    $facts = [
        ['value' => now()->year - (int) $compliance['licensed_since'], 'suffix' => '+', 'label' => 'Years licensed and lending'],
        ['value' => (int) $compliance['licence'], 'prefix' => 'No. ', 'label' => 'RBZ licence number'],
        ['value' => count(config('marketing.products')), 'label' => 'Loan products'],
        ['value' => count(config('company.branches')), 'label' => 'Offices you can walk into'],
    ];

    $pillars = [
        ['image' => 'images/products/salary-1600.webp', 'alt' => 'A salaried professional in a suit, smiling', 'position' => '65% 30%', 'title' => $profile['serves'][0]['title'], 'body' => $profile['serves'][0]['body'], 'icon' => 'M3 21v-1a6 6 0 0 1 6-6h0a6 6 0 0 1 6 6v1M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 4.5a3.5 3.5 0 0 1 0 6.5M21 21v-1a6 6 0 0 0-4-5.6'],
        ['image' => 'images/products/sme-1600.webp', 'alt' => 'A small-business owner at work', 'position' => '50% 40%', 'title' => $profile['serves'][1]['title'], 'body' => $profile['serves'][1]['body'], 'icon' => 'M4 9l1.5-5h13L20 9M4 9v11h16V9M10 20v-5h4v5'],
        ['image' => 'images/why-us/advisory-1400.webp', 'alt' => 'A loan officer explaining costs to a customer', 'position' => '50% 40%', 'title' => 'Lending you can check', 'body' => 'Every cost is in writing before you sign, and a person reviews every application.', 'icon' => 'M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3ZM9 12l2.2 2.2L15.5 10'],
    ];
@endphp

@section('content')
    <x-seo.schema :data="[\App\Support\StructuredData::organization(), \App\Support\StructuredData::page('AboutPage', 'About Baardy Micro Capital')]" />

    <x-layout.page-header
        :crumbs="[['About us']]"
        eyebrow="About us"
        title="Proudly Zimbabwean, serving since {{ $compliance['licensed_since'] }}"
        lead="A licensed microfinance lender for government employees, pensioners and small businesses."
        image="images/hero/market-2400.webp"
        alt="A produce market in Zimbabwe, the people Baardy lends to"
        position="50% 60%"
    />

    {{-- Our story --}}
    <x-ui.section :rule="false" aria-labelledby="story-label" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide>
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-x-8">
                <x-ui.eyebrow id="story-label" data-reveal class="self-start lg:col-span-2">Our story</x-ui.eyebrow>

                <div data-reveal class="flex flex-col gap-8 lg:col-span-6">
                    <p class="text-[length:clamp(1.25rem,1rem+0.9vw,1.625rem)] leading-[1.35] tracking-[-0.02em] text-ink">{{ $profile['intro'] }}</p>
                    <p class="max-w-xl text-[1.0625rem] leading-[1.6] text-ink-soft sm:text-[1.125rem]">{{ $profile['belief'] }}</p>
                </div>

                <figure data-reveal class="relative self-start overflow-hidden rounded-2xl bg-mist lg:sticky lg:top-28 lg:col-span-4">
                    <img
                        src="{{ asset('images/about/tailor-1280.webp') }}"
                        alt="A tailor smiling at his phone beside his sewing machine"
                        width="1280"
                        height="1280"
                        loading="lazy"
                        decoding="async"
                        class="aspect-[4/5] w-full object-cover object-[50%_30%]"
                    >
                    <figcaption class="absolute inset-x-3 bottom-3 rounded-xl bg-ink/55 px-4 py-3 text-small text-paper backdrop-blur-xl backdrop-saturate-150">
                        Licensed microfinance &middot; Est. {{ $compliance['licensed_since'] }}
                    </figcaption>
                </figure>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- Who we serve: a photograph with three white cards --}}
    <x-ui.section :rule="false" tone="mist" aria-labelledby="serve-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="serve-heading"
                eyebrow="Who we serve"
                title="What sets every loan apart"
                lead="Lending designed around how people in the public service, and in business for themselves, actually earn."
            />

            <div data-reveal-stagger class="mt-10 grid gap-3 md:grid-cols-3 lg:mt-14 lg:gap-4">
                @foreach ($pillars as $pillar)
                    <article class="group/pillar flex flex-col gap-5 rounded-2xl bg-paper p-2 pb-7">
                        <div class="aspect-[4/3] overflow-hidden rounded-xl bg-line">
                            <img src="{{ asset($pillar['image']) }}" alt="{{ $pillar['alt'] }}" width="1600" height="1067" loading="lazy" decoding="async"
                                 class="size-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/pillar:scale-[1.05]"
                                 style="object-position: {{ $pillar['position'] }}">
                        </div>
                        <div class="flex flex-col gap-2 px-4">
                            <h3 class="text-[1.375rem] leading-[1.3] font-medium text-ink">{{ $pillar['title'] }}</h3>
                            <p class="text-body leading-[1.4] text-ink-soft">{{ $pillar['body'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>        </x-ui.container>
    </x-ui.section>

    {{-- On the register --}}
    <x-ui.section :rule="false" aria-labelledby="register-heading" class="py-16! sm:py-20! lg:py-24!">
        <x-ui.container wide>
            <x-ui.section-heading
                id="register-heading"
                eyebrow="On the register"
                title="Licensed, supervised and easy to check"
                lead="Every figure here can be looked up on the Reserve Bank of Zimbabwe's public register."
            />

            <dl data-reveal-stagger class="mt-10 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:mt-14 lg:grid-cols-4">
                @foreach ($facts as $fact)
                    <div class="flex flex-col gap-3 border-t border-ink/70 pt-5">
                        <dt class="order-2 text-body text-ink-soft">{{ $fact['label'] }}</dt>
                        <dd class="figure-nums order-1 text-[length:clamp(2.75rem,1.5rem+3.2vw,4.5rem)] leading-none tracking-[-0.04em] text-accent">
                            {{ $fact['prefix'] ?? '' }}<span data-count-to="{{ $fact['value'] }}" data-count-on-view>{{ $fact['value'] }}</span>{{ $fact['suffix'] ?? '' }}
                        </dd>
                    </div>
                @endforeach
            </dl>

            <div data-reveal class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4">
                <x-ui.button :href="route('contact')" arrow>Visit or contact us</x-ui.button>
                <a href="{{ $compliance['register_url'] }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 text-body text-ink-soft transition-colors hover:text-accent">
                    Check our licence on the RBZ register
                    <x-ui.icon name="arrow-up-right" class="size-4" />
                </a>
            </div>
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
