{{--
    A legal page: privacy notice, terms of use, complaints procedure,
    responsible lending.

    Dressed like the other inner pages (About, Careers): the wide page header
    with one photograph, then the document set in the same editorial grid --
    a quiet left rail (contents, the other policies) and, beside it, the
    standfirst in large type, a key-points card for "The short version" when
    a page has one, and each section as a numbered row between hairlines.
    It closes on a card with the ways to reach a person.

    The page's own markup stays plain: a lead <p>, then <h2> sections. This
    component splits it at the <h2>s, so the pages need no layout markup and
    the contents list can never disagree with the headings.

    The email line only appears once company.contact.email is a real address
    (not the example.com placeholder), so no page ever shows a dead one.

    Props
      title     the page title (also the <h1>)
      summary   one or two sentences beside the title
      updated   "Last updated" date, Y-m-d
      image     published path of the header photograph
      alt       its description
      position  its object-position
      width     its pixel width / height, for layout stability
--}}
@props([
    'title',
    'summary',
    'updated',
    'image' => 'images/hero/market-2400.webp',
    'alt' => '',
    'position' => '50% 50%',
    'width' => 2400,
    'height' => 1600,
])

@php
    $pages = config('company.legal');
    $email = config('company.contact.email');
    $hasRealEmail = $email && ! str_ends_with($email, 'example.com');
    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));
    $updatedOn = \Illuminate\Support\Carbon::parse($updated);

    // Split the slot at its <h2>s: what comes first is the standfirst, each
    // heading then opens a section.
    $parts = preg_split('/(<h2[^>]*>.*?<\/h2>)/s', (string) $slot, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $standfirst = '';
    $sections = [];

    foreach ($parts as $part) {
        if (preg_match('/^<h2([^>]*)>(.*?)<\/h2>$/s', $part, $heading)) {
            $label = trim(strip_tags($heading[2]));
            preg_match('/\sid="([^"]+)"/', $heading[1], $existing);
            $sections[] = ['id' => $existing[1] ?? \Illuminate\Support\Str::slug($label), 'label' => $label, 'html' => ''];
        } elseif ($sections === []) {
            $standfirst .= $part;
        } else {
            $sections[array_key_last($sections)]['html'] .= $part;
        }
    }

    $isKeyPoints = fn (array $section): bool => $section['label'] === 'The short version';
    $numbered = array_values(array_filter($sections, fn (array $section): bool => ! $isKeyPoints($section)));
@endphp

<x-layout.page-header
    eyebrow="Legal"
    :title="$title"
    :lead="$summary"
    :image="$image"
    :alt="$alt"
    :position="$position"
    :width="$width"
    :height="$height"
>
    <p class="rise figure-nums mt-6 text-small text-muted [animation-delay:340ms]">
        {{ config('company.legal_name') }} &middot; Last updated <time datetime="{{ $updated }}">{{ $updatedOn->format('j F Y') }}</time>
    </p>
</x-layout.page-header>

<x-ui.section :rule="false" class="pt-0! pb-20! sm:pb-24! lg:pb-32!">
    <x-ui.container wide>
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-x-8">

            {{-- The rail: where you are, and the other policies --}}
            <aside class="flex flex-col gap-10 lg:sticky lg:top-28 lg:col-span-3 lg:self-start">
                @if (count($numbered) > 1)
                    <nav aria-label="On this page" data-reveal>
                        <x-ui.eyebrow>On this page</x-ui.eyebrow>
                        <ol class="mt-5 flex flex-col text-small">
                            @foreach ($numbered as $index => $section)
                                <li class="border-t border-line first:border-t-0">
                                    <a href="#{{ $section['id'] }}" class="group flex items-baseline gap-3 py-2 text-ink-soft transition-colors hover:text-accent">
                                        <span class="figure-nums w-5 shrink-0 text-muted transition-colors group-hover:text-accent">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        {{ $section['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <nav aria-label="Legal pages" data-reveal>
                    <x-ui.eyebrow>Policies</x-ui.eyebrow>
                    <ul class="mt-5 flex flex-col gap-1.5">
                        @foreach ($pages as $page)
                            @php
                                $isCurrent = request()->routeIs($page['route']);
                            @endphp
                            <li>
                                <a
                                    href="{{ route($page['route']) }}"
                                    @if ($isCurrent) aria-current="page" @endif
                                    @class([
                                        'flex items-center justify-between gap-3 rounded-xl px-4 py-3 text-small transition-colors',
                                        'bg-accent-tint font-medium text-accent' => $isCurrent,
                                        'text-ink-soft hover:bg-mist hover:text-ink' => ! $isCurrent,
                                    ])
                                >
                                    {{ $page['label'] }}
                                    <x-ui.icon name="arrow-right" @class(['text-accent' => $isCurrent, 'text-muted' => ! $isCurrent]) />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>

            {{-- The document --}}
            <div class="min-w-0 lg:col-span-9">
                <div data-reveal class="max-w-3xl article-body article-body--plain text-[length:clamp(1.25rem,1rem+0.9vw,1.625rem)] leading-[1.4] tracking-[-0.02em] text-ink">
                    {!! $standfirst !!}
                </div>

                @foreach ($sections as $section)
                    @if ($isKeyPoints($section))
                        <section id="{{ $section['id'] }}" data-reveal class="mt-10 scroll-mt-28 rounded-2xl bg-mist p-6 sm:p-8 lg:p-10">
                            <x-ui.eyebrow>{{ $section['label'] }}</x-ui.eyebrow>
                            <div class="article-body article-body--plain legal-points mt-6 max-w-2xl text-[1.0625rem] leading-[1.65]">
                                {!! $section['html'] !!}
                            </div>
                        </section>
                    @else
                        @php
                            $number = str_pad((string) (collect($numbered)->search(fn (array $item): bool => $item['id'] === $section['id']) + 1), 2, '0', STR_PAD_LEFT);
                        @endphp
                        <section id="{{ $section['id'] }}" data-reveal class="mt-10 scroll-mt-28 border-t border-line pt-10 first-of-type:mt-14 lg:mt-12 lg:pt-12">
                            <div class="grid gap-5 lg:grid-cols-9 lg:gap-x-8">
                                <header class="lg:col-span-3">
                                    <p class="figure-nums text-small text-muted">{{ $number }}</p>
                                    <h2 class="mt-2 text-[length:clamp(1.375rem,1.1rem+0.9vw,1.75rem)] leading-[1.2] font-normal tracking-[-0.02em] text-balance text-ink">{{ $section['label'] }}</h2>
                                </header>
                                <div class="article-body article-body--plain max-w-2xl text-[1.0625rem] leading-[1.7] lg:col-span-6">
                                    {!! $section['html'] !!}
                                </div>
                            </div>
                        </section>
                    @endif
                @endforeach

                {{-- Reaching a person --}}
                <aside data-reveal class="mt-16 grid gap-8 rounded-2xl bg-ink p-6 text-paper sm:p-8 lg:mt-20 lg:grid-cols-9 lg:gap-x-8 lg:p-10">
                    <div class="flex flex-col gap-3 lg:col-span-5">
                        <p class="text-[length:clamp(1.375rem,1.1rem+0.9vw,1.75rem)] leading-[1.2] tracking-[-0.02em]">Questions about this page?</p>
                        <p class="max-w-md text-body leading-[1.5] text-paper/70">A person will answer. Call, WhatsApp or write to us, and say which page you are asking about.</p>
                    </div>
                    <ul class="flex flex-col gap-3 text-body lg:col-span-4">
                        <li>
                            <a href="tel:{{ preg_replace('/\s+/', '', config('company.contact.phone')) }}" class="inline-flex items-center gap-3 text-paper transition-colors hover:text-paper/70">
                                <x-ui.icon name="phone" class="text-highlight" />
                                <span class="figure-nums">{{ config('company.contact.phone') }}</span>
                            </a>
                        </li>
                        @if ($whatsapp)
                            <li>
                                <a href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 text-paper transition-colors hover:text-paper/70">
                                    <x-ui.icon name="chat" class="text-highlight" />
                                    WhatsApp <span class="figure-nums">{{ $whatsapp['display'] }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($hasRealEmail)
                            <li>
                                <a href="mailto:{{ $email }}" class="inline-flex items-center gap-3 text-paper transition-colors hover:text-paper/70">
                                    <x-ui.icon name="mail" class="text-highlight" />
                                    {{ $email }}
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('contact') }}#contact" class="inline-flex items-center gap-3 text-paper transition-colors hover:text-paper/70">
                                <x-ui.icon name="arrow-right" class="text-highlight" />
                                Send us a message
                            </a>
                        </li>
                    </ul>
                </aside>
            </div>
        </div>
    </x-ui.container>
</x-ui.section>
