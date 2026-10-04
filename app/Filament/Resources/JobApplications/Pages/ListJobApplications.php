<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Models\JobApplication;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * "To review" is the working list and the default; the badge counts CVs nobody
 * has looked at yet.
 */
class ListJobApplications extends ListRecords
{
    protected static string $resource = JobApplicationResource::class;

    public function getSubheading(): ?string
    {
        return 'CVs sent from the careers page.';
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $total = JobApplication::query()->count();
        $toReview = JobApplication::query()->whereNull('reviewed_at')->count();

        return [
            'review' => Tab::make('To review')
                ->badge($toReview ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('reviewed_at')),
            'reviewed' => Tab::make('Reviewed')
                ->badge(($total - $toReview) ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('reviewed_at')),
            'all' => Tab::make('All')
                ->badge($total ?: null)
                ->badgeColor('gray'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'review';
    }
}
