<?php

namespace App\Filament\Widgets;

use App\LeadStage;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;

/**
 * Enquiries per day against loans approved per day over the last 30 days:
 * is demand moving, and is the branch turning it into loans.
 */
class LeadTrend extends ChartWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected ?string $heading = 'Enquiries and approvals';

    protected ?string $description = 'Per day, last 30 days.';

    protected ?string $maxHeight = '16rem';

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array{datasets: list<array<string, mixed>>, labels: list<string>}
     */
    protected function getData(): array
    {
        $since = now()->subDays(LeadStats::DAYS - 1)->startOfDay();

        $came = Lead::query()->where('created_at', '>=', $since)->pluck('created_at');
        $approved = Lead::query()->where('stage', LeadStage::Approved)->where('closed_at', '>=', $since)->pluck('closed_at');

        $days = collect(range(LeadStats::DAYS - 1, 0))->map(fn (int $daysAgo) => now()->subDays($daysAgo));

        return [
            'datasets' => [
                [
                    'label' => 'Enquiries',
                    'data' => $days->map(fn ($day): int => $came->filter(fn ($at): bool => $at->isSameDay($day))->count())->all(),
                    'borderColor' => '#5e2681',
                    'backgroundColor' => 'rgba(94, 38, 129, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Approved',
                    'data' => $days->map(fn ($day): int => $approved->filter(fn ($at): bool => $at->isSameDay($day))->count())->all(),
                    'borderColor' => '#12805c',
                    'tension' => 0.35,
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $days->map(fn ($day): string => $day->format('j M'))->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
