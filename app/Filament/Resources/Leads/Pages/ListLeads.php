<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\LeadStage;
use App\Models\Lead;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tabs follow the funnel, each with its count. "Open" is the working list and
 * the default; "New" turns red when someone has waited past the response
 * target.
 */
class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Log a lead'),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Everyone who asked to be called back, and where each one is in the funnel.';
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $byStage = Lead::query()->selectRaw('stage, count(*) as total')->groupBy('stage')->pluck('total', 'stage');
        $count = fn (LeadStage $stage): int => (int) ($byStage[$stage->value] ?? 0);

        $new = $count(LeadStage::New);
        $open = $new + $count(LeadStage::Contacted) + $count(LeadStage::Application);
        $mine = Lead::query()->open()->where('assigned_to', auth()->id())->count();

        return [
            'open' => Tab::make('Open')
                ->badge($open ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->open()),
            'new' => Tab::make('New')
                ->badge($new ?: null)
                ->badgeColor(LeadResource::overdueCount() > 0 ? 'danger' : 'primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stage', LeadStage::New)->reorder()->oldest()),
            'mine' => Tab::make('Mine')
                ->badge($mine ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->open()->where('assigned_to', auth()->id())),
            'approved' => Tab::make('Approved')
                ->badge($count(LeadStage::Approved) ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stage', LeadStage::Approved)),
            'lost' => Tab::make('Lost')
                ->badge($count(LeadStage::Lost) ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stage', LeadStage::Lost)),
            'all' => Tab::make('All')
                ->badge($byStage->sum() ?: null)
                ->badgeColor('gray'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'open';
    }
}
