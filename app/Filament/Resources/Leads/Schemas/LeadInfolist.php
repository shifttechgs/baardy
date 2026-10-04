<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\LeadStage;
use App\Models\Lead;
use Carbon\CarbonInterface;
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
 * funnel across the top, then the timeline of everything said and done on the
 * left, and on the right who they are, what they want and what brought them in.
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
                                TextEntry::make('branch')
                                    ->label('Nearest branch')
                                    ->icon(Heroicon::OutlinedMapPin),
                            ]),

                        Section::make('What they want')
                            ->icon(Heroicon::OutlinedBanknotes)
                            ->schema([
                                TextEntry::make('interest')
                                    ->label('Interested in')
                                    ->weight(FontWeight::Medium),
                                TextEntry::make('reference')
                                    ->icon(Heroicon::OutlinedHashtag)
                                    ->copyable()
                                    ->fontFamily('mono'),
                            ]),

                        Section::make('Funnel')
                            ->icon(Heroicon::OutlinedFunnel)
                            ->schema([
                                TextEntry::make('stage')->badge(),
                                TextEntry::make('lost_reason')
                                    ->label('Why it was lost')
                                    ->visible(fn (Lead $record): bool => $record->stage === LeadStage::Lost),
                                TextEntry::make('assignee.name')
                                    ->label('Handled by')
                                    ->icon(Heroicon::OutlinedUserCircle)
                                    ->placeholder('Nobody yet'),
                                TextEntry::make('created_at')
                                    ->label('Came in')
                                    ->icon(Heroicon::OutlinedClock)
                                    ->dateTime('j M Y, H:i'),
                                TextEntry::make('first_response')
                                    ->label('First contact')
                                    ->state(fn (Lead $record): string => $record->first_contacted_at
                                        ? $record->created_at->diffForHumans($record->first_contacted_at, ['syntax' => CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]).' after it came in'
                                        : 'Not yet, waiting '.$record->created_at->diffForHumans(['syntax' => CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2])),
                            ]),

                        Section::make('Source')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->collapsible()
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
