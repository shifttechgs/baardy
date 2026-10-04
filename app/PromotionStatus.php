<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Where a promotion is in its sign-off.
 *
 * Only an Approved promotion can appear on the site, and only between its
 * start and end dates. Changing an approved promotion's content sends it
 * back to Draft (see Promotion::booted()), so nothing published can differ
 * from what was signed off.
 */
enum PromotionStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case AwaitingApproval = 'awaiting_approval';
    case Approved = 'approved';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::AwaitingApproval => 'Awaiting approval',
            self::Approved => 'Approved',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::AwaitingApproval => 'warning',
            self::Approved => 'success',
        };
    }
}
