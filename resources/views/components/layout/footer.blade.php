{{--
    Site footer.

    The dark foot of the page. Together with the closing call to action it is
    the page's only tonal shift -- everything above it stays on paper, divided
    by rules.

    It closes on an oversized wordmark that spans the full width and sits flush
    to the bottom edge. See the note on that block for how it is set.

    Every link goes somewhere real (config/company.php -> footer). Anything
    not yet confirmed by the client stays hidden rather than shown dead: the
    email line until company.contact.email is a real address, and each social
    icon until its profile has an href.
--}}
@php
    $email = config('company.contact.email');
    $hasRealEmail = $email && ! str_ends_with($email, 'example.com');
    $socials = collect(config('company.social'))->filter(fn (array $social): bool => filled($social['href']) && $social['href'] !== '#');
@endphp

<footer class="overflow-hidden bg-ink text-paper">
    <x-ui.container>
        {{-- Two columns: identity and contact on the left, link columns on the right. --}}
        <div class="grid gap-12 py-14 lg:grid-cols-12 lg:gap-10 lg:py-16">

            {{-- Identity and contact --}}
            <div class="flex flex-col gap-5 lg:col-span-4">
                <x-ui.logo tone="inverse" />

                <p class="max-w-xs text-small text-ink-muted">
                    {{ config('company.description') }}
                </p>

                <ul class="flex flex-col gap-3 text-small">
                    <li>
                        <a
                            href="tel:{{ preg_replace('/\s+/', '', config('company.contact.phone')) }}"
                            class="inline-flex items-center gap-2.5 rounded-xs text-paper transition-colors hover:text-accent-tint"
                        >
                            <x-ui.icon name="phone" class="text-ink-muted" />
                            {{ config('company.contact.phone') }}
                        </a>
                        <span class="ml-[1.9em] block text-micro text-ink-muted">
                            {{ config('company.contact.phone_label') }}
                        </span>
                    </li>
                    @if ($hasRealEmail)
                    <li>
                        <a
                            href="mailto:{{ $email }}"
                            class="inline-flex items-center gap-2.5 rounded-xs text-paper transition-colors hover:text-accent-tint"
                        >
                            <x-ui.icon name="mail" class="text-ink-muted" />
                            {{ $email }}
                        </a>
                    </li>
                    @endif
                </ul>

            </div>

            {{-- Link columns. Two across from the smallest screen rather than
                 one: the labels are short, and stacking them single-file was
                 adding hundreds of pixels on a phone for no gain. --}}
            <div class="grid grid-cols-2 gap-x-8 gap-y-10 lg:col-span-8 lg:grid-cols-4">
                @foreach (config('company.footer') as $heading => $links)
                    <nav aria-labelledby="footer-{{ Str::slug($heading) }}">
                        <h2
                            id="footer-{{ Str::slug($heading) }}"
                            class="text-small font-medium text-paper"
                        >
                            {{ $heading }}
                        </h2>

                        <ul class="mt-4 flex flex-col gap-3">
                            @foreach ($links as $link)
                                <li>
                                    <a
                                        href="{{ $link['href'] }}"
                                        class="rounded-xs text-small text-ink-muted transition-colors hover:text-paper"
                                    >
                                        {{ $link['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>
        </div>

        {{--
            Legal foot.

            The regulatory disclosure paragraph that used to sit here was
            removed at the client's request. Two things went with it, and both
            need somewhere to live before launch:

              1. The licensing statement -- registered company number, regulator
                 and licence number. This is the disclosure a regulator and a
                 cautious borrower both look for in a footer. The values are
                 still in config/company.php -> compliance.
              2. The site-wide "every figure here is an illustrative
                 placeholder" disclaimer. Individual sections still carry their
                 own disclosures, but the blanket one is gone.

            See docs/COMPANY.md, "Compliance".
        --}}
        <div class="border-t border-ink-line py-8">
            <div class="flex flex-col gap-4 text-micro text-ink-muted sm:flex-row sm:items-center sm:justify-between">
                <p>
                    &copy; {{ now()->year }} {{ config('company.legal_name') }}. All rights reserved.
                </p>

                <div class="flex items-center gap-5">
                    @if ($socials->isNotEmpty())
                        <ul class="flex items-center gap-1">
                            @foreach ($socials as $social)
                                <li>
                                    <a
                                        href="{{ $social['href'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex size-9 items-center justify-center rounded-sm text-ink-muted
                                               transition-colors hover:bg-ink-line hover:text-paper"
                                    >
                                        <span class="sr-only">{{ config('company.name') }} on {{ $social['label'] }}</span>
                                        <x-ui.icon :name="$social['icon']" class="size-4" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Build credit, at the far right. External, so it opens in a
                         new tab and carries rel="noopener" to keep the opener out of
                         the new document. --}}
                    <p>
                        Developed by
                        <a
                            href="https://shifttechgs.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-xs text-paper transition-colors hover:text-accent-tint"
                        >ShiftTech</a>
                    </p>
                </div>
            </div>
        </div>

        {{--
            Oversized wordmark.

            Set as SVG rather than CSS text because the mark has to span the
            container exactly at every width, and `textLength` guarantees that
            where a font-size in `vw` only approximates it.

            lengthAdjust="spacing" is the important part: it reaches the target
            width by opening or closing the letter-spacing and NEVER by
            stretching the glyphs. The alternative, "spacingAndGlyphs",
            distorts the letterforms -- on a wordmark that is the difference
            between typography and a squashed logo.

            The viewBox is deliberately shorter than the type's em box, so the
            mark is cropped at the baseline and reads as bleeding off the bottom
            of the page. 250 units of the 320px type showed the letters whole and
            cost 223px of footer height; 168 crops about a third off their feet,
            which is the point of the device and returns ~75px. The name is set in
            caps to match the supplied artwork, which also means no descenders
            drop into the crop.

            Held at low contrast on purpose: at this size it is architecture,
            not an announcement. aria-hidden, because the accessible name is
            already carried by the logo at the top of the footer.
        --}}
        <div class="pt-4 text-ink-line sm:pt-6">
            {{--
                font-size is tuned so the string's natural width lands near the
                1200 viewBox, which keeps lengthAdjust="spacing" making small
                corrections rather than forcing conspicuous gaps between the
                letters. Six characters across 1200 units needs a large size;
                change the size if the word ever changes.
            --}}
            <svg
                viewBox="0 0 1200 168"
                class="block w-full font-sans"
                aria-hidden="true"
                focusable="false"
            >
                <text
                    x="0"
                    y="240"
                    textLength="1200"
                    lengthAdjust="spacing"
                    font-size="320"
                    font-weight="600"
                    fill="currentColor"
                >BAARDY</text>
            </svg>
        </div>
    </x-ui.container>
</footer>
