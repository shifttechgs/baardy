<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionPageTest extends TestCase
{
    use RefreshDatabase;

    private function livePromotion(array $attributes = []): Promotion
    {
        return Promotion::factory()->live()->forProduct('Educational Loans')->create([
            'title' => 'School fees season',
            'tracking_code' => 'SCHOOL27',
            'cta_label' => 'Start an application',
            'ends_at' => now()->addDays(12),
            ...$attributes,
        ]);
    }

    public function test_the_image_frame_follows_the_posters_own_proportions_within_limits(): void
    {
        Storage::fake('public');

        $square = Promotion::factory()->create(['image_path' => UploadedFile::fake()->image('square.png', 600, 600)->store('promotions', 'public')]);
        $wide = Promotion::factory()->create(['image_path' => UploadedFile::fake()->image('wide.png', 2000, 500)->store('promotions', 'public')]);
        $tall = Promotion::factory()->create(['image_path' => UploadedFile::fake()->image('tall.png', 400, 1600)->store('promotions', 'public')]);
        $none = Promotion::factory()->create(['image_path' => null]);

        $this->assertSame(1.0, $square->imageAspectRatio());
        $this->assertSame(round(16 / 9, 3), $wide->imageAspectRatio(), 'a very wide banner is capped at 16:9');
        $this->assertSame(0.8, $tall->imageAspectRatio(), 'a very tall poster is capped at 4:5');
        $this->assertSame(round(4 / 3, 3), round($none->imageAspectRatio(), 3), 'no image falls back to 4:3');
    }

    public function test_it_says_how_long_is_left(): void
    {
        $this->assertSame('12 days left', $this->livePromotion()->timeLeftLabel());
        $this->assertSame('Ends tomorrow', $this->livePromotion(['slug' => 'b', 'tracking_code' => 'B', 'ends_at' => now()->addDay()])->timeLeftLabel());
        $this->assertSame('Ends today', $this->livePromotion(['slug' => 'c', 'tracking_code' => 'C', 'ends_at' => now()->endOfDay()])->timeLeftLabel());
    }

    public function test_the_page_shows_the_facts_and_the_time_left(): void
    {
        $promotion = $this->livePromotion();

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSee('12 days left')
            ->assertSee('Educational Loans')
            ->assertSee($promotion->ends_at->format('j F Y'))
            ->assertSee('Take up this offer')
            ->assertSee('A person from our team calls you back.');
    }

    public function test_the_action_appears_three_times_and_always_carries_the_tracking_code(): void
    {
        $promotion = $this->livePromotion();
        $html = (string) $this->get(route('promotions.show', $promotion))->assertOk()->getContent();

        $this->assertSame(3, substr_count($html, e($promotion->enquiryUrl())), 'under the headline, in the side card and in the closing band');
        $this->assertStringContainsString('promo=SCHOOL27', $promotion->enquiryUrl());
    }

    public function test_a_person_can_be_asked_by_whatsapp_or_phone_without_any_branch_details(): void
    {
        $promotion = $this->livePromotion();

        $this->get(route('promotions.show', $promotion))
            ->assertSee('https://wa.me/', false)
            ->assertSee(rawurlencode('"School fees season" (ref SCHOOL27)'), false)
            ->assertSee(config('company.contact.phone'))
            ->assertDontSee('Mership House')
            ->assertDontSee('Corner 9th Avenue');
    }

    public function test_the_closing_action_is_the_promotions_own_not_the_generic_slip(): void
    {
        $this->get(route('promotions.show', $this->livePromotion()))
            ->assertSee('Ready to take up School fees season?')
            ->assertDontSee('Two answers, and we');
    }

    public function test_an_ended_promotion_offers_no_action_for_the_offer_and_points_to_what_is_on(): void
    {
        $promotion = Promotion::factory()->ended()->create([
            'tracking_code' => 'GONE27',
            'cta_label' => 'Take it up now',
        ]);

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSee('This promotion has ended')
            ->assertSee('This offer has ended')
            ->assertSee('See what is on now')
            ->assertDontSee('Take it up now')
            ->assertDontSee('Take up this offer')
            ->assertDontSee('promo=GONE27');
    }
}
