<x-filament-widgets::widget>
    <x-filament::section heading="Funnel" description="Leads from the last 30 days, by the furthest stage each one reached.">
        @if ($total === 0)
            <p class="text-sm text-gray-500">No leads in the last 30 days yet. Enquiries from the website will show here.</p>
        @else
            <ol class="flex flex-col gap-4">
                @foreach ($steps as $step)
                    <li class="grid grid-cols-[minmax(0,11rem)_1fr_auto] items-center gap-4 text-sm">
                        <span class="truncate text-gray-700">{{ $step['label'] }}</span>
                        <span class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                            <span class="block h-full rounded-full bg-primary-600" style="width: {{ max($step['share'], $step['count'] > 0 ? 2 : 0) }}%"></span>
                        </span>
                        <span class="w-20 text-right tabular-nums">
                            <span class="font-medium text-gray-950">{{ $step['count'] }}</span>
                            <span class="text-gray-500">&middot; {{ $step['share'] }}%</span>
                        </span>
                    </li>
                @endforeach
            </ol>

            @if ($lost !== [])
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <p class="text-xs font-medium tracking-wide text-gray-500 uppercase">Lost, and why</p>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($lost as $reason => $count)
                            <li class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">{{ $reason }} <span class="font-medium text-gray-950">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
