{{--
    A lead's timeline, newest first: the enquiry itself, every repeat
    enquiry, each move through the funnel, assignments and notes -- with who
    did it and when. Visitor and staff text is escaped; blank lines are kept.
--}}
@php
    /** @var \App\Models\Lead $lead */
    $lead = $getRecord();
    $activities = $lead->activities()->with('user')->get();
@endphp

<ol class="relative flex flex-col gap-6">
    <span aria-hidden="true" class="absolute top-2 bottom-2 left-[15px] w-px bg-gray-200"></span>

    @forelse ($activities as $activity)
        @php
            $isStaffNote = $activity->type === \App\LeadActivityType::Note;
            $isEnquiry = in_array($activity->type, [\App\LeadActivityType::Enquired, \App\LeadActivityType::EnquiredAgain], true);
        @endphp

        <li class="relative flex gap-4">
            <span @class([
                'relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full ring-4 ring-white',
                'bg-primary-50 text-primary-700' => $isEnquiry,
                'bg-amber-50 text-amber-700' => $isStaffNote,
                'bg-gray-100 text-gray-600' => ! $isEnquiry && ! $isStaffNote,
            ])>
                <x-filament::icon :icon="$activity->type->getIcon()" class="size-4" />
            </span>

            <div class="min-w-0 flex-1 pt-1">
                <p class="flex flex-wrap items-baseline gap-x-2 text-sm">
                    <span class="font-medium text-gray-950">{{ $activity->type->getLabel() }}</span>
                    <span class="text-gray-500">
                        {{ $activity->user?->name ?? ($isEnquiry ? $lead->name : 'System') }}
                        &middot;
                        <time datetime="{{ $activity->created_at?->toIso8601String() }}" title="{{ $activity->created_at?->format('j M Y, H:i') }}">{{ $activity->created_at?->diffForHumans() }}</time>
                    </span>
                </p>

                @if (filled($activity->body))
                    <div @class([
                        'mt-2 text-sm leading-relaxed whitespace-pre-line text-gray-700',
                        'rounded-lg bg-gray-50 px-4 py-3' => $isEnquiry || $isStaffNote,
                    ])>{{ $activity->body }}</div>
                @endif
            </div>
        </li>
    @empty
        <li class="text-sm text-gray-500">Nothing has happened on this lead yet.</li>
    @endforelse
</ol>
