<?php

namespace App\Filament\Widgets\Concerns;

use App\Filament\Widgets\LeadStats;
use App\LeadStage;
use App\Models\Lead;
use Illuminate\Support\Collection;

/**
 * What the "what is working" charts share: the last 30 days of leads grouped
 * by branch, loan product or source, each group counted (enquiries, approved,
 * still open), plus one plain sentence naming the best and weakest group once
 * each has enough enquiries for the gap to mean something.
 */
trait BreaksDownLeads
{
    /** Fewest enquiries a group needs before it is compared with another. */
    private const MIN_LEADS = 5;

    /** The most groups a chart shows; the rest would only be noise. */
    private const MAX_GROUPS = 6;

    /**
     * @return Collection<int, Lead>
     */
    protected function recentLeads(): Collection
    {
        return Lead::query()
            ->with('promotion:id,title')
            ->where('created_at', '>=', now()->subDays(LeadStats::DAYS)->startOfDay())
            ->get();
    }

    public static function canView(): bool
    {
        return Lead::query()->where('created_at', '>=', now()->subDays(LeadStats::DAYS)->startOfDay())->exists();
    }

    /**
     * @param  Collection<int, Lead>  $leads
     * @param  callable(Lead): string  $key
     * @return list<array{label: string, total: int, open: int, approved: int, rate: int}>
     */
    protected function rows(Collection $leads, callable $key): array
    {
        return $leads
            ->groupBy($key)
            ->map(function (Collection $group, string $label): array {
                $approved = $group->where('stage', LeadStage::Approved)->count();

                return [
                    'label' => $label,
                    'total' => $group->count(),
                    'open' => $group->filter(fn (Lead $lead): bool => $lead->stage->isOpen())->count(),
                    'approved' => $approved,
                    'rate' => (int) round($approved / $group->count() * 100),
                ];
            })
            ->sortByDesc('total')
            ->take(self::MAX_GROUPS)
            ->values()
            ->all();
    }

    /**
     * @param  list<array{label: string, total: int, open: int, approved: int, rate: int}>  $rows
     */
    protected function insight(string $noun, array $rows): ?string
    {
        $enough = collect($rows)->filter(fn (array $row): bool => $row['total'] >= self::MIN_LEADS)->sortByDesc('rate')->values();

        if ($enough->count() < 2) {
            return $rows === [] ? null : "Too few enquiries yet to compare {$noun} fairly.";
        }

        $best = $enough->first();
        $worst = $enough->last();

        if ($best['rate'] - $worst['rate'] < 10) {
            return "All {$noun} are converting at a similar rate.";
        }

        return "{$best['label']} turns {$best['rate']}% of enquiries into loans; {$worst['label']} only {$worst['rate']}%.";
    }
}
