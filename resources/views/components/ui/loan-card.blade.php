{{--
    A loan as a card: photograph, name, who it is for, the range and term, and
    a circled arrow, the whole card being the link to the loan's own page.

    Props
      loan   a loan from LoanController (marketing product + loans page copy)
      promo  a live Promotion on this loan, if any
      range  show the amount and term on the photograph (default true)
--}}
@props(['loan', 'promo' => null, 'range' => true])

<a
    href="{{ route('loans.show', $loan['slug']) }}"
    {{ $attributes->class('group/card flex flex-col gap-4 rounded-2xl bg-mist p-2 pb-5 transition-[background-color,transform] duration-300 hover:-translate-y-0.5 hover:bg-paper') }}
>
    <span class="relative block aspect-[4/3] overflow-hidden rounded-xl bg-line">
        <img
            src="{{ asset(str_replace('-1600.webp', '-480.webp', $loan['image']['src'])) }}"
            srcset="{{ asset(str_replace('-1600.webp', '-480.webp', $loan['image']['src'])) }} 480w, {{ asset($loan['image']['src']) }} 1600w"
            sizes="(min-width: 64rem) 28rem, (min-width: 40rem) 45vw, 100vw"
            alt="{{ $loan['image']['alt'] }}"
            width="480"
            height="360"
            loading="lazy"
            decoding="async"
            class="size-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/card:scale-[1.06]"
            style="object-position: {{ $loan['image']['position'] }}"
        >
        @if ($promo)
            <span class="absolute top-2 left-2 rounded-full bg-highlight px-2.5 py-0.5 text-micro font-medium text-paper">Limited offer</span>
        @endif
        @if ($range)
            <span class="figure-nums absolute right-2 bottom-2 left-2 rounded-lg bg-ink/45 px-3 py-2 text-micro text-paper backdrop-blur-xl backdrop-saturate-150">
                <x-ui.money :amount="$loan['min']" /> &ndash; <x-ui.money :amount="$loan['max']" /> &middot; {{ $loan['term'] }}
            </span>
        @endif
    </span>

    <span class="flex flex-1 flex-col justify-between gap-5 px-3">
        <span class="flex flex-col gap-1.5">
            <span class="text-body font-medium text-ink">{{ $loan['name'] }}</span>
            <span class="text-small leading-[1.45] text-ink-soft">{{ $loan['best_for'] }}</span>
        </span>
        <span class="flex items-end justify-between gap-3">
            <span class="text-small text-muted">View loan details</span>
            <span aria-hidden="true" class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-accent text-paper transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover/card:translate-x-0.5">
                <x-ui.icon name="arrow-right" />
            </span>
        </span>
    </span>
</a>
