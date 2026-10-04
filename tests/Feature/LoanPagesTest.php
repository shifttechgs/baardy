<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Tests\TestCase;

class LoanPagesTest extends TestCase
{
    public function test_every_product_has_a_page_with_its_own_title_and_description(): void
    {
        $titles = [];
        $descriptions = [];

        foreach (config('marketing.products') as $product) {
            $page = config('loans')[$product['slug']] ?? null;
            $this->assertNotNull($page, "{$product['name']} has no page copy in config/loans.php");

            $response = $this->get(route('loans.show', $product['slug']));

            $response->assertOk()
                ->assertSee($page['headline'])
                ->assertSee('<title>'.$page['title'].'</title>', false)
                ->assertSee('rel="canonical" href="'.route('loans.show', $product['slug']).'"', false)
                ->assertSee('application/ld+json', false)
                ->assertSee(route('contact', ['interest' => $product['name']]), false);

            $titles[] = $page['title'];
            $descriptions[] = $page['meta'];
        }

        $this->assertSame($titles, array_unique($titles), 'titles must be unique');
        $this->assertSame($descriptions, array_unique($descriptions), 'descriptions must be unique');
    }

    public function test_titles_and_descriptions_fit_a_search_result(): void
    {
        foreach (config('loans') as $slug => $page) {
            $this->assertLessThanOrEqual(65, mb_strlen($page['title']), "{$slug} title is too long");
            $this->assertLessThanOrEqual(165, mb_strlen($page['meta']), "{$slug} description is too long");
        }
    }

    public function test_an_unknown_loan_is_a_404(): void
    {
        $this->get('/loans/payday-cash-instantly')->assertNotFound();
    }

    public function test_the_loans_hub_links_every_loan(): void
    {
        $response = $this->get(route('loans.index'))->assertOk();

        foreach (config('marketing.products') as $product) {
            $response->assertSee(route('loans.show', $product['slug']), false);
        }
    }

    public function test_the_page_marks_up_the_faq_it_shows(): void
    {
        $this->get(route('loans.show', 'educational-loans'))
            ->assertSee('FAQPage', false)
            ->assertSee('Can I repay early?')
            ->assertSee('no early-settlement penalty');
    }

    public function test_the_loan_a_promotion_is_on_shows_the_offer(): void
    {
        Promotion::factory()->live()->placedIn(['product'])->forProduct('Educational Loans')->create(['summary' => 'Term fees spread out']);

        $this->get(route('loans.show', 'educational-loans'))->assertSee('Term fees spread out');
    }

    public function test_the_sitemap_lists_the_loan_pages_and_live_promotions_only(): void
    {
        $live = Promotion::factory()->live()->create(['slug' => 'live-offer']);
        Promotion::factory()->ended()->create(['slug' => 'old-offer']);

        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee(route('loans.show', 'sme-bridging-finance'), false)
            ->assertSee(route('promotions.show', $live), false)
            ->assertDontSee('old-offer')
            ->assertDontSee('/admin');
    }

    public function test_robots_points_at_the_sitemap_and_keeps_crawlers_out_of_the_back_office(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'), false)
            ->assertSee('Disallow: /admin', false);
    }
}
