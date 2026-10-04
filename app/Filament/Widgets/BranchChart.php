<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BreaksDownLeads;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Enquiries against approved loans, branch by branch, with one sentence on
 * which branch converts best.
 */
class BranchChart extends ChartWidget
{
    use BreaksDownLeads;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected ?string $heading = 'Branches';

    protected ?string $maxHeight = '16rem';

    public function getDescription(): string|Htmlable|null
    {
        return $this->insight('branches', $this->rows($this->recentLeads(), fn (Lead $lead): string => $lead->branch));
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array{datasets: list<array<string, mixed>>, labels: list<string>}
     */
    protected function getData(): array
    {
        $rows = collect($this->rows($this->recentLeads(), fn (Lead $lead): string => $lead->branch));

        return [
            'datasets' => [
                ['label' => 'Enquiries', 'data' => $rows->pluck('total')->all(), 'backgroundColor' => '#5e2681', 'borderRadius' => 6, 'maxBarThickness' => 36],
                ['label' => 'Approved', 'data' => $rows->pluck('approved')->all(), 'backgroundColor' => '#12805c', 'borderRadius' => 6, 'maxBarThickness' => 36],
            ],
            'labels' => $rows->pluck('label')->all(),
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
