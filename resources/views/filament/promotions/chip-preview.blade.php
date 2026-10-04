{{-- A live preview of the bar above the site header, as it will read. --}}
@php
    $summary = trim((string) $get('summary'));
    $endsAt = filled($get('ends_at')) ? \Carbon\CarbonImmutable::parse($get('ends_at')) : null;
@endphp

<div class="flex flex-col gap-2">
    <p class="text-sm font-medium text-gray-950">Preview</p>

    <div class="overflow-hidden rounded-xl bg-[#1c1420] ring-1 ring-gray-950/5">
        <div class="flex h-10 items-center justify-center gap-2.5 bg-[#5e2681] px-4 text-sm text-white">
            <span class="shrink-0 rounded-full bg-[#f16a26] px-2.5 py-0.5 text-xs font-medium">Limited offer</span>
            <span @class(['truncate', 'text-white/50' => $summary === ''])>{{ $summary !== '' ? $summary : 'Your one-line pitch appears here' }}</span>
            @if ($endsAt)
                <span class="hidden shrink-0 text-white/70 sm:inline">&middot; Ends {{ $endsAt->format('j M') }}</span>
            @endif
            <span aria-hidden="true">&rarr;</span>
        </div>
        <div class="h-8 bg-[radial-gradient(ellipse_at_0%_100%,rgb(94_38_129/0.45),transparent_60%)]"></div>
    </div>
</div>
