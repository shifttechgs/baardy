{{--
    Form problem notice.

    Shown when a form could not be sent or needs another look. A soft red
    fill with a round mark, a headline and a plain-language line, so it reads
    as a person talking rather than a system message. With `fallback`, it
    also offers the two ways to reach the team directly, because a failed
    form should never be a dead end.

    Props
      title     the headline
      fallback  add WhatsApp and call links (default false)

    Slots
      (default)  the explanation

    Attributes pass through to the wrapper (x-show, x-cloak, ...).
--}}
@props(['title', 'fallback' => false])

@php
    $whatsapp = $fallback
        ? collect(config('company.branches'))->pluck('phones')->flatten(1)->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'))
        : null;
    $phone = config('company.contact.phone');
@endphp

<div {{ $attributes->class('flex items-start gap-4 rounded-xl bg-danger/5 p-4 sm:p-5') }} role="alert">
    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-danger text-paper">
        <x-ui.icon name="alert" class="size-5" />
    </span>

    <div class="flex min-w-0 flex-1 flex-col gap-1">
        <p class="text-body font-medium text-ink">{{ $title }}</p>
        <p class="text-small text-ink-soft">{{ $slot }}</p>

        @if ($fallback)
            <p class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-small font-medium">
                @if ($whatsapp)
                    <a href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}" target="_blank" rel="noopener noreferrer" class="text-accent underline-offset-4 hover:underline">WhatsApp us</a>
                @endif
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="text-accent underline-offset-4 hover:underline">Call {{ $phone }}</a>
            </p>
        @endif
    </div>
</div>
