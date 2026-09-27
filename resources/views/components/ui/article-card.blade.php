{{--
    An Insights article card: the homepage strip, the /insights index and the
    "more insights" row at the foot of an article.

    Editorial, after stitch.money/blog: the text sits below the photograph,
    never on it. A two-column meta line (date, then category), then a light,
    large title. No chips, no arrows -- the photograph and the type carry it.

    The whole card is clickable through the title link's ::after overlay, so
    there is one link per card for screen readers rather than three.

    Props
      article  one entry from config('insights.articles')
      lead     the featured article: wider photograph, larger title, excerpt
      sizes    the image `sizes` hint for where the card is placed
--}}
@props([
    'article',
    'lead' => false,
    'sizes' => '(min-width: 64rem) 22rem, (min-width: 40rem) 50vw, 100vw',
])

@php
    $published = \Illuminate\Support\Carbon::parse($article['published']);
    $image = 'images/insights/'.$article['image'];
@endphp

<article {{ $attributes->merge(['class' => 'group relative flex flex-col']) }}>
    <div class="relative aspect-[16/10] overflow-hidden rounded-xl bg-mist">
        <img
            src="{{ asset($image.'-800.webp') }}"
            srcset="{{ asset($image.'-480.webp') }} 480w, {{ asset($image.'-800.webp') }} 800w{{ $lead ? ', '.asset($image.'-1600.webp').' 1600w' : '' }}"
            sizes="{{ $sizes }}"
            width="800"
            height="500"
            alt="{{ $article['alt'] }}"
            loading="lazy"
            decoding="async"
            class="size-full object-cover transition-transform duration-[1200ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]"
        >
    </div>

    <p class="figure-nums mt-4 grid grid-cols-2 gap-4 text-small text-muted {{ $lead ? 'max-w-md' : '' }}">
        <time datetime="{{ $published->toDateString() }}">{{ $published->format('F j, Y') }}</time>
        <span>{{ $article['category'] }}</span>
    </p>

    <h3 data-word-reveal class="mt-3 {{ $lead ? 'max-w-xl text-[length:clamp(1.625rem,1.2rem+1.2vw,2.25rem)] leading-[1.15] tracking-[-0.025em]' : 'text-[length:clamp(1.125rem,1rem+0.4vw,1.3125rem)] leading-snug tracking-[-0.015em]' }} font-light text-balance text-ink">
        <a
            href="{{ route('insights.show', $article['slug']) }}"
            class="bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_1px] bg-left-bottom bg-no-repeat
                   transition-[background-size] duration-500 ease-out after:absolute after:inset-0
                   group-hover:bg-[length:100%_1px]"
        >{{ $article['title'] }}</a>
    </h3>

    @if ($lead)
        <p class="mt-3 line-clamp-2 max-w-lg text-body text-muted">{{ $article['excerpt'] }}</p>
    @endif
</article>
