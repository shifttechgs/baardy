<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Lead;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * The lead's contact details: for logging a phone call or walk-in by hand,
 * and for correcting a website enquiry. The same choices and rules as the
 * website form, so both kinds of lead read the same.
 *
 * Two groups: who is asking, then what they need. The dropdowns are
 * Filament's own (not the browser's), so they take the panel's styling.
 */
class LeadForm
{
    /**
     * How an enquiry taken by hand reached the team, stored as its source.
     *
     * @var array<string, string>
     */
    public const OFFLINE_SOURCES = [
        'phone' => 'Phone call',
        'whatsapp' => 'WhatsApp',
        'walk-in' => 'Walk-in at a branch',
        'referral' => 'Referral',
    ];

    public static function configure(Schema $schema): Schema
    {
        $interests = StoreEnquiryRequest::interests();
        $branches = StoreEnquiryRequest::branches();

        return $schema
            ->columns(1)
            ->components([
                // Read-only context on the edit page: which lead this is.
                Section::make('This lead')
                    ->icon(Heroicon::OutlinedInboxArrowDown)
                    ->columns(['default' => 2, 'md' => 4])
                    ->visibleOn('edit')
                    ->schema([
                        TextEntry::make('reference')->label('Reference')->fontFamily('mono')->copyable(),
                        TextEntry::make('stage')->label('Stage')->badge(),
                        TextEntry::make('created_at')->label('Came in')->dateTime('j M Y, H:i'),
                        TextEntry::make('source')
                            ->label('Came from')
                            ->state(fn (Lead $record): string => $record->sourceLabel()),
                    ]),

                Section::make('Who is asking')
                    ->description('How we reach them.')
                    ->icon(Heroicon::OutlinedUser)
                    ->columns(['default' => 1, 'md' => 3])
                    ->schema([
                        TextInput::make('name')
                            ->label('Full name')
                            ->placeholder('e.g. Tendai Moyo')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('phone')
                            ->label('Phone or WhatsApp')
                            ->placeholder('+263 77 123 4567')
                            ->helperText('Include the country code.')
                            ->tel()
                            ->required()
                            ->maxLength(40)
                            ->regex('/^[0-9+()\-\s]{7,}$/'),
                        TextInput::make('email')
                            ->label('Email (optional)')
                            ->placeholder('name@example.com')
                            ->email()
                            ->maxLength(190),
                    ]),

                Section::make('What they need')
                    ->description('Which loan, where, and how the enquiry reached us.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columns(['default' => 1, 'md' => 3])
                    ->schema([
                        Select::make('interest')
                            ->label('Interested in')
                            ->options(array_combine($interests, $interests))
                            ->native(false)
                            ->placeholder('Choose a loan')
                            ->required(),
                        Select::make('branch')
                            ->label('Nearest branch')
                            ->options(array_combine($branches, $branches))
                            ->native(false)
                            ->default($branches[0] ?? null)
                            ->selectablePlaceholder(false)
                            ->required(),
                        Select::make('source')
                            ->label('How did they reach us?')
                            ->options(self::OFFLINE_SOURCES)
                            ->native(false)
                            ->default('phone')
                            ->selectablePlaceholder(false)
                            ->required()
                            ->visibleOn('create'),
                        Textarea::make('message')
                            ->label('Notes')
                            ->placeholder('What they asked for, amounts, anything to remember.')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
