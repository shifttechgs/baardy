<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Promotion;
use App\PromotionStatus;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The four numbers that say what needs attention: what is live, what is
 * waiting for sign-off, what is scheduled, and what ends this week. Each
 * links to the promotions list.
 */
class PromotionStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $list = PromotionResource::getUrl('index');

        $live = Promotion::query()->live()->count();
        $awaiting = Promotion::query()->where('status', PromotionStatus::AwaitingApproval)->count();
        $scheduled = Promotion::query()
            ->where('status', PromotionStatus::Approved)
            ->where('starts_at', '>', now())
            ->count();
        $endingSoon = Promotion::query()->live()->where('ends_at', '<=', now()->addWeek())->count();

        return [
            Stat::make('Live now', $live)
                ->description($live === 1 ? 'promotion on the site' : 'promotions on the site')
                ->descriptionIcon(Heroicon::OutlinedSignal)
                ->color($live > 0 ? 'success' : 'gray')
                ->url($list),
            Stat::make('Awaiting approval', $awaiting)
                ->description($awaiting > 0 ? 'need signing off' : 'nothing to sign off')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($awaiting > 0 ? 'warning' : 'gray')
                ->url($list),
            Stat::make('Scheduled', $scheduled)
                ->description('approved, not started yet')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('gray')
                ->url($list),
            Stat::make('Ending this week', $endingSoon)
                ->description($endingSoon > 0 ? 'will leave the site within 7 days' : 'none ending soon')
                ->descriptionIcon(Heroicon::OutlinedFlag)
                ->color($endingSoon > 0 ? 'danger' : 'gray')
                ->url($list),
        ];
    }
}
