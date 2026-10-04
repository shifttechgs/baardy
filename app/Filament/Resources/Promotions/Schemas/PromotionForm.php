<?php

namespace App\Filament\Resources\Promotions\Schemas;

use App\Models\Promotion;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * The promotion form, ordered the way an owner thinks about an offer:
 * which loan, what it is, the picture, the terms -- with when and where in
 * the sidebar, and the technical fields folded away and filled in for them.
 *
 * Friction removed:
 *   - duration buttons (2 weeks / 1 month / 3 months) instead of picking an
 *     end date; custom dates remain one click away
 *   - header bar, homepage flyer and Loans menu on by default; the loan tag
 *     switches on by itself when a loan is chosen
 *   - the link, tracking code and button label default themselves
 *     (Advanced, collapsed); the photograph is described by the title
 *   - a live preview of the header bar and a character count on the
 *     one-liner, so nobody has to publish to see how it reads
 *
 * Status is not edited here: it moves only through Publish / Submit for
 * approval / Save draft and the sign-off actions on the edit page.
 */
class PromotionForm
{
    /**
     * Duration presets, in days.
     *
     * @var array<string, int>
     */
    private const DURATIONS = ['2w' => 14, '1m' => 30, '3m' => 91];

    public static function configure(Schema $schema): Schema
    {
        $products = collect(config('marketing.products'))->pluck('name', 'name')->all();

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('The offer')
                            ->schema([
                                Select::make('product')
                                    ->label('Which loan is it for?')
                                    ->options($products)
                                    ->in(array_keys($products))
                                    ->placeholder('All loans')
                                    ->live()
                                    ->afterStateUpdated(fn (?string $state, Set $set) => $set('show_on_product', filled($state))),

                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(120)
                                    ->placeholder('School fees season')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Set $set, ?Promotion $record): void {
                                        if ($record === null) {
                                            $set('slug', Str::slug((string) $state));
                                        }
                                    }),

                                TextInput::make('summary')
                                    ->label('One-line pitch')
                                    ->required()
                                    ->maxLength(80)
                                    ->placeholder('Term fees, spread across the term')
                                    ->live(debounce: 300)
                                    ->hint(fn (?string $state): string => Str::length((string) $state).' / 80'),

                                View::make('filament.promotions.chip-preview'),

                                Textarea::make('body')
                                    ->label('Details')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Who it is for, what they get, and how to take it up.')
                                    ->helperText('Blank lines start new paragraphs.'),

                                FileUpload::make('image_path')
                                    ->label('Photograph')
                                    ->helperText('Optional. A real photograph works best; landscape, at least 1200px wide.')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('promotions')
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->preventFilePathTampering(),
                            ]),

                        Section::make('Terms and conditions')
                            ->description('Shown in full on the promotion page. Add eligibility and anything a borrower must know.')
                            ->schema([
                                Textarea::make('terms')
                                    ->hiddenLabel()
                                    ->required()
                                    ->rows(4)
                                    ->default("This offer is subject to application, affordability assessment and approval.\n\nAvailable for applications made between the start and end dates shown on this page."),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('When it runs')
                            ->schema([
                                ToggleButtons::make('duration')
                                    ->hiddenLabel()
                                    ->options(['2w' => '2 weeks', '1m' => '1 month', '3m' => '3 months', 'custom' => 'Custom'])
                                    ->grouped()
                                    ->default('1m')
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateHydrated(function (ToggleButtons $component, ?Promotion $record): void {
                                        if ($record !== null) {
                                            $component->state('custom');
                                        }
                                    })
                                    ->afterStateUpdated(fn (?string $state, Get $get, Set $set) => self::applyDuration($state, $get('starts_at'), $set)),

                                DateTimePicker::make('starts_at')
                                    ->label('Starts')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->displayFormat('j M Y, H:i')
                                    ->default(fn (): string => now()->startOfHour()->toDateTimeString())
                                    ->live()
                                    ->afterStateUpdated(fn (?string $state, Get $get, Set $set) => self::applyDuration($get('duration'), $state, $set)),

                                DateTimePicker::make('ends_at')
                                    ->label('Ends')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->displayFormat('j M Y, H:i')
                                    ->after('starts_at')
                                    ->default(fn (): string => now()->startOfHour()->addDays(self::DURATIONS['1m'])->toDateTimeString())
                                    ->disabled(fn (Get $get): bool => $get('duration') !== 'custom')
                                    ->dehydrated(),
                            ]),

                        Section::make('Where it appears')
                            ->description('It is always on the Promotions page.')
                            ->schema([
                                Toggle::make('show_in_banner')->label('Every page, bar above the header')->default(true),
                                Toggle::make('show_in_hero')->label('Homepage, flyer beside the headline')->default(true),
                                Toggle::make('show_in_menu')->label('Loans menu, featured card')->default(true),
                                Toggle::make('show_on_product')
                                    ->label('"Limited offer" tag on the loan')
                                    ->disabled(fn (Get $get): bool => blank($get('product')))
                                    ->dehydrated()
                                    ->helperText(fn (Get $get): ?string => blank($get('product')) ? 'Choose a loan to use this.' : null),
                            ]),

                        Section::make('Share this promotion')
                            ->description('A tagged link for each place you post it, so the dashboard shows which one worked.')
                            ->icon(Heroicon::OutlinedLink)
                            ->visibleOn('edit')
                            ->schema([
                                View::make('filament.promotions.share-links'),
                            ]),

                        Section::make('Advanced')
                            ->description('Filled in for you.')
                            ->collapsed()
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Link')
                                    ->required()
                                    ->alphaDash()
                                    ->maxLength(120)
                                    ->unique(ignoreRecord: true)
                                    ->prefix('/promotions/'),
                                TextInput::make('tracking_code')
                                    ->required()
                                    ->alphaDash()
                                    ->maxLength(40)
                                    ->unique(ignoreRecord: true)
                                    ->default(fn (): string => Str::upper(Str::random(8)))
                                    ->helperText('Recorded on every enquiry this promotion brings in.'),
                                TextInput::make('cta_label')
                                    ->label('Button label')
                                    ->required()
                                    ->maxLength(40)
                                    ->default('Visit a branch'),
                            ]),
                    ]),
            ]);
    }

    /**
     * Set the end date from a preset duration and the start date. Custom
     * leaves the end date to the editor.
     */
    private static function applyDuration(?string $duration, ?string $startsAt, Set $set): void
    {
        $days = self::DURATIONS[$duration ?? ''] ?? null;

        if ($days === null || blank($startsAt)) {
            return;
        }

        $set('ends_at', CarbonImmutable::parse($startsAt)->addDays($days)->toDateTimeString());
    }
}
