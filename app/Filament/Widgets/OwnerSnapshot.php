<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leads\LeadResource;
use App\LeadStage;
use App\Models\Lead;
use Carbon\CarbonInterface;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;

/**
 * Three numbers that answer "are we doing better or worse?": demand, how much
 * of it becomes loans, and how fast the team responds. Each is set against the
 * 30 days before, so the owner reads a direction, not a bare figure.
 */
class OwnerSnapshot extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $days = LeadStats::DAYS;
        $columns = ['id', 'stage', 'created_at', 'first_contacted_at'];

        $now = Lead::query()->where('created_at', '>=', now()->subDays($days)->startOfDay())->get($columns);
        $before = Lead::query()
            ->where('created_at', '>=', now()->subDays($days * 2)->startOfDay())
            ->where('created_at', '<', now()->subDays($days)->startOfDay())
            ->get($columns);

        $perDay = collect(range($days - 1, 0))
            ->map(fn (int $daysAgo): int => $now->filter(fn (Lead $lead): bool => $lead->created_at->isSameDay(now()->subDays($daysAgo)))->count())
            ->all();

        $approvedNow = $now->where('stage', LeadStage::Approved)->count();
        $approvedBefore = $before->where('stage', LeadStage::Approved)->count();
        $rateNow = $this->rate($approvedNow, $now->count());
        $rateBefore = $this->rate($approvedBefore, $before->count());

        $minutesNow = $this->medianMinutes($now);
        $minutesBefore = $this->medianMinutes($before);

        return [
            Stat::make('Enquiries, last '.$days.' days', $now->count())
                ->description($this->change($now->count(), $before->count(), 'previous '.$days.' days'))
                ->descriptionIcon($now->count() >= $before->count() ? Heroicon::OutlinedArrowTrendingUp : Heroicon::OutlinedArrowTrendingDown)
                ->descriptionColor($now->count() >= $before->count() ? 'success' : 'warning')
                ->chart($perDay)
                ->color('primary')
                ->url(LeadResource::getUrl('index')),
            Stat::make('Enquiries that became loans', $rateNow.'%')
                ->description($approvedNow.' approved of '.$now->count().' · was '.$rateBefore.'% before')
                ->descriptionIcon($rateNow >= $rateBefore ? Heroicon::OutlinedArrowTrendingUp : Heroicon::OutlinedArrowTrendingDown)
                ->descriptionColor($rateNow >= $rateBefore ? 'success' : 'warning')
                ->color('success')
                ->url(LeadResource::getUrl('index', ['activeTab' => 'approved'])),
            Stat::make('Time to first call', $minutesNow === null ? '—' : $this->duration($minutesNow))
                ->description($minutesNow === null
                    ? 'no calls logged yet'
                    : 'median · target '.LeadResource::RESPONSE_TARGET_HOURS.'h'.($minutesBefore === null ? '' : ' · was '.$this->duration($minutesBefore).' before'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->descriptionColor($minutesNow !== null && $minutesNow > LeadResource::RESPONSE_TARGET_HOURS * 60 ? 'warning' : 'success')
                ->color('gray'),
        ];
    }

    private function rate(int $approved, int $total): int
    {
        return $total === 0 ? 0 : (int) round($approved / $total * 100);
    }

    private function change(int $now, int $before, string $period): string
    {
        if ($before === 0) {
            return $now === 0 ? 'no enquiries in either period' : 'none in the '.$period;
        }

        $percent = (int) round(($now - $before) / $before * 100);

        return ($percent >= 0 ? '+' : '').$percent.'% vs the '.$period;
    }

    /**
     * @param  Collection<int, Lead>  $leads
     */
    private function medianMinutes(Collection $leads): ?float
    {
        $minutes = $leads
            ->filter(fn (Lead $lead): bool => $lead->first_contacted_at !== null)
            ->map(fn (Lead $lead): float => $lead->created_at->diffInMinutes($lead->first_contacted_at))
            ->values();

        return $minutes->isEmpty() ? null : (float) $minutes->median();
    }

    private function duration(float $minutes): string
    {
        if ($minutes < 1) {
            return 'under 1 min';
        }

        return now()->subMinutes((int) round($minutes))->diffForHumans(['syntax' => CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2, 'short' => true]);
    }
}
