<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

/**
 * Where a lead is in the funnel.
 *
 *   New          came in from the site; nobody has spoken to them yet
 *   Contacted    a person has called or messaged them
 *   Application  they have started an application at a branch
 *   Approved     the loan was approved -- the funnel's win
 *   Lost         closed without a loan, always with a LeadLostReason
 *
 * New, Contacted and Application are open; Approved and Lost are closed.
 */
enum LeadStage: string implements HasColor, HasIcon, HasLabel
{
    case New = 'new';
    case Contacted = 'contacted';
    case Application = 'application';
    case Approved = 'approved';
    case Lost = 'lost';

    /**
     * @return list<self>
     */
    public static function open(): array
    {
        return [self::New, self::Contacted, self::Application];
    }

    public function isOpen(): bool
    {
        return in_array($this, self::open(), true);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Application => 'Application',
            self::Approved => 'Approved',
            self::Lost => 'Lost',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'primary',
            self::Contacted => 'info',
            self::Application => 'warning',
            self::Approved => 'success',
            self::Lost => 'gray',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::New => Heroicon::OutlinedSparkles,
            self::Contacted => Heroicon::OutlinedPhone,
            self::Application => Heroicon::OutlinedDocumentText,
            self::Approved => Heroicon::OutlinedCheckBadge,
            self::Lost => Heroicon::OutlinedXCircle,
        };
    }
}
