<?php

namespace Tests\Feature;

use App\Filament\Pages\Management;
use App\Filament\Pages\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_read_the_full_proposal_in_the_panel(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(Proposal::getUrl())
            ->assertOk()
            ->assertSee('What changes day to day')
            ->assertSee('If the government wants paper, print it. Do not run the business on it.')
            ->assertSee('Already built and working')
            ->assertSee('What we would build next')
            ->assertSee('Always knowing who and when')
            ->assertSee('What runs by itself')
            ->assertSee('Where it pays for itself')
            ->assertSee('Work it out with your own numbers')
            ->assertSee('How we would roll it out')
            ->assertSee('What we need from Baardy')
            ->assertSee('What it costs')
            ->assertSee('Try it free for 30 days');
    }

    public function test_the_price_is_shown_as_a_starting_price(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(Proposal::getUrl())
            ->assertSee('From')
            ->assertSee('US$50')
            ->assertSee('pricing starts from US$50 a month')
            ->assertSee('Staff time covers the starting price (US$50 a month)')
            ->assertSee('Confirmed after the working session.');
    }

    public function test_the_price_is_one_setting_and_follows_it_everywhere(): void
    {
        config(['company.proposal.price_from' => 80]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(Proposal::getUrl())
            ->assertSee('US$80')
            ->assertSee('pricing starts from US$80 a month')
            ->assertDontSee('US$50');
    }

    public function test_with_no_price_set_no_number_appears(): void
    {
        config(['company.proposal.price_from' => null]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(Proposal::getUrl())
            ->assertSee('Priced after the working session')
            ->assertSee('Pricing is confirmed with you after the working session.')
            ->assertDontSee('US$50')
            ->assertDontSee('starting price');
    }

    public function test_every_module_card_offers_the_trial(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(Proposal::getUrl())
            ->assertSeeText('30-day free trial', escape: false);

        $this->assertSame(
            6,
            substr_count((string) $this->actingAs(User::factory()->admin()->create())->get(Proposal::getUrl())->getContent(), '30-day free trial'),
        );
    }

    public function test_the_management_overview_links_to_the_proposal_without_repeating_the_paper_section(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(Management::getUrl())
            ->assertOk()
            ->assertSee('Read the full proposal')
            ->assertSee(Proposal::getUrl(), false)
            ->assertDontSee('If the government wants paper');
    }

    public function test_the_proposal_sits_under_the_management_menu_and_is_closed_to_non_admins(): void
    {
        $this->assertSame('Management', Proposal::getNavigationGroup());

        $this->actingAs(User::factory()->create())
            ->get(Proposal::getUrl())
            ->assertForbidden();
    }
}
