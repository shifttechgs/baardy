<?php

namespace App\Support;

use App\Models\Vacancy;

/**
 * Builds the site's schema.org JSON-LD.
 *
 * Only VERIFIED facts go in (config/company.php): the legal name, the year
 * founded, the head-office phone the client confirmed, the country served and
 * the regulator named in words. Never emitted, until the client confirms
 * them: the email, the company registration number, the licence number,
 * opening hours, social profiles, branch addresses and phones (the client has
 * twice asked for those to stay off the site), ratings, prices and rates.
 *
 * Every page refers to the one Organization by @id, so search and AI engines
 * resolve them all to the same entity.
 */
class StructuredData
{
    public const BRAND = 'Baardy Micro Capital';

    public static function organizationId(): string
    {
        return url('/').'#organization';
    }

    public static function websiteId(): string
    {
        return url('/').'#website';
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     * @return array<string, mixed>
     */
    public static function graph(array $nodes): array
    {
        return ['@context' => 'https://schema.org', '@graph' => $nodes];
    }

    /**
     * @return array{'@id': string}
     */
    public static function organizationRef(): array
    {
        return ['@id' => self::organizationId()];
    }

    /**
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $phone = preg_replace('/\s+/', '', (string) config('company.contact.phone'));

        return [
            '@type' => ['Organization', 'FinancialService'],
            '@id' => self::organizationId(),
            'name' => self::BRAND,
            'legalName' => config('company.legal_name'),
            'url' => url('/'),
            'description' => 'Zimbabwean-owned, credit-only microfinance institution licensed by the Reserve Bank of Zimbabwe. Salary-based, educational, agricultural, women and youth empowerment loans and SME bridging finance, with offices in Harare and Bulawayo.',
            'foundingDate' => (string) config('company.compliance.licensed_since'),
            'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe'],
            'telephone' => $phone,
            'contactPoint' => [[
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'telephone' => $phone,
                'areaServed' => 'ZW',
                'availableLanguage' => 'en',
            ]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::websiteId(),
            'url' => url('/'),
            'name' => self::BRAND,
            'inLanguage' => 'en',
            'publisher' => self::organizationRef(),
        ];
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $trail  [name, absolute url], Home first
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $trail): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($trail)->values()->map(fn (array $crumb, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb[0],
                'item' => $crumb[1],
            ])->all(),
        ];
    }

    /**
     * A plain page of one of schema.org's page types (AboutPage, ContactPage).
     *
     * @return array<string, mixed>
     */
    public static function page(string $type, string $name): array
    {
        return [
            '@type' => $type,
            '@id' => url()->current().'#webpage',
            'url' => url()->current(),
            'name' => $name,
            'isPartOf' => ['@id' => self::websiteId()],
            'about' => self::organizationRef(),
        ];
    }

    /**
     * @param  array<string, mixed>  $article  an entry of config/insights.php -> articles
     * @return array<string, mixed>
     */
    public static function article(array $article): array
    {
        return [
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['excerpt'],
            'image' => [asset('images/insights/'.$article['image'].'-1600.webp')],
            'datePublished' => $article['published'],
            'dateModified' => $article['modified'] ?? $article['published'],
            'author' => self::organizationRef(),
            'publisher' => self::organizationRef(),
            'mainEntityOfPage' => url()->current(),
            'inLanguage' => 'en',
        ];
    }

    /**
     * One open vacancy. The careers page lists roles on one page, so the
     * posting's URL is that role's anchor on it. No salary is stated because
     * none is recorded, and none is ever invented.
     *
     * @return array<string, mixed>
     */
    public static function jobPosting(Vacancy $vacancy): array
    {
        $employmentTypes = [
            'full-time' => 'FULL_TIME',
            'part-time' => 'PART_TIME',
            'contract' => 'CONTRACTOR',
            'temporary' => 'TEMPORARY',
            'internship' => 'INTERN',
        ];

        $description = '<p>'.e($vacancy->summary).'</p><p>'.nl2br(e($vacancy->description)).'</p>';

        if (filled($vacancy->requirements)) {
            $items = collect(explode("\n", $vacancy->requirements))->map('trim')->filter()->map(fn (string $line): string => '<li>'.e($line).'</li>');
            $description .= '<p>Requirements:</p><ul>'.$items->implode('').'</ul>';
        }

        return array_filter([
            '@type' => 'JobPosting',
            'title' => $vacancy->title,
            'description' => $description,
            'datePosted' => $vacancy->created_at->toDateString(),
            'validThrough' => $vacancy->closes_on?->endOfDay()->toIso8601String(),
            'employmentType' => $employmentTypes[strtolower($vacancy->employment_type)] ?? null,
            'hiringOrganization' => ['@type' => 'Organization', '@id' => self::organizationId(), 'name' => config('company.legal_name'), 'sameAs' => url('/')],
            'jobLocation' => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $vacancy->location, 'addressCountry' => 'ZW']],
            'directApply' => true,
            'url' => route('careers').'#role-'.$vacancy->slug,
        ], fn (mixed $value): bool => $value !== null);
    }
}
