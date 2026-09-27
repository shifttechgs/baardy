{{--
    Site header: one floating pill.

    After stitch.money's header, adapted. Everything lives in a single pill
    centred at the top of the page (`siteHeader` in app.js):

      at rest     the full bar -- the Baardy lockup, the menu and the call
                  to action. Over the homepage hero it is dark glass
                  with light type ([data-over-photo]); everywhere else, light
                  frosted glass.
      scrolling   down past the hero, the pill condenses to the mark and the
                  call to action ([data-compact]): the wordmark and the links
                  blur away and the pill narrows. Any scroll up expands it.
      menus       Loans, Company and Insights open a panel that grows out of the
                  pill itself: the pill turns white and its height animates
                  to the open menu's content, including between menus of
                  different sizes. Hover (with intent), click or Enter opens;
                  leaving the header, Escape or scrolling closes.
      mobile      the same pill grows into the full menu.

    [data-over-photo] and [data-compact] are the switches every descendant
    keys its styles off, through the `group/nav` variants. Both are rendered
    on the server, so the first paint is already correct.

    Triggers are real links (their section or page) so the menu works without
    JavaScript; with it, a click opens the panel instead, and each panel links
    to the same place.

    Non-overlay pages get a spacer the height of the header, since the pill is
    fixed rather than in the flow.
--}}
@props(['overlay' => false])

@php
    $nav = config('company.nav');
    $products = config('marketing.products');
    $about = config('company.nav_about');
    $articles = collect(config('insights.articles'))->take(3);

    $thumb = fn (string $src): string => str_replace('-1600.webp', '-480.webp', $src);
@endphp

<header
    x-data="siteHeader(@js($overlay))"
    x-on:scroll.window.passive="onScroll()"
    x-on:resize.window.passive="measure()"
    x-on:keydown.escape.window="escape()"
    @if ($overlay) data-over-photo @endif
    x-bind:data-over-photo="overPhoto ? '' : false"
    x-bind:data-compact="compact ? '' : false"
    x-bind:data-expanded="menu || open ? '' : false"
    class="group/nav hero-drop pointer-events-none fixed inset-x-0 top-0 z-50 px-3 pt-3 sm:px-6 sm:pt-5 lg:pt-6"
>
    <div
        x-on:mouseleave="closeMenu()"
        class="pointer-events-auto mx-auto max-w-[92rem] overflow-hidden rounded-[1.75rem] bg-mist/85 text-ink shadow-[0_12px_40px_-24px_rgb(21_16_25/0.35)]
               backdrop-blur-xl backdrop-saturate-150 transition-[max-width,background-color,box-shadow,color] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]
               group-data-over-photo/nav:bg-ink/25 group-data-over-photo/nav:text-paper group-data-over-photo/nav:shadow-[inset_0_0_0_1px_rgb(255_255_255/0.18)]
               group-data-compact/nav:max-w-[25rem] group-data-expanded/nav:bg-paper group-data-expanded/nav:shadow-[0_40px_80px_-40px_rgb(21_16_25/0.45)]"
    >
        {{-- The bar --}}
        <div class="relative flex h-16 items-center justify-between gap-4 pr-2 pl-4 sm:pl-5 lg:h-[4.25rem]">

            <x-ui.logo compact tone="adaptive" />

            {{-- Desktop menu --}}
            <nav
                aria-label="Primary"
                class="absolute top-1/2 left-1/2 hidden -translate-x-1/2 -translate-y-1/2 transition-[opacity,filter] duration-500 lg:block
                       group-data-compact/nav:pointer-events-none group-data-compact/nav:opacity-0 group-data-compact/nav:blur-sm"
            >
                <ul class="flex items-center gap-1">
                    @foreach ($nav as $item)
                        <li>
                            @if (isset($item['menu']))
                                <a
                                    href="{{ $item['href'] }}"
                                    x-ref="trigger-{{ $item['menu'] }}"
                                    x-on:mouseenter="openMenu('{{ $item['menu'] }}')"
                                    x-on:click.prevent="toggleMenu('{{ $item['menu'] }}')"
                                    x-bind:aria-expanded="menu === '{{ $item['menu'] }}' ? 'true' : 'false'"
                                    aria-controls="menu-{{ $item['menu'] }}"
                                    class="inline-flex h-10 items-center gap-1.5 rounded-full px-4 text-body transition-colors duration-200"
                                    x-bind:class="menu && menu !== '{{ $item['menu'] }}' ? 'opacity-55' : ''"
                                >
                                    {{ $item['label'] }}
                                    <x-ui.icon name="chevron-down" class="size-3.5 opacity-60 transition-transform duration-300" x-bind:class="menu === '{{ $item['menu'] }}' && 'rotate-180'" />
                                </a>
                            @else
                                <a
                                    href="{{ $item['href'] }}"
                                    x-on:mouseenter="closeMenu()"
                                    class="inline-flex h-10 items-center rounded-full px-4 text-body transition-opacity duration-200"
                                    x-bind:class="menu ? 'opacity-55' : ''"
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="flex items-center gap-2 sm:gap-4">
                {{-- Visibility lives on a wrapper: the button's own inline-flex
                     would otherwise override `hidden`. --}}
                <span class="hidden sm:contents">
                <x-ui.button
                    :href="config('company.cta.primary.href')"
                    pill
                    class="group/cta gap-3 pr-1.5 pl-5 whitespace-nowrap
                           group-data-over-photo/nav:bg-paper group-data-over-photo/nav:text-ink group-data-over-photo/nav:hover:bg-mist"
                >
                    {{ config('company.cta.primary.label') }}
                    <span class="inline-flex size-8 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover/cta:translate-x-0.5 group-data-over-photo/nav:bg-accent group-data-over-photo/nav:text-paper">
                        <x-ui.icon name="arrow-right" />
                    </span>
                </x-ui.button>
                </span>

                {{-- Mobile trigger --}}
                <button
                    x-ref="toggle"
                    type="button"
                    x-on:click="toggleMobile()"
                    x-bind:aria-expanded="open ? 'true' : 'false'"
                    aria-controls="mobile-menu"
                    class="inline-flex size-11 items-center justify-center rounded-full transition-colors hover:bg-ink/5 lg:hidden
                           group-data-over-photo/nav:hover:bg-paper/10"
                >
                    <span class="sr-only" x-text="open ? 'Close menu' : 'Open menu'">Open menu</span>
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true" class="size-5">
                        <path x-bind:d="open ? 'M5 5l10 10' : 'M3 7h14'" d="M3 7h14" class="transition-all duration-300" />
                        <path x-bind:d="open ? 'M15 5L5 15' : 'M3 13h14'" d="M3 13h14" class="transition-all duration-300" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- The panel: grows out of the pill to the open menu's height. --}}
        <div
            class="overflow-hidden transition-[height] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
            style="height: 0"
            x-bind:style="`height: ${height}px`"
        >
            <div class="grid items-start">
            {{-- Loans --}}
            <div id="menu-loans" x-ref="menu-loans" x-show="menu === 'loans'" x-cloak class="menu-panel hidden p-3 pt-1 [grid-area:1/1] lg:block">
                <div class="grid grid-cols-6 gap-2">
                    @foreach ($products as $product)
                        <a href="/#products" x-on:click="closeMenu(true)" class="group/card flex flex-col gap-3 rounded-2xl bg-mist p-2 pb-4 transition-colors duration-300 hover:bg-line/60">
                            <span class="block aspect-[4/3] overflow-hidden rounded-xl bg-line">
                                <img src="{{ asset($thumb($product['image']['src'])) }}" alt="" loading="lazy" decoding="async" width="480" height="360"
                                     class="size-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/card:scale-[1.06]"
                                     style="object-position: {{ $product['image']['position'] }}">
                            </span>
                            <span class="flex flex-col gap-1 px-2">
                                <span class="flex items-center gap-1.5 text-body text-ink">
                                    {{ $product['name'] }}
                                    <x-ui.icon name="arrow-right" class="size-3.5 text-highlight transition-transform duration-300 group-hover/card:translate-x-0.5" />
                                </span>
                                <span class="figure-nums text-small text-muted">{{ $product['term'] }}</span>
                            </span>
                        </a>
                    @endforeach

                    <a href="{{ config('company.cta.primary.href') }}" x-on:click="closeMenu(true)" class="group/card flex flex-col justify-between rounded-2xl bg-accent p-5 text-paper transition-colors duration-300 hover:bg-accent-strong">
                        <span class="flex flex-col gap-2">
                            <span class="text-lead leading-snug">Not sure which loan?</span>
                            <span class="text-small text-paper/75">Tell us what it is for, and a person will point you to the right one.</span>
                        </span>
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-paper text-accent transition-transform duration-300 group-hover/card:translate-x-0.5 group-hover/card:-translate-y-0.5">
                            <x-ui.icon name="arrow-up-right" />
                        </span>
                    </a>
                </div>
            </div>

            {{-- Company --}}
            <div id="menu-about" x-ref="menu-about" x-show="menu === 'about'" x-cloak class="menu-panel hidden p-3 pt-1 [grid-area:1/1] lg:block">
                <div class="grid grid-cols-5 gap-2">
                    @foreach ($about as $card)
                        <a href="{{ $card['href'] }}" x-on:click="closeMenu(true)" class="group/card flex min-h-40 flex-col justify-between gap-6 rounded-2xl bg-mist p-5 transition-colors duration-300 hover:bg-line/60">
                            <span class="flex flex-col gap-1.5">
                                <span class="text-body text-ink">{{ $card['label'] }}</span>
                                <span class="text-small text-muted">{{ $card['description'] }}</span>
                            </span>
                            <x-ui.icon name="arrow-right" class="size-4 text-highlight transition-transform duration-300 group-hover/card:translate-x-1" />
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Insights --}}
            <div id="menu-insights" x-ref="menu-insights" x-show="menu === 'insights'" x-cloak class="menu-panel hidden p-3 pt-1 [grid-area:1/1] lg:block">
                <div class="grid grid-cols-4 gap-2">
                    @foreach ($articles as $article)
                        <a href="{{ route('insights.show', $article['slug']) }}" x-on:click="closeMenu(true)" class="group/card flex flex-col gap-3 rounded-2xl bg-mist p-2 pb-4 transition-colors duration-300 hover:bg-line/60">
                            <span class="block aspect-[16/10] overflow-hidden rounded-xl bg-line">
                                <img src="{{ asset('images/insights/'.$article['image'].'-480.webp') }}" alt="" loading="lazy" decoding="async" width="480" height="300"
                                     class="size-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/card:scale-[1.06]">
                            </span>
                            <span class="flex flex-col gap-1 px-2">
                                <span class="text-small text-muted">{{ $article['category'] }} &middot; {{ $article['minutes'] }} min read</span>
                                <span class="text-body leading-snug text-ink">{{ $article['title'] }}</span>
                            </span>
                        </a>
                    @endforeach

                    <a href="{{ route('insights.index') }}" x-on:click="closeMenu(true)" class="group/card flex flex-col justify-between rounded-2xl border border-line p-5 transition-colors duration-300 hover:bg-mist">
                        <span class="flex flex-col gap-1.5">
                            <span class="text-body text-ink">All insights</span>
                            <span class="text-small text-muted">Guides on budgeting, borrowing and seasonal finance.</span>
                        </span>
                        <x-ui.icon name="arrow-right" class="size-4 text-highlight transition-transform duration-300 group-hover/card:translate-x-1" />
                    </a>
                </div>
            </div>

            {{-- Mobile: the whole menu, in the same pill. --}}
            <div id="mobile-menu" x-ref="mobile" x-show="open" x-cloak class="menu-panel [grid-area:1/1] max-h-[calc(100svh-6rem)] overflow-y-auto px-4 pb-4 lg:hidden">
                <nav aria-label="Primary (mobile)" class="flex flex-col gap-6 border-t border-line pt-5">
                    <div>
                        <p class="text-small text-muted">Loans</p>
                        <ul class="mt-2 flex flex-col">
                            @foreach ($products as $product)
                                <li>
                                    <a href="/#products" x-on:click="toggleMobile()" class="flex items-center gap-3 py-2">
                                        <img src="{{ asset($thumb($product['image']['src'])) }}" alt="" loading="lazy" width="480" height="360" class="size-11 rounded-lg object-cover" style="object-position: {{ $product['image']['position'] }}">
                                        <span class="flex flex-col">
                                            <span class="text-body text-ink">{{ $product['name'] }}</span>
                                            <span class="figure-nums text-small text-muted">{{ $product['term'] }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <ul class="flex flex-col border-t border-line">
                        @foreach ([...$about, ['label' => 'Insights', 'href' => route('insights.index')], ['label' => 'FAQs', 'href' => '/#faq']] as $link)
                            <li class="border-b border-line">
                                <a href="{{ $link['href'] }}" x-on:click="toggleMobile()" class="flex h-12 items-center justify-between text-body text-ink">
                                    {{ $link['label'] }}
                                    <x-ui.icon name="arrow-right" class="size-4 text-muted" />
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-col gap-2.5">
                        <x-ui.button :href="config('company.cta.primary.href')" size="lg" pill x-on:click="toggleMobile()" class="w-full">
                            {{ config('company.cta.primary.label') }}
                        </x-ui.button>
                    </div>
                </nav>
            </div>
            </div>
        </div>
    </div>
</header>

@unless ($overlay)
    {{-- The pill is fixed, so pages without a full-bleed first section need
         its height held open above their content. --}}
    <div aria-hidden="true" class="h-22 sm:h-26 lg:h-28"></div>
@endunless
