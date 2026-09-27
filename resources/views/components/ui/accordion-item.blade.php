{{--
    An accordion row, built on native <details>/<summary>.

    Deliberately not a JavaScript widget. The native element is already
    keyboard-operable, announced correctly by screen readers, expandable
    before any script has run, and findable by the browser's in-page search.
    A shared `name` makes the group exclusive -- opening one row closes the
    others -- without a line of script. Browsers without exclusive-accordion
    support simply allow more than one row open, which is a fine fallback.

    Each row is its own rounded tile, with no outline: mist while closed (a
    shade darker on hover), paper with a soft shadow once open, so the open
    answer lifts off the list rather than just changing colour.

    MOTION is CSS only (`.accordion` in app.css): the panel animates its
    height through ::details-content with interpolate-size, the answer rises
    in once the panel is open, and the plus turns into a minus by rotating its
    vertical bar flat. Browsers without ::details-content open instantly,
    exactly as they did before.

    Props
      question  the summary text, rendered as a heading
      number    optional index shown before the question ("01")
      open      render the row open on load
      group     shared name; rows with the same name open exclusively
      level     heading tag, to keep the document outline correct
--}}
@props([
    'question',
    'number' => null,
    'open' => false,
    'group' => 'faq',
    'level' => 'h3',
])

<details
    name="{{ $group }}"
    @if ($open) open @endif
    class="accordion group rounded-xl bg-mist px-5 transition-[background-color,box-shadow] duration-300
           [&:not([open]):hover]:bg-line/60 open:bg-paper open:shadow-[0_22px_48px_-26px_rgb(21_16_25/0.3)] sm:px-7"
>
    <summary
        class="grid cursor-pointer list-none grid-cols-[1fr_auto] items-center gap-x-4 py-5 text-left
               sm:grid-cols-[2.25rem_1fr_auto] sm:gap-x-5 sm:py-6 [&::-webkit-details-marker]:hidden"
    >
        {{-- Hidden on phones: the column costs the question a third of its width. --}}
        <span class="figure-nums hidden text-small text-muted transition-colors duration-300 group-open:text-accent sm:block">
            {{ $number }}
        </span>

        <{{ $level }}
            data-word-reveal
            class="text-[length:clamp(1.0625rem,0.95rem+0.4vw,1.25rem)] leading-snug font-normal tracking-[-0.015em] text-ink"
        >
            {{ $question }}
        </{{ $level }}>

        {{-- Two bars rather than two icons: the vertical one rotates flat, so
             the plus becomes a minus in one continuous movement. --}}
        <span
            aria-hidden="true"
            class="relative flex size-9 shrink-0 items-center justify-center rounded-full bg-paper text-ink
                   transition-[background-color,color,transform] duration-300 ease-out
                   group-hover:text-accent group-open:rotate-180 group-open:bg-accent group-open:text-paper"
        >
            <span class="absolute h-[1.5px] w-3.5 rounded-full bg-current"></span>
            <span class="absolute h-3.5 w-[1.5px] rounded-full bg-current transition-transform duration-300 ease-out group-open:rotate-90"></span>
        </span>
    </summary>

    <div class="accordion-answer grid pb-6 sm:grid-cols-[2.25rem_1fr] sm:gap-x-5 sm:pb-7">
        <div class="max-w-2xl pr-12 text-body text-ink-soft sm:col-start-2">
            {{ $slot }}
        </div>
    </div>
</details>
