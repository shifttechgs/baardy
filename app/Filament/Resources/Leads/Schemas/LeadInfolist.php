<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

/**
 * One lead, laid out for the person about to call them: where it is in the
 * funnel across the top (stage, timing and any lost reason live there; the
 * reference, interest and branch are in the page heading), then the timeline of
 * everything said and done on the left, and on the right who they are, who is
 * handling them, and, folded away, what brought them in.
 */
class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        View::make('filament.leads.stage-stepper'),
                    ]),

                Section::make('Timeline')
                    ->description('Everything said and done on this lead, newest first.')
                    ->icon(Heroicon::OutlinedQueueList)
                    ->columnSpan(['default' => 'full', 'lg' => 2])
                    ->schema([
                        View::make('filament.leads.timeline'),
                    ]),

                Group::make()
                    ->columnSpan(['default' => 'full', 'lg' => 1])
                    ->schema([
                        Section::make('Contact')
                            ->icon(Heroicon::OutlinedUser)
                            ->schema([
                                TextEntry::make('phone')
                                    ->icon(Heroicon::OutlinedPhone)
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Medium)
                                    ->copyable()
                                    ->copyMessage('Number copied')
                                    ->url(fn (Lead $record): string => $record->telUrl()),
                                TextEntry::make('email')
                                    ->icon(Heroicon::OutlinedEnvelope)
                                    ->placeholder('Not given')
                                    ->copyable(),
                            ]),

                        Section::make('Lead')
                            ->icon(Heroicon::OutlinedClipboardDocumentList)
                            ->schema([
                                TextEntry::make('assignee.name')
                                    ->label('Handled by')
                                    ->icon(Heroicon::OutlinedUserCircle)
                                    ->placeholder('Nobody yet'),
                                TextEntry::make('created_at')
                                    ->label('Came in')
                                    ->icon(Heroicon::OutlinedClock)
                                    ->dateTime('j M Y, H:i'),
                            ]),

                        Section::make('Source')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->collapsed()
                            ->schema([
                                TextEntry::make('source')
                                    ->label('Came from')
                                    ->state(fn (Lead $record): string => $record->sourceLabel()),
                                TextEntry::make('landing_page')
                                    ->label('First page seen')
                                    ->placeholder('Not recorded'),
                                TextEntry::make('referrer')
                                    ->label('Referred by')
                                    ->placeholder('No referring site'),
                            ]),
                    ]),
            ]);
    }
}
