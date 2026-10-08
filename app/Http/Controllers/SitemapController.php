<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Support\StructuredData;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * What search engines and AI assistants read to find and understand the site:
 *
 *   /sitemap.xml  every public page, with a last-modified date where one is known
 *   /robots.txt   what crawlers may read, naming the AI search bots explicitly
 *   /llms.txt     a plain-text map of the site for AI assistants
 *
 * The admin panel and staff routes are left out and disallowed. Everything is
 * built from config and the database, so a new loan or a newly live promotion
 * appears without anyone editing a file.
 */
class SitemapController extends Controller
{
    private const DISALLOWED = ['/admin', '/staff', '/livewire'];

    public function sitemap(): Response
    {
        $reviewed = config('seo.reviewed');

        /** @var Collection<string, string|null> $pages path => last modified (Y-m-d) */
        $pages = collect(['/' => null, '/loans' => $reviewed, '/about' => null, '/contact' => null, '/how-to-apply' => null, '/faq' => null, '/glossary' => null, '/partners' => null, '/careers' => null, '/insights' => null]);

        foreach (config('marketing.products') as $product) {
            if (isset($product['slug'])) {
                $pages->put('/loans/'.$product['slug'], $reviewed);
            }
        }

        foreach (config('insights.articles') as $article) {
            $pages->put('/insights/'.$article['slug'], $article['modified'] ?? $article['published']);
        }

        $pages->put('/promotions', null);

        foreach (Promotion::query()->live()->get(['slug', 'updated_at']) as $promotion) {
            $pages->put('/promotions/'.$promotion->slug, $promotion->updated_at?->toDateString());
        }

        foreach (config('company.legal') as $page) {
            $pages->put(route($page['route'], absolute: false), null);
        }

        $urls = $pages->map(fn (?string $lastModified, string $path): array => ['loc' => url($path), 'lastmod' => $lastModified])->values();

        return response(view('sitemap', ['urls' => $urls]))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $disallow = array_map(fn (string $path): string => 'Disallow: '.$path, self::DISALLOWED);
        $lines = ['User-agent: *', ...$disallow, ''];

        // A bot that matches its own group ignores the `*` group, so each
        // AI crawler repeats the disallow rules rather than being left able
        // to read the back office.
        foreach (config('seo.ai_crawlers') as $crawler) {
            $lines = [...$lines, 'User-agent: '.$crawler, 'Allow: /', ...$disallow, ''];
        }

        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function llms(): Response
    {
        $lines = [
            '# '.StructuredData::BRAND,
            '',
            '> Zimbabwean-owned, credit-only microfinance institution licensed by the Reserve Bank of Zimbabwe, listed on its register since '.config('company.compliance.licensed_since').'. Offices in Harare and Bulawayo. Loans are applied for in person at a branch, and a person reviews every application.',
            '',
            '## Loans',
        ];

        foreach (config('marketing.products') as $product) {
            if (isset($product['slug'])) {
                $lines[] = '- ['.$product['name'].']('.route('loans.show', $product['slug']).'): '.$product['summary'];
            }
        }

        array_push(
            $lines,
            '',
            '## About and trust',
            '- [About Baardy Micro Capital]('.route('about').'): who we are, licensing and the people we lend to',
            '- [Responsible lending]('.route('legal.responsible-lending').'): how we lend, and what to do if repaying becomes hard',
            '- [Complaints procedure]('.route('legal.complaints').'): how to complain and what happens next',
            '- [Privacy notice]('.route('legal.privacy').'): what we collect, why, and your rights',
            '- [How to apply]('.route('how-to-apply').'): the steps and the documents to bring',
            '- [Frequently asked questions]('.route('faq').'): documents, repayment, missed payments, early settlement and our licence',
            '- [Glossary]('.route('glossary').'): loan terms in plain language',
            '- [Contact]('.route('contact').'): offices, phone and WhatsApp',
            '',
            '## Guides',
        );

        foreach (config('insights.articles') as $article) {
            $lines[] = '- ['.$article['title'].']('.route('insights.show', $article['slug']).'): '.$article['excerpt'];
        }

        $register = config('company.compliance.register_url');

        if (filled($register)) {
            array_push($lines, '', '## Verify our licence', '- [Reserve Bank of Zimbabwe microfinance register]('.$register.')');
        }

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
