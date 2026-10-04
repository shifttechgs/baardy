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

<x-layout.page-header
    eyebrow="Legal"
    :title="$title"
    :lead="$summary"
>
    <p class="rise figure-nums mt-8 text-small text-muted [animation-delay:300ms]">
        {{ config('company.legal_name') }} &middot; Last updated <time datetime="{{ $updated }}">{{ \Illuminate\Support\Carbon::parse($updated)->format('j F Y') }}</time>
    </p>
</x-layout.page-header>

<x-ui.section :rule="false" class="pt-0! pb-20! sm:pb-24! lg:pb-32!">
    <x-ui.container wide>
        <div class="grid gap-12 border-t border-line pt-12 lg:grid-cols-12 lg:gap-x-16">
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
                            <a href="{{ route('contact') }}#contact" class="inline-flex items-center gap-2 text-ink-soft transition-colors hover:text-accent">
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
