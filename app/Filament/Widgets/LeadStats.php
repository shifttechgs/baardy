<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leads\LeadResource;
use App\LeadStage;
use App\Models\Lead;
use Carbon\CarbonInterface;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The lead numbers for the last 30 days: how many came in, who is still
 * waiting for a first call, how fast the team gets to them, and how many
 * became loans.
 */
class LeadStats extends StatsOverviewWidget
{
    public const DAYS = 30;

    protected static ?int $sort = -3;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $list = LeadResource::getUrl('index');
        $since = now()->subDays(self::DAYS)->startOfDay();

        $recent = Lead::query()->where('created_at', '>=', $since)->get(['id', 'stage', 'created_at', 'first_contacted_at']);

        $perDay = collect(range(self::DAYS - 1, 0))
            ->map(fn (int $daysAgo): int => $recent->filter(fn (Lead $lead): bool => $lead->created_at->isSameDay(now()->subDays($daysAgo)))->count())
            ->all();

        $waiting = Lead::query()->where('stage', LeadStage::New)->count();
        $overdue = LeadResource::overdueCount();

        $responseMinutes = $recent
            ->filter(fn (Lead $lead): bool => $lead->first_contacted_at !== null)
            ->map(fn (Lead $lead): float => $lead->created_at->diffInMinutes($lead->first_contacted_at))
            ->values();
        $median = $responseMinutes->isEmpty() ? null : (float) $responseMinutes->median();

        $approved = $recent->where('stage', LeadStage::Approved)->count();
        $conversion = $recent->isEmpty() ? 0 : (int) round($approved / $recent->count() * 100);

        return [
            Stat::make('Leads, last '.self::DAYS.' days', $recent->count())
                ->description($recent->filter(fn (Lead $lead): bool => $lead->created_at->gte(now()->subDays(7)))->count().' in the last 7 days')
                ->descriptionIcon(Heroicon::OutlinedInboxArrowDown)
                ->chart($perDay)
                ->color('primary')
                ->url($list),
            Stat::make('Waiting for a call', $waiting)
                ->description(match (true) {
                    $overdue > 0 => "{$overdue} past the ".LeadResource::RESPONSE_TARGET_HOURS.'-hour target',
                    $waiting > 0 => 'all within the '.LeadResource::RESPONSE_TARGET_HOURS.'-hour target',
                    default => 'everyone has been contacted',
                })
                ->descriptionIcon($overdue > 0 ? Heroicon::OutlinedExclamationTriangle : Heroicon::OutlinedPhone)
                ->color($overdue > 0 ? 'danger' : ($waiting > 0 ? 'warning' : 'success'))
                ->url(LeadResource::getUrl('index', ['activeTab' => 'new'])),
            Stat::make('Time to first contact', $median === null ? '—' : self::duration($median))
                ->description('median, last '.self::DAYS.' days')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($median !== null && $median > LeadResource::RESPONSE_TARGET_HOURS * 60 ? 'warning' : 'gray'),
            Stat::make('Became loans', $conversion.'%')
                ->description($approved.' approved of '.$recent->count())
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color($approved > 0 ? 'success' : 'gray')
                ->url(LeadResource::getUrl('index', ['activeTab' => 'approved'])),
        ];
    }

    private static function duration(float $minutes): string
    {
        if ($minutes < 1) {
            return 'under 1 min';
        }

        return now()->subMinutes((int) round($minutes))->diffForHumans(['syntax' => CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2, 'short' => true]);
    }
}
