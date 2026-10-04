<?php

namespace App\Filament\Resources\JobApplications;

use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Filament\Resources\JobApplications\Pages\ViewJobApplication;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;

/**
 * Job applications from /careers. Not edited: the CV is kept in the database
 * and downloaded from here, and neither the list nor the detail page loads the
 * document until it is asked for. A row opens the applicant's own page.
 */
class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Applications';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * CVs nobody has looked at yet. Pulses amber while there are any.
     */
    public static function getNavigationBadge(): ?string
    {
        $unreviewed = JobApplication::query()->whereNull('reviewed_at')->count();

        return $unreviewed > 0 ? (string) $unreviewed : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $unreviewed = (int) static::getNavigationBadge();

        return $unreviewed.' CV'.($unreviewed === 1 ? '' : 's').' not yet reviewed';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutCv();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // The CV is the point of the page: a large reading surface on the
                // left, who they are and what they said beside it.
                Section::make('CV')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->description(fn (JobApplication $record): string => $record->cv_original_name.' · '.Number::fileSize($record->cv_size))
                    ->headerActions([static::viewCvAction(), static::downloadCvAction()])
                    ->columnSpan(['default' => 'full', 'lg' => 2])
                    ->schema([
                        View::make('filament.job-applications.cv-preview'),
                    ]),

                Group::make()
                    ->columnSpan(['default' => 'full', 'lg' => 1])
                    ->schema([
                        Section::make('Applicant')
                            ->icon(Heroicon::OutlinedUser)
                            ->schema([
                                TextEntry::make('phone')
                                    ->icon(Heroicon::OutlinedPhone)
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Medium)
                                    ->copyable()
                                    ->copyMessage('Number copied')
                                    ->url(fn (JobApplication $record): string => $record->telUrl()),
                                TextEntry::make('email')
                                    ->icon(Heroicon::OutlinedEnvelope)
                                    ->placeholder('Not given')
                                    ->copyable()
                                    ->url(fn (JobApplication $record): ?string => filled($record->email) ? 'mailto:'.$record->email : null),
                            ]),

                        Section::make('Application')
                            ->icon(Heroicon::OutlinedBriefcase)
                            ->schema([
                                TextEntry::make('vacancy.title')
                                    ->label('Applied for')
                                    ->placeholder('General application')
                                    ->weight(FontWeight::Medium),
                                TextEntry::make('created_at')
                                    ->label('Received')
                                    ->icon(Heroicon::OutlinedClock)
                                    ->dateTime('j M Y, H:i'),
                                TextEntry::make('status')
                                    ->state(fn (JobApplication $record): string => $record->isReviewed() ? 'Reviewed' : 'To review')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === 'Reviewed' ? 'gray' : 'warning'),
                            ]),

                        Section::make('Their message')
                            ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                            ->schema([
                                TextEntry::make('message')
                                    ->hiddenLabel()
                                    ->placeholder('They did not add a message.')
                                    ->prose(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->searchPlaceholder('Search applicants')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->deferFilters(false)
            ->recordUrl(fn (JobApplication $record): string => static::getUrl('view', ['record' => $record]))
            ->filters([
                SelectFilter::make('vacancy_id')
                    ->label('Applied for')
                    ->relationship('vacancy', 'title'),
            ])
            ->emptyStateHeading('No applications here')
            ->emptyStateDescription('CVs sent from the careers page land here.')
            ->emptyStateIcon(Heroicon::OutlinedBriefcase)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(fn (JobApplication $record): FontWeight => $record->isReviewed() ? FontWeight::Medium : FontWeight::Bold)
                    ->description(fn (JobApplication $record): string => $record->phone),
                TextColumn::make('vacancy.title')
                    ->label('Applied for')
                    ->placeholder('General application')
                    ->wrap(),
                TextColumn::make('cv_original_name')
                    ->label('CV')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->color('gray')
                    ->limit(22)
                    ->description(fn (JobApplication $record): string => Number::fileSize($record->cv_size))
                    ->visibleFrom('lg'),
                TextColumn::make('message')
                    ->limit(40)
                    ->tooltip(fn (JobApplication $record): ?string => $record->message)
                    ->placeholder('No message')
                    ->color('gray')
                    ->visibleFrom('2xl'),
                TextColumn::make('status')
                    ->state(fn (JobApplication $record): string => $record->isReviewed() ? 'Reviewed' : 'To review')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Reviewed' ? 'gray' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->dateTimeTooltip('j M Y, H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    static::viewCvAction(),
                    static::downloadCvAction(),
                    static::markReviewedAction(),
                    DeleteAction::make(),
                ])->tooltip('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markReviewed')
                        ->label('Mark reviewed')
                        ->icon(Heroicon::OutlinedCheck)
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            JobApplication::query()->whereKey($records->modelKeys())->whereNull('reviewed_at')->update(['reviewed_at' => now()]);

                            Notification::make()->title('Marked as reviewed')->success()->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Opens the CV in a new tab: a PDF is shown in the browser, anything else
     * downloads. Served by JobApplicationCvController, for admin users only.
     */
    public static function viewCvAction(): Action
    {
        return Action::make('viewCv')
            ->label('View CV')
            ->icon(Heroicon::OutlinedEye)
            ->color('gray')
            ->url(fn (JobApplication $record): string => route('applications.cv', $record))
            ->openUrlInNewTab();
    }

    /**
     * Streams the CV; the document is fetched here, not for the list.
     */
    public static function downloadCvAction(): Action
    {
        return Action::make('downloadCv')
            ->label('Download CV')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->action(function (JobApplication $record) {
                $cv = JobApplication::query()->findOrFail($record->getKey());

                return response()->streamDownload(
                    fn () => print ($cv->cvContents()),
                    $cv->cv_original_name,
                    ['Content-Type' => $cv->cv_mime],
                );
            });
    }

    public static function markReviewedAction(): Action
    {
        return Action::make('markReviewed')
            ->label('Mark reviewed')
            ->icon(Heroicon::OutlinedCheck)
            ->visible(fn (JobApplication $record): bool => ! $record->isReviewed())
            ->action(function (JobApplication $record): void {
                $record->forceFill(['reviewed_at' => now()])->save();

                Notification::make()->title('Marked as reviewed')->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplications::route('/'),
            'view' => ViewJobApplication::route('/{record}'),
        ];
    }
}
