<?php

namespace App\Filament\Resources\Vacancies\Schemas;

use App\Models\Vacancy;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * A job opening, in three groups: the role, what it involves, and when it is
 * shown. The slug follows the title until the role has been saved once.
 */
class VacancyForm
{
    public static function configure(Schema $schema): Schema
    {
        $branches = collect(config('company.branches'))->pluck('name', 'name')->all();

        return $schema
            ->columns(1)
            ->components([
                Section::make('The role')
                    ->description('What the role is called, where it is and how it is worked.')
                    ->icon(Heroicon::OutlinedBriefcase)
                    ->columns(['default' => 1, 'md' => 3])
                    ->schema([
                        TextInput::make('title')
                            ->placeholder('e.g. Loan Officer')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set, ?Vacancy $record): void {
                                if ($record === null) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        Select::make('location')
                            ->options($branches)
                            ->placeholder('Choose a branch')
                            ->required(),
                        Select::make('employment_type')
                            ->label('Employment type')
                            ->options(['Full-time' => 'Full-time', 'Part-time' => 'Part-time', 'Contract' => 'Contract', 'Internship' => 'Internship'])
                            ->default('Full-time')
                            ->selectablePlaceholder(false)
                            ->required(),
                        TextInput::make('summary')
                            ->helperText('One line shown on the careers page.')
                            ->placeholder('A short line that makes someone want to read on')
                            ->required()
                            ->maxLength(160)
                            ->columnSpan(['md' => 2]),
                        TextInput::make('slug')
                            ->label('Web address')
                            ->helperText('Made from the title. Letters, numbers and dashes.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),
                    ]),

                Section::make('What it involves')
                    ->description('Shown when someone opens the role on the careers page.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        Textarea::make('description')
                            ->label('About the role')
                            ->required()
                            ->rows(8),
                        Textarea::make('requirements')
                            ->helperText('One requirement per line. The first three show as tags on the role card.')
                            ->rows(8),
                    ]),

                Section::make('Publishing')
                    ->description('Whether the role is on the careers page, and until when.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->columns(['default' => 1, 'md' => 3])
                    ->schema([
                        DatePicker::make('closes_on')
                            ->label('Closing date')
                            ->helperText('Leave blank to stay open until unpublished.'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Off keeps it as a draft.')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
