<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Promotion;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * What visitors can see right now, soonest-ending first, with where each
 * promotion appears and a link to it on the site.
 */
class LivePromotions extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Live on the site';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Promotion::query()->live()->orderBy('ends_at'))
            ->paginated(false)
            ->emptyStateHeading('No promotions are live')
            ->emptyStateDescription('Approved promotions appear here while they run.')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->columns([
                ImageColumn::make('image_path')->label('')->disk('public')->square()->imageSize(40),
                TextColumn::make('title')->wrap(),
                TextColumn::make('placements')
                    ->label('Appears in')
                    ->state(fn (Promotion $record): array => array_values(array_filter([
                        $record->show_in_banner ? 'Header bar' : null,
                        $record->show_in_hero ? 'Homepage flyer' : null,
                        $record->show_in_menu ? 'Loans menu' : null,
                        $record->show_on_product ? 'Loan tag' : null,
                    ])))
                    ->badge()
                    ->color('gray')
                    ->placeholder('Promotions page only'),
                TextColumn::make('ends_at')->label('Ends')->dateTime('j M Y')->description(fn (Promotion $record): string => $record->ends_at->diffForHumans()),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View on site')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Promotion $record): string => route('promotions.show', $record))
                    ->openUrlInNewTab(),
                Action::make('edit')
                    ->url(fn (Promotion $record): string => PromotionResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
