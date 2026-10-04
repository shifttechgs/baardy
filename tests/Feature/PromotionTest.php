<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\User;
use App\PromotionStatus;
use Tests\TestCase;

/**
 * The rules that decide what reaches the public site.
 */
class PromotionTest extends TestCase
{
    public function test_only_approved_promotions_inside_their_dates_are_live(): void
    {
        $live = Promotion::factory()->live()->create();
        Promotion::factory()->create();
        Promotion::factory()->awaitingApproval()->create();
        Promotion::factory()->ended()->create();
        Promotion::factory()->scheduled()->create();

        $this->assertSame([$live->id], Promotion::query()->live()->pluck('id')->all());
        $this->assertTrue($live->isLive());
    }

    public function test_each_placement_shows_the_live_promotion_ending_soonest(): void
    {
        Promotion::factory()->live()->placedIn(['hero'])->create(['ends_at' => now()->addMonths(2)]);
        $soonest = Promotion::factory()->live()->placedIn(['hero'])->create(['ends_at' => now()->addWeek()]);
        Promotion::factory()->placedIn(['hero'])->create(['ends_at' => now()->addDay()]);

        $this->assertTrue(Promotion::forPlacement('hero')->is($soonest));
        $this->assertNull(Promotion::forPlacement('menu'));
        $this->assertNull(Promotion::forPlacement('not-a-placement'));
    }

    public function test_editing_an_approved_promotion_sends_it_back_for_approval(): void
    {
        $approver = User::factory()->approver()->create();
        $promotion = Promotion::factory()->live()->create();
        $promotion->approved_by = $approver->id;
        $promotion->save();

        $promotion->update(['summary' => 'A different promise']);

        $promotion->refresh();
        $this->assertSame(PromotionStatus::Draft, $promotion->status);
        $this->assertNull($promotion->approved_by);
        $this->assertNull($promotion->approved_at);
        $this->assertFalse($promotion->isLive());
    }

    public function test_approving_does_not_undo_itself(): void
    {
        $promotion = Promotion::factory()->awaitingApproval()->create();

        $promotion->status = PromotionStatus::Approved;
        $promotion->approved_at = now();
        $promotion->save();

        $this->assertSame(PromotionStatus::Approved, $promotion->refresh()->status);
    }

    public function test_the_call_to_action_preselects_the_loan_and_carries_the_tracking_code(): void
    {
        $promotion = Promotion::factory()->live()->forProduct('Educational Loans')->create(['tracking_code' => 'SCHOOL27']);

        $this->assertSame(
            route('contact', ['interest' => 'Educational Loans', 'promo' => 'SCHOOL27']).'#contact',
            $promotion->enquiryUrl()
        );
    }
}
