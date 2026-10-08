<?php

namespace App\Providers;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // In production every generated URL (canonical links, the sitemap,
        // Open Graph, structured data) uses the real https address from
        // APP_URL, whatever host or scheme the request arrived on. Without it
        // a request through a proxy, a bare IP or a www host can put the wrong
        // address into what search engines read.
        if ($this->app->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }

        // Every delete asks first, in the same words: what goes, and that it
        // cannot be undone. Screens with something specific to say override
        // the description where the action is declared.
        DeleteAction::configureUsing(fn (DeleteAction $action): DeleteAction => $action
            ->modalDescription('This cannot be undone.')
            ->modalSubmitActionLabel('Delete'));
        DeleteBulkAction::configureUsing(fn (DeleteBulkAction $action): DeleteBulkAction => $action
            ->modalHeading('Delete the selected records?')
            ->modalDescription('They are removed for good. This cannot be undone.')
            ->modalSubmitActionLabel('Delete'));

        // Every admin dropdown is Filament's styled list, not the browser's
        // native one, so form selects and table filters share the panel's look.
        Select::configureUsing(fn (Select $select): Select => $select->native(false));
        SelectFilter::configureUsing(fn (SelectFilter $filter): SelectFilter => $filter->native(false));
    }
}
