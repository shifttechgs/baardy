<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

/**
 * Why a lead was closed without a loan. Required when closing, so the funnel
 * report can say where people drop out, not just that they did.
 */
enum LeadLostReason: string implements HasLabel
{
    case Unreachable = 'unreachable';
    case NotEligible = 'not_eligible';
    case NotInterested = 'not_interested';
    case WentElsewhere = 'went_elsewhere';
    case Declined = 'declined';
    case Spam = 'spam';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unreachable => 'Could not reach them',
            self::NotEligible => 'Not eligible',
            self::NotInterested => 'No longer interested',
            self::WentElsewhere => 'Went to another lender',
            self::Declined => 'Application declined',
            self::Spam => 'Spam or test',
        };
    }
}
