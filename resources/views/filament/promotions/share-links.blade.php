{{--
    One tagged link per channel, each with a Copy button. A link carries
    utm_source (the channel), utm_medium and utm_campaign (this promotion), so
    enquiries it brings in are reported as "Promotion: <name> (<Channel>)" on the
    dashboard. The link only works while the promotion is live; before that the
    public page is not found, and the note says so.
--}}
@php
    /** @var \App\Models\Promotion $promotion */
    $promotion = $getRecord();
    $live = $promotion->isLive();
@endphp

<div class="flex flex-col gap-3">
    @unless ($live)
        <p class="rounded-lg bg-warning-50 px-3 py-2 text-sm text-warning-700">
            These links work once the promotion is live. Until then the page is not found.
        </p>
    @endunless

    <ul class="flex flex-col gap-2">
        @foreach (\App\Models\Promotion::SHARE_CHANNELS as $source => $channel)
            @php $url = $promotion->trackedUrl($source); @endphp

            <li
                x-data="{
                    copied: false,
                    async copy() {
                        try {
                            await navigator.clipboard.writeText(@js($url));
                        } catch (error) {
                            const field = this.$refs.field;
                            field.select();
                            document.execCommand('copy');
                        }

                        this.copied = true;
                        setTimeout(() => this.copied = false, 1800);
                    },
                }"
                class="flex flex-col gap-1.5"
            >
                <span class="text-xs font-medium text-gray-700">{{ $channel['label'] }}</span>

                <div class="flex items-center gap-2">
                    <input
                        x-ref="field"
                        type="text"
                        readonly
                        value="{{ $url }}"
                        aria-label="{{ $channel['label'] }} link"
                        x-on:focus="$el.select()"
                        class="min-w-0 flex-1 truncate rounded-lg border-0 bg-gray-50 px-3 py-2 text-xs text-gray-600 ring-1 ring-gray-200 focus:ring-primary-600"
                    >

                    <button
                        type="button"
                        x-on:click="copy()"
                        class="inline-flex w-24 shrink-0 items-center justify-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-medium text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50"
                        x-bind:class="copied && 'text-success-700 ring-success-300'"
                    >
                        <span x-show="! copied" class="inline-flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-m-clipboard-document" class="size-4" />
                            Copy
                        </span>
                        <span x-show="copied" x-cloak class="inline-flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-m-check" class="size-4" />
                            Copied
                        </span>
                    </button>
                </div>
            </li>
        @endforeach
    </ul>

    <p class="text-xs text-gray-500">
        Paste the right one wherever you post it. Enquiries then show on the dashboard as
        "Promotion: {{ $promotion->title }} (Facebook)", and so on.
    </p>
</div>
