<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Filament's sign-in page with Baardy's wording. Behaviour -- throttling,
 * remember-me, multi-factor challenges -- is Filament's, untouched; only the
 * heading and subheading change, and only outside a multi-factor challenge.
 */
class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return 'Sign in to Baardy';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return 'Manage promotions and site content.';
    }
}
