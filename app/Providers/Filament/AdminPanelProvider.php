<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Pages\Dashboard;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * The Baardy admin panel at /admin.
 *
 * Light only, in the manner of Stripe's and Stitch's dashboards: the theme
 * (resources/css/filament/admin/theme.css) sets the soft page, white
 * hairline-separated surfaces and self-hosted Geist; the palette here is
 * Baardy purple on cool greys. Only users with `is_admin` get in
 * (User::canAccessPanel()).
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->darkMode(false)
            ->brandName('Baardy Micro Capital')
            ->brandLogo(fn (): View => view('filament.brand'))
            ->brandLogoHeight('1.75rem')
            ->favicon(asset('images/baardy-mark.png'))
            ->font('Geist', provider: LocalFontProvider::class)
            ->colors([
                // Baardy purple as a full scale, with the brand colour at 700,
                // so buttons take the true purple with white text rather than
                // a pale tint with dark text.
                'primary' => [
                    50 => '#f7f3fa',
                    100 => '#efe6f5',
                    200 => '#dccbea',
                    300 => '#c3a4da',
                    400 => '#a071c3',
                    500 => '#8045a8',
                    600 => '#6b2f93',
                    700 => '#5e2681',
                    800 => '#4a1d68',
                    900 => '#3a1752',
                    950 => '#240d35',
                ],
                'gray' => Color::Slate,
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('16rem')
            ->renderHook(PanelsRenderHook::SCRIPTS_AFTER, fn (): View => view('filament.session-expired'))
            ->renderHook(PanelsRenderHook::SIDEBAR_FOOTER, fn (): View => view('filament.sidebar-credit'))
            ->renderHook(PanelsRenderHook::SIMPLE_LAYOUT_START, fn (): View => view('filament.auth.top'))
            ->renderHook(PanelsRenderHook::SIMPLE_PAGE_END, fn (): View => view('filament.auth.bottom'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
