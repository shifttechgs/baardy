<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BreaksDownLeads;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Enquiries against approved loans for each loan product, as horizontal bars
 * so the product names stay readable.
 */
class ProductChart extends ChartWidget
{
    use BreaksDownLeads;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Loan products';

    protected ?string $maxHeight = '18rem';

    public function getDescription(): string|Htmlable|null
    {
        return $this->insight('products', $this->rows($this->recentLeads(), fn (Lead $lead): string => $lead->interest));
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
        $rows = collect($this->rows($this->recentLeads(), fn (Lead $lead): string => $lead->interest));

        return [
            'datasets' => [
                ['label' => 'Enquiries', 'data' => $rows->pluck('total')->all(), 'backgroundColor' => '#5e2681', 'borderRadius' => 6, 'maxBarThickness' => 22],
                ['label' => 'Approved', 'data' => $rows->pluck('approved')->all(), 'backgroundColor' => '#12805c', 'borderRadius' => 6, 'maxBarThickness' => 22],
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
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => [
                'x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                'y' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
