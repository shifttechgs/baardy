<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Filament\Resources\Leads\LeadResource;
use App\Http\Requests\StoreEnquiryRequest;
use App\LeadStage;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * The leads list: who, what for, where from, where in the funnel, and how
 * long they have waited -- red once a new lead is past the response target.
 * Call and WhatsApp are one click from every row; the row opens the lead.
 */
class LeadsTable
{
    public static function configure(Table $table): Table
    {
        $interests = StoreEnquiryRequest::interests();
        $branches = StoreEnquiryRequest::branches();

        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->searchPlaceholder('Search leads')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(25)
            ->deferFilters(false)
            ->filtersFormColumns(2)
            ->filtersFormWidth(Width::Large)
            ->modifyQueryUsing(fn ($query) => $query->with(['assignee', 'promotion']))
            ->recordUrl(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('No leads here')
            ->emptyStateDescription('Enquiries from the website\'s "Get in touch" form land here.')
            ->emptyStateIcon(Heroicon::OutlinedInboxArrowDown)
            ->columns([
                TextColumn::make('reference')
                    ->fontFamily('mono')
                    ->color('gray')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(['name', 'reference', 'phone', 'email'])
                    ->weight('medium')
                    ->description(fn (Lead $record): string => $record->phone),
                TextColumn::make('interest')
                    ->label('Interested in')
                    ->description(fn (Lead $record): string => $record->branch)
                    ->visibleFrom('md'),
                TextColumn::make('source')
                    ->label('Came from')
                    ->state(fn (Lead $record): string => $record->sourceLabel())
                    ->color('gray')
                    ->limit(28)
                    ->visibleFrom('xl'),
                TextColumn::make('stage')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Handled by')
                    ->placeholder('Nobody yet')
                    ->visibleFrom('lg'),
                TextColumn::make('created_at')
                    ->label('Came in')
                    ->since()
                    ->dateTimeTooltip('j M Y, H:i')
                    ->sortable()
                    ->color(fn (Lead $record): ?string => static::isOverdue($record) ? 'danger' : null)
                    ->icon(fn (Lead $record): ?Heroicon => static::isOverdue($record) ? Heroicon::OutlinedExclamationCircle : null)
                    ->tooltip(fn (Lead $record): ?string => static::isOverdue($record) ? 'Past the '.LeadResource::RESPONSE_TARGET_HOURS.'-hour response target' : null)
                    ->visibleFrom('sm'),
            ])
            ->filters([
                SelectFilter::make('interest')->options(array_combine($interests, $interests)),
                SelectFilter::make('branch')->options(array_combine($branches, $branches)),
                SelectFilter::make('assigned_to')
                    ->label('Handled by')
                    ->relationship('assignee', 'name'),
                SelectFilter::make('promotion_id')
                    ->label('Promotion')
                    ->relationship('promotion', 'title'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('call')
                        ->label('Call')
                        ->icon(Heroicon::OutlinedPhone)
                        ->url(fn (Lead $record): string => $record->telUrl()),
                    Action::make('whatsapp')
                        ->label('WhatsApp')
                        ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                        ->color('success')
                        ->url(fn (Lead $record): string => $record->whatsappUrl(auth()->user()))
                        ->openUrlInNewTab(),
                    Action::make('contacted')
                        ->label('Mark contacted')
                        ->icon(Heroicon::OutlinedCheck)
                        ->visible(fn (Lead $record): bool => $record->stage === LeadStage::New)
                        ->action(function (Lead $record): void {
                            $record->moveTo(LeadStage::Contacted, auth()->user());

                            Notification::make()->title($record->name.' marked as contacted')->success()->send();
                        }),
                ])->tooltip('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading('Delete the selected leads?')
                        ->modalDescription('The leads and their timelines are removed for good. This cannot be undone.'),
                ]),
            ]);
    }

    private static function isOverdue(Lead $lead): bool
    {
        return $lead->stage === LeadStage::New
            && $lead->created_at->lt(now()->subHours(LeadResource::RESPONSE_TARGET_HOURS));
    }
}
