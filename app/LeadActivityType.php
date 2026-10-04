<?php

namespace App;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

/**
 * One line of a lead's timeline.
 */
enum LeadActivityType: string implements HasIcon, HasLabel
{
    case Enquired = 'enquired';
    case EnquiredAgain = 'enquired_again';
    case StageChanged = 'stage_changed';
    case Assigned = 'assigned';
    case Note = 'note';

    public function getLabel(): string
    {
        return match ($this) {
            self::Enquired => 'Enquired on the website',
            self::EnquiredAgain => 'Enquired again',
            self::StageChanged => 'Moved',
            self::Assigned => 'Assigned',
            self::Note => 'Note',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Enquired, self::EnquiredAgain => Heroicon::OutlinedGlobeAlt,
            self::StageChanged => Heroicon::OutlinedArrowRight,
            self::Assigned => Heroicon::OutlinedUser,
            self::Note => Heroicon::OutlinedChatBubbleBottomCenterText,
        };
    }
}
