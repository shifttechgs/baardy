{{--
    Form confirmation.

    The "it worked" state shared by the site's forms: a purple seal whose tick
    draws itself, a headline, a line of reassurance and, optionally, a
    reference and a follow-up action. Flat, in keeping with the rest of the
    site: fills only, no borders or shadows.

    Props
      title  the headline

    Slots
      (default)  the reassurance line
      reference  a reference the visitor can quote (optional)
      actions    a follow-up link or button (optional)

    Attributes pass through to the wrapper, so Alpine's x-show and friends
    work on it.
--}}
@props(['title'])

<div {{ $attributes->class('flex min-h-[26rem] flex-col items-start justify-center gap-6') }} role="status">
    <span class="relative flex size-16 items-center justify-center rounded-full bg-accent text-paper">
        <span class="seal-pulse absolute inset-0 rounded-full bg-accent" aria-hidden="true"></span>
        <svg viewBox="0 0 18 18" class="relative size-7" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3.5 9.5 7 13l7.5-8" pathLength="1" class="seal-tick" />
        </svg>
    </span>

    <div class="flex flex-col gap-2">
        <h3 class="text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink">{{ $title }}</h3>
        <p class="max-w-md text-lead text-muted">{{ $slot }}</p>
    </div>

    @isset($reference)
        {{ $reference }}
    @endisset

    @isset($actions)
        {{ $actions }}
    @endisset
</div>
