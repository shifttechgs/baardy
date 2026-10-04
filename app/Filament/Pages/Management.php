<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * The front door to running the organisation from the panel, instead of paper
 * and spreadsheets. It lists the modules that make up the management side and
 * where each one stands, so the menu reads as a plan before any of them exist.
 * As a module is built it gets its own item under this group, and its card here
 * links to it.
 */
class Management extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Overview';

    protected static ?string $slug = 'management';

    protected string $view = 'filament.pages.management';

    public function getTitle(): string
    {
        return 'Management';
    }

    public function getSubheading(): ?string
    {
        return 'Clients, loans, repayments and the team, in one place and on the record: who did what, and when.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('proposal')
                ->label('Read the full proposal')
                ->icon(Heroicon::OutlinedDocumentText)
                ->url(Proposal::getUrl()),
        ];
    }

    /**
     * The modules, in the order they would be built. `status` is one of
     * "Live" (has its own menu item), "Next" or "Planned".
     *
     * @return list<array{title: string, status: string, icon: Heroicon, summary: string, answers: string}>
     */
    public function modules(): array
    {
        return [
            [
                'title' => 'Clients',
                'status' => 'Next',
                'icon' => Heroicon::OutlinedUsers,
                'summary' => 'One record per client: identity, employer or business, contacts, guarantors and documents. Leads become clients when a loan is approved.',
                'answers' => 'Who is this person, and what have we agreed with them?',
            ],
            [
                'title' => 'Loans',
                'status' => 'Next',
                'icon' => Heroicon::OutlinedBanknotes,
                'summary' => 'Applications, appraisal and approval, then the loan itself: amount, term, rate, fees and the repayment schedule, with an officer on every file.',
                'answers' => 'What do we lend, to whom, and who approved it?',
            ],
            [
                'title' => 'Repayments',
                'status' => 'Planned',
                'icon' => Heroicon::OutlinedCalendarDays,
                'summary' => 'Every instalment, due date and payment received, matched to the schedule, with early settlement and part payments handled.',
                'answers' => 'What is due, when, and what has been paid?',
            ],
            [
                'title' => 'Collections',
                'status' => 'Planned',
                'icon' => Heroicon::OutlinedBellAlert,
                'summary' => 'A daily worklist of who to call: due today, late and promised payments, with reminders sent by SMS or WhatsApp before they fall behind.',
                'answers' => 'Who do we need to speak to today?',
            ],
            [
                'title' => 'Team and branches',
                'status' => 'Planned',
                'icon' => Heroicon::OutlinedBuildingStorefront,
                'summary' => 'Staff accounts with roles and limits, branch targets, and a full activity log of every change.',
                'answers' => 'Who did this, and were they allowed to?',
            ],
            [
                'title' => 'Reports',
                'status' => 'Planned',
                'icon' => Heroicon::OutlinedChartBar,
                'summary' => 'The loan book, disbursements, collections and loans at risk, by branch, product and officer, ready to print or export.',
                'answers' => 'How is the business doing, and where is it slipping?',
            ],
        ];
    }
}
