<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Response;

/**
 * /sitemap.xml and /robots.txt: every public page, so search engines find the
 * loan pages, the promotions that are live and the insights without having to
 * crawl to them. The admin panel and staff routes are left out and disallowed.
 */
class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $paths = collect(['/', '/loans', '/about', '/contact', '/partners', '/careers', '/promotions', '/insights'])
            ->merge(collect(config('marketing.products'))->pluck('slug')->filter()->map(fn (string $slug): string => '/loans/'.$slug))
            ->merge(collect(config('insights.articles'))->pluck('slug')->map(fn (string $slug): string => '/insights/'.$slug))
            ->merge(Promotion::query()->live()->pluck('slug')->map(fn (string $slug): string => '/promotions/'.$slug))
            ->merge(collect(config('company.legal'))->map(fn (array $page): string => route($page['route'], absolute: false)));

        return response(view('sitemap', ['urls' => $paths->map(fn (string $path): string => url($path))->unique()->values()]))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /staff',
            'Disallow: /livewire',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
