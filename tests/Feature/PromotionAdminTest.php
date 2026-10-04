<?php

namespace Tests\Feature;

use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Models\Promotion;
use App\Models\User;
use App\PromotionStatus;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The admin panel: who gets in, and the sign-off from draft to live.
 */
class PromotionAdminTest extends TestCase
{
    public function test_only_admins_can_open_the_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())->get('/admin/promotions')->assertOk();
    }

    public function test_an_admin_can_create_a_promotion_as_a_draft(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreatePromotion::class)
            ->fillForm([
                'title' => 'School fees season',
                'slug' => 'school-fees-season',
                'summary' => 'Term fees, spread over the term',
                'body' => 'Details of the offer.',
                'terms' => 'Subject to application, affordability assessment and approval.',
                'product' => 'Educational Loans',
                'cta_label' => 'Visit a branch',
                'tracking_code' => 'SCHOOL27',
                'show_in_hero' => true,
                'duration' => 'custom',
                'starts_at' => now()->toDateTimeString(),
                'ends_at' => now()->addMonth()->toDateTimeString(),
            ])
            ->call('saveDraft')
            ->assertHasNoFormErrors();

        $promotion = Promotion::firstWhere('slug', 'school-fees-season');
        $this->assertNotNull($promotion);
        $this->assertSame(PromotionStatus::Draft, $promotion->status);
    }

    public function test_terms_and_an_end_date_after_the_start_are_required(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreatePromotion::class)
            ->fillForm([
                'title' => 'No terms',
                'slug' => 'no-terms',
                'summary' => 'Missing its terms',
                'body' => 'Details.',
                'terms' => '',
                'duration' => 'custom',
                'starts_at' => now()->toDateTimeString(),
                'ends_at' => now()->subDay()->toDateTimeString(),
            ])
            ->call('saveDraft')
            ->assertHasFormErrors(['terms' => 'required', 'ends_at' => 'after']);
    }

    public function test_the_form_fills_in_everything_but_the_offer_itself(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreatePromotion::class)
            ->assertSchemaStateSet([
                'duration' => '1m',
                'show_in_hero' => true,
                'show_in_menu' => true,
                'cta_label' => 'Visit a branch',
            ])
            ->fillForm([
                'title' => 'Harvest season',
                'summary' => 'Inputs now, repay after harvest',
                'body' => 'Details.',
            ])
            ->set('data.product', 'Agricultural Loans')
            ->callAction('saveDraft')
            ->assertHasNoFormErrors();

        $promotion = Promotion::sole();
        $this->assertSame('harvest-season', $promotion->slug);
        $this->assertNotEmpty($promotion->tracking_code);
        $this->assertNotEmpty($promotion->terms);
        $this->assertTrue($promotion->show_on_product);
        $this->assertSame(30, (int) round($promotion->starts_at->diffInDays($promotion->ends_at)));
    }

    public function test_an_approver_can_publish_straight_from_the_create_page(): void
    {
        $approver = User::factory()->approver()->create();
        $this->actingAs($approver);

        Livewire::test(CreatePromotion::class)
            ->fillForm(['title' => 'Straight to live', 'summary' => 'Live today', 'body' => 'Details.'])
            ->callAction('publish')
            ->assertHasNoFormErrors();

        $promotion = Promotion::sole();
        $this->assertSame(PromotionStatus::Approved, $promotion->status);
        $this->assertTrue($promotion->approver->is($approver));
        $this->assertTrue($promotion->isLive());
    }

    public function test_an_admin_who_is_not_an_approver_can_only_submit_for_approval(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreatePromotion::class)
            ->fillForm(['title' => 'Needs sign-off', 'summary' => 'Waiting', 'body' => 'Details.'])
            ->call('publish')
            ->assertForbidden();

        Livewire::test(CreatePromotion::class)
            ->assertActionDoesNotExist('publish')
            ->fillForm(['title' => 'Needs sign-off', 'summary' => 'Waiting', 'body' => 'Details.'])
            ->callAction('submitForApproval')
            ->assertHasNoFormErrors();

        $this->assertSame(PromotionStatus::AwaitingApproval, Promotion::sole()->status);
    }

    public function test_an_approver_can_save_and_publish_an_edit_in_one_step(): void
    {
        $this->actingAs(User::factory()->approver()->create());
        $promotion = Promotion::factory()->live()->create();

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->assertActionHasLabel('saveAndPublish', 'Save and publish')
            ->fillForm(['title' => 'A better title'])
            ->callAction('saveAndPublish')
            ->assertHasNoFormErrors();

        $promotion->refresh();
        $this->assertSame('A better title', $promotion->title);
        $this->assertSame(PromotionStatus::Approved, $promotion->status);
    }

    public function test_save_keeps_the_status_and_warns_when_it_takes_a_promotion_off_the_site(): void
    {
        $this->actingAs(User::factory()->approver()->create());
        $promotion = Promotion::factory()->live()->create();

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->fillForm(['title' => 'Edited'])
            ->callAction('save')
            ->assertNotified('Changes need approval again');

        $this->assertSame(PromotionStatus::Draft, $promotion->refresh()->status);
    }

    public function test_duplicating_makes_a_fresh_draft(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $original = Promotion::factory()->live()->create(['title' => 'Harvest']);

        Livewire::test(ListPromotions::class)
            ->callAction(TestAction::make('replicate')->table($original));

        $copy = Promotion::whereKeyNot($original->getKey())->sole();
        $this->assertSame('Copy of Harvest', $copy->title);
        $this->assertSame(PromotionStatus::Draft, $copy->status);
        $this->assertNull($copy->approved_by);
        $this->assertNotSame($original->slug, $copy->slug);
        $this->assertNotSame($original->tracking_code, $copy->tracking_code);
        $this->assertSame($original->terms, $copy->terms);
    }

    public function test_a_promotion_moves_from_draft_to_approved_through_the_sign_off(): void
    {
        $approver = User::factory()->approver()->create();
        $promotion = Promotion::factory()->create();

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->callAction('saveAndSubmit');
        $this->assertSame(PromotionStatus::AwaitingApproval, $promotion->refresh()->status);

        $this->actingAs($approver);

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->assertActionHasLabel('saveAndPublish', 'Approve and publish')
            ->callAction('saveAndPublish');

        $promotion->refresh();
        $this->assertSame(PromotionStatus::Approved, $promotion->status);
        $this->assertTrue($promotion->approver->is($approver));
        $this->assertNotNull($promotion->approved_at);
    }

    public function test_an_admin_who_is_not_an_approver_cannot_approve(): void
    {
        $promotion = Promotion::factory()->awaitingApproval()->create();

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(EditPromotion::class, ['record' => $promotion->getRouteKey()])
            ->assertActionDoesNotExist('saveAndPublish')
            ->call('saveAndPublish')
            ->assertForbidden();

        $this->assertSame(PromotionStatus::AwaitingApproval, $promotion->refresh()->status);
    }

    public function test_the_list_shows_promotions(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $promotions = Promotion::factory()->count(3)->create();

        Livewire::test(ListPromotions::class)->assertCanSeeTableRecords($promotions);
    }
}
