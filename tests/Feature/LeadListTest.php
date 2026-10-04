<?php

namespace Tests\Feature;

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\LeadLostReason;
use App\LeadStage;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_mark_contacted_moves_a_new_lead_and_stamps_first_contact(): void
    {
        $lead = Lead::factory()->create();

        Livewire::test(ListLeads::class)
            ->callTableAction('contacted', $lead)
            ->assertNotified();

        $lead->refresh();
        $this->assertSame(LeadStage::Contacted, $lead->stage);
        $this->assertNotNull($lead->first_contacted_at);
    }

    public function test_mark_contacted_is_only_offered_on_new_leads(): void
    {
        $contacted = Lead::factory()->contacted()->create();

        Livewire::test(ListLeads::class)
            ->assertTableActionHidden('contacted', $contacted);
    }

    public function test_edit_page_shows_the_lead_and_saves_back_to_it(): void
    {
        $lead = Lead::factory()->create(['name' => 'Tendai Moyo']);

        $this->get(LeadResource::getUrl('edit', ['record' => $lead]))
            ->assertOk()
            ->assertSee('Edit Tendai Moyo')
            ->assertSee($lead->reference);

        Livewire::test(EditLead::class, ['record' => $lead->getRouteKey()])
            ->fillForm(['name' => 'Tendai Ncube'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(LeadResource::getUrl('view', ['record' => $lead]));

        $this->assertSame('Tendai Ncube', $lead->refresh()->name);
    }

    public function test_the_lead_page_shows_funnel_progress_for_each_kind_of_lead(): void
    {
        $waiting = Lead::factory()->create(['created_at' => now()->subHours(5)]);
        $won = Lead::factory()->approved()->create();
        $lost = Lead::factory()->lost(LeadLostReason::NotEligible)->create();

        $this->get(LeadResource::getUrl('view', ['record' => $waiting]))
            ->assertOk()
            ->assertSee('for a first call')
            ->assertSee('past the 2-hour target');

        $this->get(LeadResource::getUrl('view', ['record' => $won]))->assertOk()->assertSee('Loan approved');

        $this->get(LeadResource::getUrl('view', ['record' => $lost]))->assertOk()->assertSee('Closed as lost: Not eligible.');
    }

    public function test_tabs_carry_their_counts(): void
    {
        Lead::factory()->count(2)->create();
        Lead::factory()->approved()->create();

        $tabs = Livewire::test(ListLeads::class)->instance()->getTabs();

        $this->assertEquals(2, $tabs['new']->getBadge());
        $this->assertEquals(2, $tabs['open']->getBadge());
        $this->assertEquals(1, $tabs['approved']->getBadge());
        $this->assertEquals(3, $tabs['all']->getBadge());
    }
}
