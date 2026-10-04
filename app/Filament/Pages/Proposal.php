<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * The full proposal for running the organisation from the panel, readable
 * where staff and the owner already work: what changes, what is built, what
 * would be built, who and when, what runs by itself, where it pays, the phases
 * and what is needed. The same content as the shareable proposal page, kept
 * here so it is never out of reach.
 */
class Proposal extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Proposal';

    protected static ?string $slug = 'management/proposal';

    protected string $view = 'filament.pages.proposal';

    public function getTitle(): string
    {
        return 'Proposal';
    }

    public function getSubheading(): ?string
    {
        return 'Stop doing it by hand. Run the lender from one system. Try it free for 30 days.';
    }

    /**
     * The starting monthly price in US dollars (config company.proposal.price_from),
     * or null when no number is to be shown yet.
     */
    public function priceFrom(): ?int
    {
        $price = config('company.proposal.price_from');

        return is_numeric($price) && $price > 0 ? (int) $price : null;
    }
}
