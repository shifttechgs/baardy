<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Promotion;
use App\PromotionStatus;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Promotions waiting for sign-off, oldest first, each one click from its
 * review screen. Empty most of the time -- and says so plainly.
 */
class AwaitingApproval extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Needs your approval';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Promotion::query()->where('status', PromotionStatus::AwaitingApproval)->oldest('updated_at'))
            ->paginated(false)
            ->emptyStateHeading('Nothing waiting for approval')
            ->emptyStateDescription('Promotions submitted for sign-off will appear here.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Promotion $record): string => $record->summary)
                    ->wrap(),
                TextColumn::make('product')->label('Loan')->placeholder('Any'),
                TextColumn::make('starts_at')->label('Starts')->dateTime('j M Y'),
                TextColumn::make('ends_at')->label('Ends')->dateTime('j M Y'),
                TextColumn::make('updated_at')->label('Submitted')->since(),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->button()
                    ->url(fn (Promotion $record): string => PromotionResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
