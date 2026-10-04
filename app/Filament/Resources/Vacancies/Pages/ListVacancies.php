<?php

namespace App\Filament\Resources\Vacancies\Pages;

use App\Filament\Resources\Vacancies\VacancyResource;
use App\Models\Vacancy;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tabs follow a role's life: open on the careers page, unpublished drafts, and
 * closed (published but past its closing date). Each carries its count.
 */
class ListVacancies extends ListRecords
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Roles shown on the careers page.';
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $open = Vacancy::query()->open()->count();
        $drafts = Vacancy::query()->where('is_published', false)->count();
        $closed = Vacancy::query()->where('is_published', true)->whereNotNull('closes_on')->where('closes_on', '<', today())->count();

        return [
            'open' => Tab::make('Open')
                ->badge($open ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->open()),
            'drafts' => Tab::make('Drafts')
                ->badge($drafts ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false)),
            'closed' => Tab::make('Closed')
                ->badge($closed ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', true)->whereNotNull('closes_on')->where('closes_on', '<', today())),
            'all' => Tab::make('All')
                ->badge(Vacancy::query()->count() ?: null)
                ->badgeColor('gray'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'open';
    }
}
