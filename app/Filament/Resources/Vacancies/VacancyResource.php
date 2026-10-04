<?php

namespace App\Filament\Resources\Vacancies;

use App\Filament\Resources\Vacancies\Pages\CreateVacancy;
use App\Filament\Resources\Vacancies\Pages\EditVacancy;
use App\Filament\Resources\Vacancies\Pages\ListVacancies;
use App\Filament\Resources\Vacancies\Schemas\VacancyForm;
use App\Models\Vacancy;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

/**
 * Job openings shown on /careers. Published and not past the closing date =
 * visible, with an apply form that takes the applicant's CV. Created and
 * edited on their own pages, not in a modal.
 */
class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    /** A role closing within a week is flagged while it has fewer applications than this. */
    public const ENOUGH_APPLICATIONS = 3;

    /**
     * Open roles closing within a week that few people have applied for: the
     * one time a closing date is a decision (extend it, or promote the role).
     * Pulses amber while a role has no applications at all.
     *
     * @return Collection<int, Vacancy>
     */
    public static function thinlyAppliedRoles(): Collection
    {
        return Vacancy::query()
            ->open()
            ->whereBetween('closes_on', [today(), today()->addWeek()])
            ->withCount('applications')
            ->get()
            ->filter(fn (Vacancy $vacancy): bool => $vacancy->applications_count < static::ENOUGH_APPLICATIONS)
            ->values();
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::thinlyAppliedRoles()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::thinlyAppliedRoles()->contains(fn (Vacancy $vacancy): bool => $vacancy->applications_count === 0) ? 'warning' : 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return static::thinlyAppliedRoles()
            ->map(fn (Vacancy $vacancy): string => "{$vacancy->title}: {$vacancy->applications_count} applied, closes ".$vacancy->closes_on->format('j M'))
            ->implode('; ') ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return VacancyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Search roles')
            ->recordUrl(fn (Vacancy $record): string => static::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('No roles here')
            ->emptyStateDescription('Add a role to show it on the careers page.')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->description(fn (Vacancy $record): string => $record->location.' · '.$record->employment_type),
                TextColumn::make('closes_on')
                    ->label('Closes')
                    ->date('j M Y')
                    ->placeholder('Open until unpublished')
                    ->sortable(),
                TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications')
                    ->alignEnd(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])->tooltip('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVacancies::route('/'),
            'create' => CreateVacancy::route('/create'),
            'edit' => EditVacancy::route('/{record}/edit'),
        ];
    }
}
