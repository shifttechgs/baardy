{{--
    Where a lead is in the funnel, at a glance: New -> Contacted -> Application
    -> Approved, with the finished steps ticked and the current one lit. A lead
    closed as lost shows where it stopped and why. Under it, one line on timing:
    how long a new lead has waited (red once past the response target), or how
    quickly it was first contacted.
--}}
@php
    /** @var \App\Models\Lead $lead */
    $lead = $getRecord();
    $stage = $lead->stage;
    $lost = $stage === \App\LeadStage::Lost;

    $steps = [
        \App\LeadStage::New,
        \App\LeadStage::Contacted,
        \App\LeadStage::Application,
        \App\LeadStage::Approved,
    ];

    // The furthest step a lost lead reached, so the bar shows where it stopped.
    $reached = match (true) {
        ! $lost => array_search($stage, $steps, true),
        $lead->lost_reason === \App\LeadLostReason::Declined => 2,
        $lead->first_contacted_at !== null => 1,
        default => 0,
    };

    $hours = \App\Filament\Resources\Leads\LeadResource::RESPONSE_TARGET_HOURS;
    $waitingTooLong = $stage === \App\LeadStage::New && $lead->created_at->lt(now()->subHours($hours));
    $parts = ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2];
@endphp

<div class="flex flex-col gap-4">
    <ol class="grid grid-cols-4 gap-2" aria-label="Funnel progress">
        @foreach ($steps as $index => $step)
            @php
                $done = $index < $reached || (! $lost && $stage === \App\LeadStage::Approved && $index <= $reached);
                $current = ! $lost && $index === $reached && $stage !== \App\LeadStage::Approved;
                $stopped = $lost && $index === $reached;
            @endphp

            <li class="flex flex-col gap-2" @if ($current) aria-current="step" @endif>
                <span @class([
                    'h-1.5 rounded-full',
                    'bg-primary-600' => $done || $current,
                    'bg-gray-300' => $stopped,
                    'bg-gray-200' => ! $done && ! $current && ! $stopped,
                ])></span>

                <span class="flex items-center gap-1.5 text-sm">
                    @if ($done)
                        <x-filament::icon icon="heroicon-m-check-circle" class="size-4 text-primary-600" />
                    @elseif ($stopped)
                        <x-filament::icon icon="heroicon-m-x-circle" class="size-4 text-gray-400" />
                    @endif

                    <span @class([
                        'font-medium text-gray-950' => $current,
                        'text-gray-700' => $done,
                        'text-gray-500' => ! $done && ! $current,
                    ])>{{ $step->getLabel() }}</span>
                </span>
            </li>
        @endforeach
    </ol>

    <p @class([
        'flex items-center gap-2 text-sm',
        'text-danger-600' => $waitingTooLong,
        'text-gray-600' => ! $waitingTooLong,
    ])>
        @if ($lost)
            <x-filament::icon icon="heroicon-o-x-circle" class="size-4 shrink-0 text-gray-400" />
            Closed as lost: {{ $lead->lost_reason?->getLabel() ?? 'no reason recorded' }}.
        @elseif ($stage === \App\LeadStage::Approved)
            <x-filament::icon icon="heroicon-o-check-badge" class="size-4 shrink-0 text-success-600" />
            Loan approved
            @if ($lead->closed_at)
                {{ $lead->closed_at->diffForHumans() }}.
            @endif
        @elseif ($stage === \App\LeadStage::New)
            <x-filament::icon :icon="$waitingTooLong ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-clock'" class="size-4 shrink-0" />
            Waiting {{ $lead->created_at->diffForHumans($parts) }} for a first call
            @if ($waitingTooLong)
                &middot; past the {{ $hours }}-hour target
            @endif
        @else
            <x-filament::icon icon="heroicon-o-clock" class="size-4 shrink-0" />
            First contacted
            @if ($lead->first_contacted_at)
                {{ $lead->created_at->diffForHumans($lead->first_contacted_at, $parts) }} after it came in.
            @endif
        @endif
    </p>
</div>
