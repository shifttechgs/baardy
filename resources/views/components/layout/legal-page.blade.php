{{--
    A legal page: privacy notice, terms of use, complaints procedure,
    responsible lending.

    Same reading rhythm as an Insights article (`.article-body` in app.css),
    with the other legal pages listed alongside so a reader can move between
    them, and the ways to reach a person at the foot.

    The email line only appears once company.contact.email is a real address
    (not the example.com placeholder), so no page ever shows a dead one.

    Props
      title    the page title (also the <h1>)
      summary  one or two sentences under the title
      updated  "Last updated" date, Y-m-d
--}}
@props(['title', 'summary', 'updated'])

@php
    $pages = config('company.legal');
    $email = config('company.contact.email');
    $hasRealEmail = $email && ! str_ends_with($email, 'example.com');
    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));
@endphp

<x-ui.section :rule="false" class="pt-10 sm:pt-14 lg:pt-16">
    <x-ui.container>
        <nav aria-label="Breadcrumb" class="rise text-small text-muted">
            <a href="{{ route('home') }}" class="transition-colors hover:text-accent">Home</a>
            <span aria-hidden="true" class="mx-2">/</span>
            <span class="text-ink">{{ $title }}</span>
        </nav>

        <div class="mt-10 grid gap-12 lg:mt-16 lg:grid-cols-12">
            <header class="flex flex-col gap-5 lg:col-span-8">
                <p class="rise text-body text-muted [animation-delay:60ms]">{{ config('company.legal_name') }}</p>
                <h1 class="rise text-[length:clamp(2.25rem,1.4rem+3vw,3.75rem)] leading-[1.05] font-light tracking-[-0.035em] text-ink [animation-delay:120ms]">
                    {{ $title }}
                </h1>
                <p class="rise max-w-2xl text-lead text-muted [animation-delay:180ms]">{{ $summary }}</p>
                <p class="rise figure-nums text-small text-muted [animation-delay:220ms]">
                    Last updated <time datetime="{{ $updated }}">{{ \Illuminate\Support\Carbon::parse($updated)->format('j F Y') }}</time>
                </p>
            </header>
        </div>

        <div class="mt-14 grid gap-12 border-t border-line pt-12 lg:mt-16 lg:grid-cols-12 lg:gap-x-16">
            <div class="article-body lg:col-span-8">
                {{ $slot }}
            </div>

            <aside class="flex flex-col gap-8 lg:sticky lg:top-28 lg:col-span-4 lg:self-start">
                <nav aria-label="Legal pages">
                    <p class="text-small font-medium text-ink">Policies</p>
                    <ul class="mt-3 flex flex-col border-t border-line">
                        @foreach ($pages as $page)
                            @php
                                $isCurrent = request()->routeIs($page['route']);
                            @endphp
                            <li class="border-b border-line">
                                <a
                                    href="{{ route($page['route']) }}"
                                    @if ($isCurrent) aria-current="page" @endif
                                    class="flex items-center justify-between gap-3 py-3 text-body transition-colors {{ $isCurrent ? 'text-accent' : 'text-ink-soft hover:text-ink' }}"
                                >
                                    {{ $page['label'] }}
                                    <x-ui.icon name="arrow-right" class="{{ $isCurrent ? 'text-accent' : 'text-muted' }}" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="flex flex-col gap-4 rounded-xl bg-mist p-6">
                    <p class="text-body text-ink">Questions about this page?</p>
                    <ul class="flex flex-col gap-2.5 text-small">
                        <li>
                            <a href="tel:{{ preg_replace('/\s+/', '', config('company.contact.phone')) }}" class="inline-flex items-center gap-2 text-ink-soft transition-colors hover:text-accent">
                                <x-ui.icon name="phone" class="text-accent" />
                                <span class="figure-nums">{{ config('company.contact.phone') }}</span>
                            </a>
                        </li>
                        @if ($whatsapp)
                            <li>
                                <a href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-ink-soft transition-colors hover:text-accent">
                                    <x-ui.icon name="chat" class="text-accent" />
                                    WhatsApp <span class="figure-nums">{{ $whatsapp['display'] }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($hasRealEmail)
                            <li>
                                <a href="mailto:{{ $email }}" class="inline-flex items-center gap-2 text-ink-soft transition-colors hover:text-accent">
                                    <x-ui.icon name="mail" class="text-accent" />
                                    {{ $email }}
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('home') }}#contact" class="inline-flex items-center gap-2 text-ink-soft transition-colors hover:text-accent">
                                <x-ui.icon name="arrow-right" class="text-accent" />
                                Send us a message
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </x-ui.container>
</x-ui.section>
