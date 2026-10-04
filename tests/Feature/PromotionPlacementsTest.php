<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceived;
use App\Models\Lead;
use App\Models\Promotion;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Where promotions appear on the public site, and that only live ones do.
 */
class PromotionPlacementsTest extends TestCase
{
    public function test_nothing_promotional_renders_while_no_promotion_is_live(): void
    {
        Promotion::factory()->placedIn(['hero', 'menu', 'product'])->forProduct('Educational Loans')->create(['summary' => 'A draft offer']);
        Promotion::factory()->ended()->placedIn(['hero'])->create(['summary' => 'An ended offer']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('A draft offer')
            ->assertDontSee('An ended offer')
            ->assertDontSee('Limited offer')
            ->assertSee('Not sure which loan?');
    }

    public function test_a_live_promotion_appears_in_each_of_its_placements(): void
    {
        $promotion = Promotion::factory()->live()
            ->placedIn(['hero', 'menu', 'product'])
            ->forProduct('Educational Loans')
            ->create(['title' => 'School fees season', 'summary' => 'Term fees, spread over the term']);

        $content = $this->get('/')->assertOk()->getContent();

        $hero = substr($content, strpos($content, '<section aria-labelledby="hero-heading"'));
        $hero = substr($hero, 0, strpos($hero, '</section>'));
        $this->assertStringContainsString('Term fees, spread over the term', $hero);
        $this->assertStringContainsString(route('promotions.show', $promotion), $hero);

        $menu = substr($content, strpos($content, 'id="menu-loans"'));
        $menu = substr($menu, 0, strpos($menu, 'id="menu-about"'));
        $this->assertStringContainsString('School fees season', $menu);
        $this->assertStringNotContainsString('Not sure which loan?', $menu);

        $products = substr($content, strpos($content, 'id="products"'));
        $this->assertStringContainsString('Limited offer &middot; Term fees, spread over the term', $products);
    }

    public function test_a_banner_promotion_sits_above_the_header_on_every_page(): void
    {
        $promotion = Promotion::factory()->live()->placedIn(['banner'])
            ->create(['summary' => 'Term fees, spread over the term']);

        foreach (['/', route('promotions.index'), route('insights.index')] as $url) {
            $content = $this->get($url)->assertOk()->getContent();

            $banner = substr($content, strpos($content, 'id="promo-banner"'));
            $banner = substr($banner, 0, strpos($banner, '</a>'));
            $this->assertStringContainsString('Term fees, spread over the term', $banner);
            $this->assertStringContainsString(route('promotions.show', $promotion), $banner);
        }
    }

    public function test_on_the_homepage_the_bar_and_the_hero_both_carry_the_promotion(): void
    {
        Promotion::factory()->live()->placedIn(['banner', 'hero'])
            ->create(['title' => 'School fees season', 'summary' => 'Term fees, spread over the term']);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="promo-banner"', $content);

        $hero = substr($content, strpos($content, '<section aria-labelledby="hero-heading"'));
        $hero = substr($hero, 0, strpos($hero, '</section>'));
        $this->assertStringContainsString('School fees season', $hero);
        $this->assertStringNotContainsString('Your signature', $hero);

        $this->get(route('promotions.index'))->assertSee('id="promo-banner"', false);
    }

    public function test_no_banner_renders_without_a_live_banner_promotion(): void
    {
        Promotion::factory()->placedIn(['banner'])->create();

        $this->get('/')->assertOk()->assertDontSee('id="promo-banner"', false);
    }

    public function test_the_promotions_page_lists_only_live_promotions(): void
    {
        Promotion::factory()->live()->create(['title' => 'Running now']);
        Promotion::factory()->create(['title' => 'Still a draft']);
        Promotion::factory()->ended()->create(['title' => 'Already over']);

        $this->get(route('promotions.index'))
            ->assertOk()
            ->assertSee('Running now')
            ->assertDontSee('Still a draft')
            ->assertDontSee('Already over');
    }

    public function test_the_promotions_page_says_so_when_nothing_is_running(): void
    {
        $this->get(route('promotions.index'))
            ->assertOk()
            ->assertSee('There are no promotions running right now.');
    }

    public function test_a_promotion_page_shows_its_terms_and_its_call_to_action(): void
    {
        $promotion = Promotion::factory()->live()->forProduct('Agricultural Loans')->create([
            'terms' => 'Available to registered farmers only.',
            'cta_label' => 'Ask about this offer',
        ]);

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSee('Available to registered farmers only.')
            ->assertSee('Ask about this offer')
            ->assertSee(e($promotion->enquiryUrl()), false);
    }

    public function test_an_ended_promotion_page_stays_up_without_a_call_to_action(): void
    {
        $promotion = Promotion::factory()->ended()->create(['cta_label' => 'Ask about this offer']);

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSee('This promotion has ended')
            ->assertDontSee('Ask about this offer');
    }

    public function test_drafts_and_scheduled_promotions_are_not_public(): void
    {
        $this->get(route('promotions.show', Promotion::factory()->create()))->assertNotFound();
        $this->get(route('promotions.show', Promotion::factory()->awaitingApproval()->create()))->assertNotFound();
        $this->get(route('promotions.show', Promotion::factory()->scheduled()->create()))->assertNotFound();
    }

    public function test_an_enquiry_from_a_promotion_records_which_one(): void
    {
        Mail::fake();

        $promotion = Promotion::factory()->live()->create(['title' => 'Harvest bridge', 'tracking_code' => 'HARVEST27']);

        $this->get('/contact?promo=HARVEST27')
            ->assertOk()
            ->assertSee('<input type="hidden" name="promo" value="HARVEST27">', false);

        $this->postJson(route('enquiries.store'), [
            'name' => 'Test Borrower',
            'phone' => '+263 77 000 0000',
            'interest' => 'Agricultural Loans',
            'branch' => 'Harare',
            'promo' => 'HARVEST27',
        ])->assertOk();

        $this->assertTrue(Lead::sole()->promotion->is($promotion));
        $this->assertSame(1, $promotion->leads()->count());

        Mail::assertSent(EnquiryReceived::class, function (EnquiryReceived $mail) use ($promotion): bool {
            $mail->assertSeeInHtml('Promotion: '.$promotion->title);
            $mail->assertSeeInHtml('HARVEST27');

            return true;
        });
    }

    public function test_an_unknown_tracking_code_is_ignored_on_the_page_and_rejected_on_submit(): void
    {
        $this->get('/contact?promo=NOPE')->assertOk()->assertDontSee('name="promo"', false);

        $this->postJson(route('enquiries.store'), [
            'name' => 'Test Borrower',
            'phone' => '+263 77 000 0000',
            'interest' => 'Agricultural Loans',
            'branch' => 'Harare',
            'promo' => 'NOPE',
        ])->assertUnprocessable()->assertJsonValidationErrors('promo');
    }
}
