<?php

namespace Tests\Feature;

use App\Models\Vacancy;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * What search engines and AI assistants read: structured data, share
 * previews, crawler files and the pages that answer common questions.
 */
class SearchVisibilityTest extends TestCase
{
    /**
     * @return array<int, array{0: string}>
     */
    public static function publicPaths(): array
    {
        return array_map(fn (string $path): array => [$path], [
            '/', '/about', '/contact', '/careers', '/partners', '/promotions', '/insights',
            '/loans', '/loans/salary-based-loans', '/loans/educational-loans', '/loans/sme-bridging-finance',
            '/insights/budgeting-on-an-irregular-income', '/how-to-apply', '/faq', '/glossary',
            '/privacy', '/terms', '/responsible-lending', '/complaints',
        ]);
    }

    #[DataProvider('publicPaths')]
    public function test_every_structured_data_block_is_valid_json_with_a_schema_context(string $path): void
    {
        Vacancy::factory()->create();

        $html = $this->get($path)->assertOk()->getContent();
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $blocks);

        foreach ($blocks[1] as $json) {
            $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

            $this->assertSame('https://schema.org', $data['@context'], "{$path} has a block without a context");
        }
    }

    public function test_the_home_page_declares_the_organization_once_and_never_an_unverified_fact(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '"@type":["Organization","FinancialService"]'), 'one Organization node, referenced elsewhere by @id');
        $this->assertStringContainsString('"foundingDate":"2015"', $html);

        foreach (['example.com', 'PLACEHOLDER', 'sameAs', '"email"', 'aggregateRating', 'openingHours', 'priceRange'] as $unverified) {
            $this->assertStringNotContainsString($unverified, $html, "{$unverified} must not be asserted");
        }
    }

    public function test_the_loan_service_points_at_the_one_organization(): void
    {
        $html = $this->get('/loans/salary-based-loans')->getContent();

        $this->assertStringContainsString('"provider":{"@id":"'.url('/').'#organization"}', $html);
    }

    public function test_the_faq_names_the_public_service_commission_correctly(): void
    {
        $this->get('/loans/salary-based-loans')
            ->assertSee('Public Service Commission employees')
            ->assertDontSee('public Service Commission');
    }

    public function test_open_vacancies_become_job_postings_and_closed_ones_do_not(): void
    {
        $open = Vacancy::factory()->create(['title' => 'Credit Officer', 'slug' => 'credit-officer', 'location' => 'Harare', 'closes_on' => today()->addDays(10)]);
        Vacancy::factory()->closed()->create(['title' => 'Old Role', 'slug' => 'old-role']);

        $html = $this->get('/careers')->assertOk()->getContent();

        $this->assertStringContainsString('"@type":"JobPosting"', $html);
        $this->assertStringContainsString('Credit Officer', $html);
        $this->assertStringContainsString('"validThrough"', $html);
        $this->assertStringNotContainsString('baseSalary', $html, 'no salary is recorded, so none is stated');
        $this->assertStringContainsString('id="role-credit-officer"', $html, 'the posting url points at a real anchor');
        $this->assertStringNotContainsString('"title":"Old Role"', $html);
        $this->assertNotNull($open);
    }

    public function test_an_article_has_article_markup_and_its_own_share_image(): void
    {
        $html = $this->get('/insights/before-your-first-loan-application')->assertOk()->getContent();

        $this->assertStringContainsString('"@type":"Article"', $html);
        $this->assertStringContainsString('<meta property="og:type" content="article">', $html);
        $this->assertStringContainsString('images/insights/first-loan-1600.webp', $html);
        $this->assertStringContainsString('article:published_time', $html);
        $this->assertStringContainsString(route('loans.show', 'salary-based-loans'), $html, 'the guide links to the loans it relates to');
    }

    public function test_every_page_has_a_share_image_and_the_icons(): void
    {
        $this->get('/about')
            ->assertSee('<meta property="og:image" content="'.asset('images/og-default.jpg').'">', false)
            ->assertSee('<meta name="twitter:image"', false)
            ->assertSee('rel="apple-touch-icon"', false)
            ->assertSee('rel="icon"', false);

        $this->assertGreaterThan(0, filesize(public_path('favicon.ico')));
        $this->assertSame([1200, 630], array_slice(getimagesize(public_path('images/og-default.jpg')), 0, 2));
    }

    public function test_missing_pages_are_kept_out_of_the_index(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('<meta name="robots" content="noindex">', false);
        $this->get('/about')->assertDontSee('name="robots"', false);
    }

    public function test_robots_names_the_ai_search_crawlers_and_keeps_each_out_of_the_back_office(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();

        foreach (['GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'PerplexityBot', 'ClaudeBot', 'Google-Extended'] as $bot) {
            $this->assertMatchesRegularExpression('/User-agent: '.preg_quote($bot, '/')."\nAllow: \/\nDisallow: \/admin\nDisallow: \/staff/", $robots);
        }
    }

    public function test_llms_txt_maps_the_site_for_assistants(): void
    {
        $this->get('/llms.txt')
            ->assertOk()
            ->assertSee('# Baardy Micro Capital')
            ->assertSee(route('loans.show', 'educational-loans'), false)
            ->assertSee(route('how-to-apply'), false)
            ->assertSee(config('company.compliance.register_url'), false)
            ->assertDontSee('/admin');
    }

    public function test_the_sitemap_carries_last_modified_dates_and_the_new_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<lastmod>'.config('seo.reviewed').'</lastmod>', false)
            ->assertSee(route('faq'), false)
            ->assertSee(route('glossary'), false)
            ->assertSee(route('how-to-apply'), false);
    }

    public function test_the_question_pages_answer_from_confirmed_facts(): void
    {
        $this->get('/how-to-apply')
            ->assertOk()
            ->assertSee('valid national ID and your latest payslip')
            ->assertSee('one working day');

        $this->get('/faq')->assertOk()->assertSee('Is Baardy Micro Capital licensed?')->assertSee('FAQPage', false);
        $this->get('/glossary')->assertOk()->assertSee('DefinedTermSet', false)->assertSee('Credit-only microfinance institution');
    }

    public function test_titles_are_escaped_once_and_fit_a_search_result(): void
    {
        foreach (self::publicPaths() as [$path]) {
            $html = $this->get($path)->getContent();
            preg_match('#<title>(.*?)</title>#s', $html, $title);

            $this->assertStringNotContainsString('&amp;amp;', $title[1], "{$path} title is escaped twice");
            $this->assertLessThanOrEqual(70, mb_strlen(html_entity_decode($title[1])), "{$path} title is too long");
        }
    }
}
