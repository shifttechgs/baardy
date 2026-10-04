<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leads\LeadResource;
use App\LeadStage;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * New leads nobody has spoken to yet, longest-waiting first, with Call and
 * WhatsApp on each row. The first thing to clear every morning.
 */
class LeadsToCall extends TableWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected static ?string $heading = 'Call these next';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Lead::query()->where('stage', LeadStage::New)->oldest())
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('Nobody waiting')
            ->emptyStateDescription('Every lead has had a first call.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->columns([
                TextColumn::make('name')
                    ->weight('medium')
                    ->description(fn (Lead $record): string => $record->interest),
                TextColumn::make('created_at')
                    ->label('Waiting')
                    ->since()
                    ->color(fn (Lead $record): ?string => $record->created_at->lt(now()->subHours(LeadResource::RESPONSE_TARGET_HOURS)) ? 'danger' : null),
            ])
            ->recordActions([
                Action::make('call')
                    ->icon(Heroicon::OutlinedPhone)
                    ->iconButton()
                    ->tooltip('Call')
                    ->url(fn (Lead $record): string => $record->telUrl()),
                Action::make('whatsapp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->iconButton()
                    ->tooltip('WhatsApp')
                    ->color('success')
                    ->url(fn (Lead $record): string => $record->whatsappUrl(auth()->user()))
                    ->openUrlInNewTab(),
            ]);
    }
}
