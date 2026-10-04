<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Promotions\PromotionResource;
use App\Filament\Widgets\BranchChart;
use App\Filament\Widgets\LeadFunnel;
use App\Filament\Widgets\LeadTrend;
use App\Filament\Widgets\OwnerSnapshot;
use App\Filament\Widgets\ProductChart;
use App\Filament\Widgets\SourceChart;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * The owner's home, built around decisions rather than counts: what needs
 * attention today, whether the last 30 days beat the 30 before, where
 * enquiries drop out, and which branches, products and sources are working.
 */
class Dashboard extends BaseDashboard
{
    public function getHeading(): string|Htmlable
    {
        $hour = (int) now()->format('G');
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        $name = str(auth()->user()?->name ?? '')->before(' ')->toString();

        return trim("{$greeting}, {$name}", ', ');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'What needs you today, and how the business is trending.';
    }

    /**
     * Listed explicitly so nothing else the panel discovers lands on the home page.
     *
     * @return list<class-string>
     */
    public function getWidgets(): array
    {
        return [
            OwnerSnapshot::class,
            LeadFunnel::class,
            LeadTrend::class,
            BranchChart::class,
            SourceChart::class,
            ProductChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['md' => 2, 'xl' => 4];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('logLead')
                ->label('Log a lead')
                ->icon(Heroicon::OutlinedInboxArrowDown)
                ->color('gray')
                ->url(LeadResource::getUrl('create')),
            Action::make('newPromotion')
                ->label('New promotion')
                ->icon(Heroicon::OutlinedPlus)
                ->url(PromotionResource::getUrl('create')),
        ];
    }
}
