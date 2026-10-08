<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Widgets\LeadFunnel;
use App\Filament\Widgets\LeadStats;
use App\Filament\Widgets\LeadsToCall;
use App\LeadActivityType;
use App\LeadLostReason;
use App\LeadStage;
use App\Models\Lead;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Working leads in the admin panel: the list, moving a lead through the
 * funnel, notes, logging a lead by hand, and the dashboard numbers.
 */
class LeadAdminTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_only_admins_can_see_leads(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(LeadResource::getUrl('view', ['record' => $lead]))
            ->assertForbidden();
    }

    public function test_the_open_tab_lists_open_leads_only(): void
    {
        $open = Lead::factory()->count(2)->create();
        $closed = Lead::factory()->lost()->create();

        Livewire::test(ListLeads::class)
            ->assertCanSeeTableRecords($open)
            ->assertCanNotSeeTableRecords([$closed]);
    }

    public function test_the_sidebar_badge_counts_new_leads_and_turns_red_when_one_is_overdue(): void
    {
        Lead::factory()->count(2)->create();
        $this->assertSame('2', LeadResource::getNavigationBadge());
        $this->assertSame('primary', LeadResource::getNavigationBadgeColor());

        $this->travel(LeadResource::RESPONSE_TARGET_HOURS + 1)->hours();
        $this->assertSame('danger', LeadResource::getNavigationBadgeColor());
    }

    public function test_the_next_step_button_walks_a_lead_to_approved(): void
    {
        $lead = Lead::factory()->create();

        $page = Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
            ->assertActionHasLabel('advance', 'Mark contacted')
            ->callAction('advance');

        $this->assertSame(LeadStage::Contacted, $lead->refresh()->stage);
        $this->assertTrue($lead->assignee->is($this->admin));

        $page->assertActionHasLabel('advance', 'Application started')->callAction('advance');
        $this->assertSame(LeadStage::Application, $lead->refresh()->stage);

        $page->assertActionHidden('advance')->assertActionHasLabel('approve', 'Mark approved')->callAction('approve');
        $this->assertSame(LeadStage::Approved, $lead->refresh()->stage);
        $this->assertNotNull($lead->closed_at);

        $page->assertActionHidden('advance')->assertActionHidden('approve')->assertActionHidden('closeAsLost');
    }

    public function test_closing_as_lost_needs_a_reason(): void
    {
        $lead = Lead::factory()->contacted()->create();

        Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
            ->callAction('closeAsLost', data: ['reason' => null])
            ->assertHasFormErrors(['reason' => 'required']);

        $this->assertSame(LeadStage::Contacted, $lead->refresh()->stage);

        Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
            ->callAction('closeAsLost', data: ['reason' => LeadLostReason::NotEligible->value, 'note' => 'Not salaried.']);

        $lead->refresh();
        $this->assertSame(LeadStage::Lost, $lead->stage);
        $this->assertSame(LeadLostReason::NotEligible, $lead->lost_reason);
    }

    public function test_a_note_goes_on_the_timeline_with_its_author(): void
    {
        $lead = Lead::factory()->create();

        Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
            ->callAction('addNote', data: ['body' => 'Call back after 5pm.'])
            ->assertSee('Call back after 5pm.');

        $note = $lead->activities()->where('type', LeadActivityType::Note)->sole();
        $this->assertTrue($note->user->is($this->admin));
    }

    public function test_a_phone_enquiry_can_be_logged_by_hand_and_is_assigned_to_whoever_logged_it(): void
    {
        Livewire::test(CreateLead::class)
            ->fillForm([
                'name' => 'Nyasha Banda',
                'phone' => '0772 000 111',
                'interest' => 'Salary-Based Loans',
                'branch' => 'Harare',
                'source' => 'walk-in',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $lead = Lead::sole();
        $this->assertSame('walk-in', $lead->utm_source);
        $this->assertTrue($lead->assignee->is($this->admin));
        $this->assertSame('Walk-in', $lead->sourceLabel());
    }

    public function test_logging_a_number_that_already_has_an_open_lead_adds_to_it(): void
    {
        $existing = Lead::factory()->create(['phone' => '+263 772 000 111']);

        Livewire::test(CreateLead::class)
            ->fillForm([
                'name' => 'Nyasha Banda',
                'phone' => '0772 000 111',
                'interest' => 'Salary-Based Loans',
                'branch' => 'Harare',
                'source' => 'phone',
            ])
            ->call('create')
            ->assertRedirect(LeadResource::getUrl('view', ['record' => $existing]));

        $this->assertSame(1, Lead::count());
    }

    public function test_the_dashboard_shows_the_lead_widgets(): void
    {
        Lead::factory()->count(3)->create();
        Lead::factory()->approved()->create();
        Lead::factory()->lost(LeadLostReason::Unreachable)->create();

        $this->get(Dashboard::getUrl())->assertOk();

        Livewire::test(LeadStats::class)->assertSee('Waiting for a call')->assertSee('20%');
        Livewire::test(LeadFunnel::class)->assertSee('Started an application')->assertSee('Could not reach them');
        Livewire::test(LeadsToCall::class)->assertCanSeeTableRecords(Lead::where('stage', LeadStage::New)->get());
    }
}
