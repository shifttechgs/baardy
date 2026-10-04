<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->modules() as $module)
            <article class="flex flex-col gap-4 rounded-xl bg-white p-6 ring-1 ring-gray-950/5">
                <div class="flex items-start justify-between gap-3">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700">
                        <x-filament::icon :icon="$module['icon']" class="size-6" />
                    </span>

                    <span @class([
                        'rounded-full px-2.5 py-1 text-xs font-medium',
                        'bg-success-50 text-success-700' => $module['status'] === 'Live',
                        'bg-primary-50 text-primary-700' => $module['status'] === 'Next',
                        'bg-gray-100 text-gray-600' => $module['status'] === 'Planned',
                    ])>{{ $module['status'] }}</span>
                </div>

                <div class="flex flex-col gap-2">
                    <h2 class="text-lg font-medium tracking-tight text-gray-950">{{ $module['title'] }}</h2>
                    <p class="text-sm leading-relaxed text-gray-600">{{ $module['summary'] }}</p>
                </div>

                <p class="mt-auto border-t border-gray-100 pt-4 text-sm text-gray-500">
                    <span class="font-medium text-gray-700">Answers:</span> {{ $module['answers'] }}
                </p>

                <p class="-mx-6 -mb-6 flex items-center gap-2 rounded-b-xl border-t border-gray-100 bg-primary-50 px-6 py-3.5 text-sm">
                    <span class="shrink-0 whitespace-nowrap font-semibold text-primary-700">30-day free trial</span>
                    <span class="text-gray-600">Try it with your own clients and loans.</span>
                </p>
            </article>
        @endforeach
    </div>
</x-filament-panels::page>
