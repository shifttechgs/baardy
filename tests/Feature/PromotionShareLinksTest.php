<?php

namespace Tests\Feature;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Lead;
use App\Models\Promotion;
use App\Models\User;
use App\PromotionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionShareLinksTest extends TestCase
{
    use RefreshDatabase;

    private function livePromotion(): Promotion
    {
        return Promotion::factory()->create([
            'title' => 'School fees season',
            'slug' => 'school-fees-season',
            'status' => PromotionStatus::Approved,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addWeek(),
        ]);
    }

    public function test_a_tagged_link_names_the_channel_and_the_promotion(): void
    {
        $url = $this->livePromotion()->trackedUrl('facebook');

        $this->assertStringStartsWith(route('promotions.show', 'school-fees-season'), $url);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame(
            ['utm_source' => 'facebook', 'utm_medium' => 'social', 'utm_campaign' => 'school-fees-season'],
            $query,
        );
    }

    public function test_the_edit_page_offers_a_copy_link_for_each_channel(): void
    {
        $promotion = $this->livePromotion();

        $page = $this->actingAs(User::factory()->admin()->create())
            ->get(PromotionResource::getUrl('edit', ['record' => $promotion]))
            ->assertOk()
            ->assertSee('Share this promotion');

        foreach (['Facebook', 'Instagram', 'WhatsApp', 'Email', 'SMS'] as $channel) {
            $page->assertSee($channel.' link', false);
        }

        $page->assertSee('utm_campaign=school-fees-season', false)
            ->assertDontSee('These links work once the promotion is live');
    }

    public function test_the_edit_page_warns_when_the_promotion_is_not_live_yet(): void
    {
        $draft = Promotion::factory()->create(['status' => PromotionStatus::Draft]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(PromotionResource::getUrl('edit', ['record' => $draft]))
            ->assertOk()
            ->assertSee('These links work once the promotion is live');
    }

    public function test_a_promotion_lead_keeps_the_channel_in_its_source(): void
    {
        $promotion = $this->livePromotion();

        $shared = Lead::factory()->create(['promotion_id' => $promotion->id, 'utm_source' => 'facebook', 'utm_medium' => 'social']);
        $plain = Lead::factory()->create(['promotion_id' => $promotion->id]);

        $this->assertSame('Promotion: School fees season (Facebook)', $shared->sourceLabel());
        $this->assertSame('Promotion: School fees season', $plain->sourceLabel());
    }
}
