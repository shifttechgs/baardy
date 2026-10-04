{{--
    Site header: a flat bar at rest that condenses into a centred, floating
    pill as the page scrolls (after trova-travel.framer.website).

    Full width, with the Baardy lockup on the left and the menu and the call to
    action on the right (`siteHeader` in app.js):

      at rest     over the homepage hero the bar is transparent with light
                  type ([data-over-photo]); everywhere else a light frosted
                  bar with a hairline under it.
      scrolling   past the top the bar condenses into a centred pill that
                  floats over the page and stays with the reader in both
                  directions ([data-stuck]); back at the top it relaxes into
                  the flat bar again. While a menu is open the pill widens to
                  hold it.
      menus       Loans and Company open a panel that grows down out of the
                  bar: the bar turns white and its height animates to the
                  open menu's content, including between menus of different
                  sizes. Hover (with intent), click or Enter opens; leaving
                  the header, Escape or scrolling closes.
      mobile      the same bar grows into the full menu.

    [data-over-photo], [data-stuck] and [data-expanded] are the switches
    every descendant keys its styles off, through the `group/nav` variants.
    All are rendered on the server, so the first paint is already correct.

    Triggers are real links (their section or page) so the menu works without
    JavaScript; with it, a click opens the panel instead, and each panel links
    to the same place.

    A live promotion placed in the banner runs as a full-width bar above the
    header bar, on every page. It folds away while a menu is open.

    Non-overlay pages get a spacer the height of the bar, since the header is
    fixed rather than in the flow.
--}}
@props(['overlay' => false])

@php
    $nav = config('company.nav');
    $products = config('marketing.products');
    $about = config('company.nav_about');

    $thumb = fn (string $src): string => str_replace('-1600.webp', '-480.webp', $src);

    // Promotions from the admin panel: one in the pill above the header, one
    // featured in the Loans menu, and "Limited offer" tags on their loans.
    $bannerPromotion = \App\Models\Promotion::forPlacement('banner');
    $menuPromotion = \App\Models\Promotion::forPlacement('menu');
    $productPromotions = \App\Models\Promotion::byProduct();
@endphp

<header
    x-data="siteHeader(@js($overlay))"
    x-on:scroll.window.passive="onScroll()"
    x-on:resize.window.passive="measure()"
    x-on:keydown.escape.window="escape()"
    @if ($overlay) data-over-photo @endif
    x-bind:data-over-photo="overPhoto ? '' : false"
    x-bind:data-stuck="stuck ? '' : false"
    x-bind:data-expanded="menu || open ? '' : false"
    class="group/nav hero-drop pointer-events-none fixed inset-x-0 top-0 z-50 px-0 pt-0 transition-[padding] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] data-stuck:px-3 data-stuck:pt-3 data-expanded:px-2 data-expanded:pt-2"
>
    @if ($bannerPromotion)
        {{-- A full-width bar above the header bar. It folds away on its grid
             row while a menu is open. --}}
        <div
            id="promo-banner"
            class="grid grid-rows-[1fr] transition-[grid-template-rows,opacity] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]
                   group-data-expanded/nav:grid-rows-[0fr] group-data-expanded/nav:opacity-0
                   group-data-stuck/nav:grid-rows-[0fr] group-data-stuck/nav:opacity-0"
        >
            <div class="min-h-0 overflow-hidden">
                <div>
                    <a
                        href="{{ route('promotions.show', $bannerPromotion) }}"
                        x-bind:tabindex="stuck ? -1 : false"
                        class="group/promo pointer-events-auto flex h-10 w-full items-center justify-center gap-2.5 bg-accent px-4 text-small text-paper transition-colors duration-300 hover:bg-accent-strong"
                    >
                        <span class="shrink-0 rounded-full bg-highlight px-2.5 py-0.5 text-micro font-medium">Limited offer</span>
                        <span class="truncate">{{ $bannerPromotion->summary }}</span>
                        <span class="hidden shrink-0 text-paper/70 sm:inline">&middot; Ends {{ $bannerPromotion->ends_at->format('j M') }}</span>
                        <x-ui.icon name="arrow-right" class="size-3.5 shrink-0 transition-transform duration-300 group-hover/promo:translate-x-0.5" />
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div
        x-on:mouseleave="closeMenu()"
        class="pointer-events-auto relative mx-auto max-w-[120rem] overflow-hidden bg-paper/90 text-ink backdrop-blur-xl
               transition-[max-width,border-radius,background-color,border-color,box-shadow,color] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]
               group-data-over-photo/nav:border-transparent group-data-over-photo/nav:bg-transparent group-data-over-photo/nav:text-paper group-data-over-photo/nav:backdrop-blur-none
               group-data-stuck/nav:max-w-[62rem] group-data-stuck/nav:rounded-full group-data-stuck/nav:border-transparent group-data-stuck/nav:bg-paper/85 group-data-stuck/nav:backdrop-saturate-150
               group-data-stuck/nav:shadow-[0_18px_40px_-20px_rgb(21_16_25/0.35),0_0_0_1px_rgb(21_16_25/0.06)]
               group-data-expanded/nav:bg-paper group-data-expanded/nav:shadow-[0_40px_60px_-40px_rgb(21_16_25/0.35)]
               group-data-expanded/nav:max-w-[200rem] group-data-expanded/nav:rounded-[1.25rem]
               group-[[data-stuck][data-expanded]]/nav:max-w-[200rem] group-[[data-stuck][data-expanded]]/nav:rounded-[1.25rem]"
    >
        {{-- The bar --}}
        <div class="relative mx-auto flex h-22 items-center justify-between gap-6 px-6 transition-[height,padding] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] sm:px-10 lg:px-16 2xl:px-20 group-data-stuck/nav:h-16 group-data-stuck/nav:pr-2.5 group-data-stuck/nav:pl-6
                    group-data-expanded/nav:px-4 group-data-expanded/nav:sm:px-8 group-data-expanded/nav:lg:px-14 group-data-expanded/nav:2xl:px-[4.5rem]
                    group-[[data-stuck][data-expanded]]/nav:h-22 group-[[data-stuck][data-expanded]]/nav:px-4 group-[[data-stuck][data-expanded]]/nav:sm:px-8 group-[[data-stuck][data-expanded]]/nav:lg:px-14 group-[[data-stuck][data-expanded]]/nav:2xl:px-[4.5rem]">

            <x-ui.logo compact tone="adaptive" />

            <div class="flex items-center gap-2 sm:gap-4">
                {{-- Desktop menu --}}
                    <nav
                        aria-label="Primary"
                        class="hidden lg:block"
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
                                            class="inline-flex h-10 items-center gap-1.5 rounded-lg px-3.5 text-body transition-colors duration-200"
                                            x-bind:class="menu && menu !== '{{ $item['menu'] }}' ? 'opacity-55' : ''"
                                        >
                                            {{ $item['label'] }}
                                            <x-ui.icon name="chevron-down" class="size-3.5 opacity-60 transition-transform duration-300" x-bind:class="menu === '{{ $item['menu'] }}' && 'rotate-180'" />
                                        </a>
                                    @else
                                        <a
                                            href="{{ $item['href'] }}"
                                            x-on:mouseenter="closeMenu()"
                                            class="inline-flex h-10 items-center rounded-lg px-3.5 text-body transition-opacity duration-200"
                                            x-bind:class="menu ? 'opacity-55' : ''"
                                        >
                                            {{ $item['label'] }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                {{-- Visibility lives on a wrapper: the button's own inline-flex
                     would otherwise override `hidden`. --}}
                <span class="hidden sm:contents">
                <x-ui.button
                    :href="config('company.cta.primary.href')"
                    pill
                    class="gap-3 pr-1.5 pl-5 group-data-over-photo/nav:bg-paper group-data-over-photo/nav:text-ink group-data-over-photo/nav:hover:bg-mist"
                >
                    {{ config('company.cta.primary.label') }}
                    <span aria-hidden="true" class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-0.5 group-data-over-photo/nav:bg-accent group-data-over-photo/nav:text-paper">
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
                    class="inline-flex size-11 items-center justify-center rounded-lg transition-colors hover:bg-ink/5 lg:hidden
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

        {{-- The panel: grows down out of the bar to the open menu's height. --}}
        <div
            class="overflow-hidden transition-[height] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
            style="height: 0"
            x-bind:style="`height: ${height}px`"
        >
            <div class="mx-auto grid items-start px-4 sm:px-8 lg:px-14 2xl:px-[4.5rem]">
            {{-- Loans --}}
            <div id="menu-loans" x-ref="menu-loans" x-show="menu === 'loans'" x-cloak class="menu-panel hidden pt-1 pb-8 [grid-area:1/1] lg:block lg:pb-10">
                <div class="grid grid-cols-7 gap-2">
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
                                @isset ($productPromotions[$product['name']])
                                    <span class="mt-1 w-fit rounded-full bg-highlight-tint px-2.5 py-0.5 text-micro font-medium text-highlight">Limited offer</span>
                                @endisset
                            </span>
                        </a>
                    @endforeach

                    @if ($menuPromotion)
                        {{-- The featured promotion takes the card slot while it runs. --}}
                        <a href="{{ route('promotions.show', $menuPromotion) }}" x-on:click="closeMenu(true)" class="group/card relative isolate flex flex-col justify-between overflow-hidden rounded-2xl bg-accent p-5 text-paper">
                            @if ($menuPromotion->imageUrl())
                                <img src="{{ $menuPromotion->imageUrl() }}" alt="" loading="lazy" class="absolute inset-0 -z-10 size-full object-cover transition-transform duration-700 group-hover/card:scale-[1.05]">
                                <span aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-t from-ink/85 via-ink/30 to-ink/10"></span>
                            @endif
                            <span class="w-fit rounded-full bg-highlight px-2.5 py-0.5 text-micro font-medium">Limited offer</span>
                            <span class="flex flex-col gap-1.5">
                                <span class="text-lead leading-snug">{{ $menuPromotion->title }}</span>
                                <span class="text-small text-paper/80">Ends {{ $menuPromotion->ends_at->format('j M') }}</span>
                            </span>
                        </a>
                    @else
                    <a href="{{ config('company.cta.primary.href') }}" x-on:click="closeMenu(true)" class="group/card flex flex-col justify-between rounded-2xl bg-accent p-5 text-paper transition-colors duration-300 hover:bg-accent-strong">
                        <span class="flex flex-col gap-2">
                            <span class="text-lead leading-snug">Not sure which loan?</span>
                            <span class="text-small text-paper/75">Tell us what it is for, and a person will point you to the right one.</span>
                        </span>
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-paper text-accent transition-transform duration-300 group-hover/card:translate-x-0.5 group-hover/card:-translate-y-0.5">
                            <x-ui.icon name="arrow-up-right" />
                        </span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Company --}}
            <div id="menu-about" x-ref="menu-about" x-show="menu === 'about'" x-cloak class="menu-panel hidden pt-1 pb-8 [grid-area:1/1] lg:block lg:pb-10">
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

            {{-- Mobile: the whole menu, in the same pill. --}}
            <div id="mobile-menu" x-ref="mobile" x-show="open" x-cloak class="menu-panel [grid-area:1/1] max-h-[calc(100svh-6rem)] overflow-y-auto pb-8 lg:hidden">
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
                                            <span class="figure-nums text-small text-muted">
                                                {{ $product['term'] }}
                                                @isset ($productPromotions[$product['name']])
                                                    &middot; <span class="font-medium text-highlight">Limited offer</span>
                                                @endisset
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <ul class="flex flex-col border-t border-line">
                        @foreach ([...$about, ['label' => 'FAQs', 'href' => '/#faq'], ['label' => 'Partners', 'href' => route('partners')], ['label' => 'Careers', 'href' => route('careers')]] as $link)
                            <li class="border-b border-line">
                                <a href="{{ $link['href'] }}" x-on:click="toggleMobile()" class="flex h-12 items-center justify-between text-body text-ink">
                                    {{ $link['label'] }}
                                    <x-ui.icon name="arrow-right" class="size-4 text-muted" />
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-col gap-2.5">
                        <x-ui.button :href="config('company.cta.primary.href')" size="lg" arrow x-on:click="toggleMobile()" class="w-full justify-between">
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
    <div aria-hidden="true" @class(['h-22', 'mt-10' => $bannerPromotion])></div>
@endunless
