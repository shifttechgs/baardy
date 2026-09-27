{{--
    Star rating.

    ---------------------------------------------------------------------------
    RENDERS ONLY REAL, SOURCED RATINGS.

    It takes a score, a review count and the platform they came from, and draws
    them. It has no default score and no fallback: given nothing, it renders
    nothing. That is deliberate. A rating is a factual claim about what other
    customers said, and an invented one on a licensed lender's site is a
    consumer-protection problem, not a design placeholder.

    Whatever is passed in must come from the platform's own profile on the day
    it is read, and should be refreshed rather than left to drift.
    ---------------------------------------------------------------------------

    Stars fill proportionally: a 4.6 draws four full stars and one filled to
    60%, via a per-star gradient. Gradient ids are salted with uniqid() so two
    ratings on one page cannot collide.

    Props
      score   0-5, may be fractional
      count   number of reviews behind the score
      source  platform name, e.g. 'Google'
      href    optional link to the public profile, so the claim is checkable
      tone    'ink' (default) for light surfaces, 'inverse' for dark glass
--}}
@props([
    'score' => null,
    'count' => null,
    'source' => null,
    'href' => null,
    'tone' => 'ink',
])

@php
    $score = is_numeric($score) ? max(0, min(5, (float) $score)) : null;
    $count = is_numeric($count) ? (int) $count : null;
    $inverse = $tone === 'inverse';
    $emptyStar = $inverse ? 'rgb(255 255 255 / 0.25)' : 'var(--color-line-strong)';
@endphp

@if ($score !== null)
    @php
        $salt = uniqid('r');
        $label = trim(sprintf(
            '%s out of 5%s%s',
            rtrim(rtrim(number_format($score, 1), '0'), '.'),
            $count !== null ? ' from '.number_format($count).' review'.($count === 1 ? '' : 's') : '',
            $source ? ' on '.$source : '',
        ));
        $tag = $href ? 'a' : 'div';
    @endphp

    <{{ $tag }}
        @if ($href) href="{{ $href }}" target="_blank" rel="noopener noreferrer" @endif
        {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 rounded-xs']) }}
    >
        <span class="sr-only">{{ $label }}</span>

        <span aria-hidden="true" class="inline-flex items-center gap-0.5">
            @for ($i = 1; $i <= 5; $i++)
                @php $fill = max(0, min(1, $score - ($i - 1))); @endphp

                <svg viewBox="0 0 20 20" class="size-[1.05em] shrink-0" focusable="false">
                    @if ($fill > 0 && $fill < 1)
                        <defs>
                            <linearGradient id="{{ $salt }}-{{ $i }}">
                                <stop offset="{{ $fill * 100 }}%" stop-color="currentColor" />
                                <stop offset="{{ $fill * 100 }}%" stop-color="{{ $emptyStar }}" />
                            </linearGradient>
                        </defs>
                    @endif

                    <path
                        d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.51L10 14.22l-4.94 2.6.94-5.51-4-3.9 5.53-.8L10 1.6z"
                        fill="{{ match (true) {
                            $fill >= 1 => 'currentColor',
                            $fill <= 0 => $emptyStar,
                            default => 'url(#'.$salt.'-'.$i.')',
                        } }}"
                    />
                </svg>
            @endfor
        </span>

        <span aria-hidden="true" @class(['text-small', 'text-ink-soft' => ! $inverse, 'text-paper/80' => $inverse])>
            <span @class(['figure-nums font-medium', 'text-ink' => ! $inverse, 'text-paper' => $inverse])>{{ rtrim(rtrim(number_format($score, 1), '0'), '.') }}</span>
            @if ($count !== null)
                <span @class(['text-muted' => ! $inverse, 'text-paper/65' => $inverse])>
                    &middot; {{ number_format($count) }}{{ $source ? ' '.$source : '' }} review{{ $count === 1 ? '' : 's' }}
                </span>
            @elseif ($source)
                <span @class(['text-muted' => ! $inverse, 'text-paper/65' => $inverse])>&middot; {{ $source }}</span>
            @endif
        </span>
    </{{ $tag }}>
@endif
