<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BreaksDownLeads;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Where enquiries come from, as shares of the whole, with one sentence on
 * which source converts best.
 */
class SourceChart extends ChartWidget
{
    use BreaksDownLeads;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected ?string $heading = 'Where enquiries come from';

    protected ?string $maxHeight = '16rem';

    public function getDescription(): string|Htmlable|null
    {
        return $this->insight('sources', $this->rows($this->recentLeads(), fn (Lead $lead): string => $lead->sourceLabel()));
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array{datasets: list<array<string, mixed>>, labels: list<string>}
     */
    protected function getData(): array
    {
        $rows = collect($this->rows($this->recentLeads(), fn (Lead $lead): string => $lead->sourceLabel()));

        return [
            'datasets' => [[
                'label' => 'Enquiries',
                'data' => $rows->pluck('total')->all(),
                'backgroundColor' => ['#5e2681', '#8045a8', '#a071c3', '#c3a4da', '#dccbea', '#9ca3af'],
                'borderWidth' => 2,
                'borderColor' => '#ffffff',
            ]],
            'labels' => $rows->pluck('label')->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'cutout' => '62%',
            'plugins' => ['legend' => ['position' => 'right']],
            'scales' => ['x' => ['display' => false], 'y' => ['display' => false]],
        ];
    }
}
