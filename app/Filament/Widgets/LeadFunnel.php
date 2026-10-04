<?php

namespace App\Filament\Widgets;

use App\LeadLostReason;
use App\LeadStage;
use App\Models\Lead;
use Filament\Widgets\Widget;

/**
 * The last 30 days of leads as a funnel: how many came in, were contacted,
 * started an application and were approved, and why the rest were lost.
 * Each stage counts the leads that reached it, whatever happened next.
 */
class LeadFunnel extends Widget
{
    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected string $view = 'filament.widgets.lead-funnel';

    /**
     * @return array{steps: list<array{label: string, count: int, share: int}>, lost: array<string, int>, total: int}
     */
    protected function getViewData(): array
    {
        $leads = Lead::query()
            ->where('created_at', '>=', now()->subDays(LeadStats::DAYS)->startOfDay())
            ->get(['id', 'stage', 'lost_reason', 'first_contacted_at']);

        $total = $leads->count();
        $reachedApplication = fn (Lead $lead): bool => in_array($lead->stage, [LeadStage::Application, LeadStage::Approved], true)
            || $lead->lost_reason === LeadLostReason::Declined;

        $steps = [
            ['label' => 'Came in', 'count' => $total],
            ['label' => 'Contacted', 'count' => $leads->filter(fn (Lead $lead): bool => $lead->first_contacted_at !== null)->count()],
            ['label' => 'Started an application', 'count' => $leads->filter($reachedApplication)->count()],
            ['label' => 'Approved', 'count' => $leads->filter(fn (Lead $lead): bool => $lead->stage === LeadStage::Approved)->count()],
        ];

        return [
            'total' => $total,
            'steps' => array_map(fn (array $step): array => [
                ...$step,
                'share' => $total > 0 ? (int) round($step['count'] / $total * 100) : 0,
            ], $steps),
            'lost' => $leads
                ->filter(fn (Lead $lead): bool => $lead->stage === LeadStage::Lost)
                ->countBy(fn (Lead $lead): string => $lead->lost_reason?->getLabel() ?? 'No reason')
                ->sortDesc()
                ->all(),
        ];
    }
}
