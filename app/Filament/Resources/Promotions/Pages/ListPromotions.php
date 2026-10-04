<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Promotion;
use App\PromotionStatus;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tabs follow a promotion's life: what is live, what waits for sign-off, what
 * is booked for later, drafts, and what has finished. Each carries its count;
 * "Awaiting approval" is highlighted because it is waiting on the owner.
 */
class ListPromotions extends ListRecords
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Offers on the website: what is live, what needs sign-off and what is coming up.';
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $live = Promotion::query()->live()->count();
        $awaiting = Promotion::query()->where('status', PromotionStatus::AwaitingApproval)->count();
        $scheduled = Promotion::query()->where('status', PromotionStatus::Approved)->where('starts_at', '>', now())->count();
        $drafts = Promotion::query()->where('status', PromotionStatus::Draft)->count();
        $ended = Promotion::query()->where('status', PromotionStatus::Approved)->where('ends_at', '<=', now())->count();

        return [
            'all' => Tab::make('All')
                ->badge(Promotion::query()->count() ?: null)
                ->badgeColor('gray'),
            'live' => Tab::make('Live')
                ->badge($live ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->live()),
            'awaiting' => Tab::make('Awaiting approval')
                ->badge($awaiting ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PromotionStatus::AwaitingApproval)),
            'scheduled' => Tab::make('Scheduled')
                ->badge($scheduled ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PromotionStatus::Approved)->where('starts_at', '>', now())),
            'drafts' => Tab::make('Drafts')
                ->badge($drafts ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PromotionStatus::Draft)),
            'ended' => Tab::make('Ended')
                ->badge($ended ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PromotionStatus::Approved)->where('ends_at', '<=', now())),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
