{{--
    A promotion card, in the same editorial language as the Insights cards:
    photograph, a two-column meta line (the loan, the end date), then a light
    title and the one-line summary. One link per card, through the title's
    ::after overlay.

    Props
      promotion  an App\Models\Promotion
--}}
@props(['promotion'])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col']) }}>
    <div class="relative aspect-[16/10] overflow-hidden rounded-xl bg-accent-tint">
        @if ($promotion->imageUrl())
            <img src="{{ $promotion->imageUrl() }}" alt="{{ $promotion->imageAlt() }}" loading="lazy" decoding="async" width="800" height="500"
                 class="size-full object-cover transition-transform duration-[1200ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]">
        @else
            <div class="flex size-full items-end p-6">
                <img src="{{ asset('images/baardy-mark.png') }}" alt="" class="size-10 opacity-80">
            </div>
        @endif

        <span class="absolute top-4 left-4 rounded-full bg-paper px-3 py-1 text-micro font-medium text-highlight">Limited offer</span>
    </div>

    <p class="figure-nums mt-4 grid grid-cols-2 gap-4 text-small text-muted">
        <span>{{ $promotion->product ?? 'All loans' }}</span>
        <span>Ends <time datetime="{{ $promotion->ends_at->toDateString() }}">{{ $promotion->ends_at->format('j M Y') }}</time></span>
    </p>

    <h3 class="mt-3 text-[length:clamp(1.125rem,1rem+0.4vw,1.3125rem)] leading-snug font-light tracking-[-0.015em] text-balance text-ink">
        <a
            href="{{ route('promotions.show', $promotion) }}"
            class="bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_1px] bg-left-bottom bg-no-repeat transition-[background-size] duration-500 ease-out after:absolute after:inset-0 group-hover:bg-[length:100%_1px]"
        >{{ $promotion->title }}</a>
    </h3>

    <p class="mt-2 text-body text-muted">{{ $promotion->summary }}</p>
</article>
