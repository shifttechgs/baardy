<?php

namespace App\Filament\Resources\Promotions\Tables;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Promotion;
use App\PromotionStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('ends_at', 'desc')
            ->searchPlaceholder('Search promotions')
            ->recordUrl(fn (Promotion $record): string => PromotionResource::getUrl('edit', ['record' => $record]))
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->deferFilters(false)
            ->emptyStateHeading('No promotions here')
            ->emptyStateDescription('Create a promotion to put an offer on the website.')
            ->emptyStateIcon(Heroicon::OutlinedMegaphone)
            ->columns([
                ImageColumn::make('image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->imageSize(44)
                    ->visibleFrom('sm'),
                TextColumn::make('title')
                    ->searchable()
                    ->grow()
                    ->description(fn (Promotion $record): string => $record->summary)
                    ->wrap(),
                TextColumn::make('product')
                    ->label('Loan')
                    ->placeholder('Any')
                    ->visibleFrom('xl'),
                // Where the promotion is in its life, not just its approval: an
                // approved promotion past its end date reads "Ended", not "Approved".
                TextColumn::make('phase')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Promotion $record): string => match (true) {
                        $record->status === PromotionStatus::Draft => 'Draft',
                        $record->status === PromotionStatus::AwaitingApproval => 'Awaiting approval',
                        $record->isLive() => 'Live',
                        $record->hasEnded() => 'Ended',
                        default => 'Scheduled',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Live' => 'success',
                        'Awaiting approval' => 'warning',
                        'Scheduled' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('placements')
                    ->label('Appears in')
                    ->state(fn (Promotion $record): array => array_values(array_filter([
                        $record->show_in_banner ? 'Header' : null,
                        $record->show_in_hero ? 'Homepage' : null,
                        $record->show_in_menu ? 'Menu' : null,
                        $record->show_on_product ? 'Loan tag' : null,
                    ])))
                    ->badge()
                    ->color('gray')
                    ->placeholder('Promotions page')
                    ->visibleFrom('xl'),
                TextColumn::make('leads_count')
                    ->label('Leads')
                    ->counts('leads')
                    ->numeric()
                    ->alignEnd()
                    ->sortable()
                    ->visibleFrom('lg'),
                TextColumn::make('ends_at')
                    ->label('Runs')
                    ->state(fn (Promotion $record): string => $record->starts_at->format('j M').' – '.$record->ends_at->format('j M Y'))
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PromotionStatus::class),
                TernaryFilter::make('live')
                    ->label('Live now')
                    ->queries(
                        true: fn (Builder $query) => $query->live(),
                        false: fn (Builder $query) => $query->whereNotIn('id', Promotion::query()->live()->select('id')),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('view')
                        ->label('View on site')
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->visible(fn (Promotion $record): bool => $record->isLive())
                        ->url(fn (Promotion $record): string => route('promotions.show', $record))
                        ->openUrlInNewTab(),
                    // Start a new promotion from an old one: same loan, terms,
                    // photo and placements. Never copies the approval -- the copy
                    // is a draft with a fresh link, tracking code and dates.
                    ReplicateAction::make()
                        ->label('Duplicate')
                        ->requiresConfirmation(false)
                        ->excludeAttributes(['slug', 'tracking_code', 'status', 'approved_by', 'approved_at', 'starts_at', 'ends_at', 'leads_count'])
                        ->beforeReplicaSaved(function (Promotion $replica): void {
                            $replica->title = 'Copy of '.$replica->title;
                            $replica->slug = Str::slug($replica->title).'-'.Str::lower(Str::random(4));
                            $replica->tracking_code = Str::upper(Str::random(8));
                            $replica->status = PromotionStatus::Draft;
                            $replica->starts_at = now()->startOfHour();
                            $replica->ends_at = now()->startOfHour()->addDays(30);
                        })
                        ->successNotificationTitle('Duplicated as a draft')
                        ->successRedirectUrl(fn (Promotion $replica): string => PromotionResource::getUrl('edit', ['record' => $replica])),
                    DeleteAction::make()
                        ->modalDescription('The promotion comes off the site for good. The leads it brought in are kept. This cannot be undone.'),
                ])->tooltip('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading('Delete the selected promotions?')
                        ->modalDescription('They come off the site for good. The leads they brought in are kept. This cannot be undone.'),
                ]),
            ]);
    }
}
