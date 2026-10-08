<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Promotions\PromotionResource;
use App\Filament\Resources\Vacancies\VacancyResource;
use App\Filament\Widgets\BranchChart;
use App\Filament\Widgets\OwnerSnapshot;
use App\Filament\Widgets\ProductChart;
use App\Filament\Widgets\SourceChart;
use App\LeadStage;
use App\Models\JobApplication;
use App\Models\Lead;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Vacancy;
use App\PromotionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOperationsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_dashboard_renders_with_no_data(): void
    {
        $this->get(Dashboard::getUrl())->assertOk();

        Livewire::test(OwnerSnapshot::class)->assertSee('Enquiries that became loans');

        foreach ([BranchChart::class, SourceChart::class, ProductChart::class] as $chart) {
            $this->assertFalse($chart::canView(), "{$chart} should hide itself with no leads");
        }
    }

    public function test_the_sidebar_shows_no_badges_when_nothing_needs_attention(): void
    {
        foreach ([LeadResource::class, JobApplicationResource::class, PromotionResource::class, VacancyResource::class] as $resource) {
            $this->assertNull($resource::getNavigationBadge(), "{$resource} should have no badge");
        }
    }

    public function test_leads_pulse_red_when_an_enquiry_is_overdue_and_amber_when_only_applications_are_quiet(): void
    {
        $quiet = Lead::factory()->create();
        $quiet->forceFill(['stage' => LeadStage::Application, 'updated_at' => now()->subDays(5)])->saveQuietly();

        $this->assertSame('1', LeadResource::getNavigationBadge());
        $this->assertSame('warning', LeadResource::getNavigationBadgeColor());
        $this->assertStringContainsString('quiet for 3 days or more', LeadResource::getNavigationBadgeTooltip());

        Lead::factory()->create(['created_at' => now()->subHours(5)]);

        $this->assertSame('2', LeadResource::getNavigationBadge());
        $this->assertSame('danger', LeadResource::getNavigationBadgeColor());
        $this->assertStringContainsString('waiting longer than 2 hours', LeadResource::getNavigationBadgeTooltip());
    }

    public function test_a_fresh_lead_is_counted_but_does_not_pulse(): void
    {
        Lead::factory()->create();

        $this->assertSame('1', LeadResource::getNavigationBadge());
        $this->assertSame('primary', LeadResource::getNavigationBadgeColor());
    }

    public function test_applications_and_promotions_flag_what_is_waiting_on_the_owner(): void
    {
        JobApplication::factory()->count(2)->create(['reviewed_at' => null]);
        JobApplication::factory()->create(['reviewed_at' => now()]);

        $this->assertSame('2', JobApplicationResource::getNavigationBadge());
        $this->assertSame('warning', JobApplicationResource::getNavigationBadgeColor());

        Promotion::factory()->create(['status' => PromotionStatus::AwaitingApproval]);
        Promotion::factory()->create(['status' => PromotionStatus::Approved, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(3)]);

        $this->assertSame('2', PromotionResource::getNavigationBadge());
        $this->assertSame('warning', PromotionResource::getNavigationBadgeColor());
        $this->assertStringContainsString('1 awaiting your sign-off', PromotionResource::getNavigationBadgeTooltip());
    }

    public function test_a_closing_role_is_flagged_only_while_it_is_short_of_applicants(): void
    {
        Vacancy::factory()->create(['title' => 'Branch Cashier', 'closes_on' => today()->addDays(2)]);
        $busy = Vacancy::factory()->create(['title' => 'Loan Officer', 'closes_on' => today()->addDays(2)]);
        JobApplication::factory()->count(3)->create(['vacancy_id' => $busy->id, 'reviewed_at' => now()]);

        $this->assertSame('1', VacancyResource::getNavigationBadge());
        $this->assertSame('warning', VacancyResource::getNavigationBadgeColor());
        $this->assertStringContainsString('Branch Cashier: 0 applied', VacancyResource::getNavigationBadgeTooltip());
        $this->assertStringNotContainsString('Loan Officer', VacancyResource::getNavigationBadgeTooltip());
    }

    public function test_snapshot_compares_with_the_previous_period(): void
    {
        Lead::factory()->count(2)->create();
        Lead::factory()->create(['created_at' => now()->subDays(40)]);

        Livewire::test(OwnerSnapshot::class)->assertSee('+100% vs the previous 30 days');
    }

    public function test_breakdown_says_which_branch_converts_better_once_there_is_enough_data(): void
    {
        Lead::factory()->count(5)->approved()->create(['branch' => 'Harare']);
        Lead::factory()->count(5)->create(['branch' => 'Bulawayo']);

        $this->assertTrue(BranchChart::canView());

        Livewire::test(BranchChart::class)
            ->assertSee('Harare turns 100% of enquiries into loans; Bulawayo only 0%.');
    }

    public function test_the_charts_plot_enquiries_and_approvals_per_group(): void
    {
        Lead::factory()->count(2)->create(['branch' => 'Harare', 'interest' => 'Salary-Based Loans']);
        Lead::factory()->approved()->create(['branch' => 'Harare', 'interest' => 'Salary-Based Loans']);

        $branches = Livewire::test(BranchChart::class)->instance();
        $data = (fn (): array => $this->getData())->call($branches);

        $this->assertSame(['Harare'], $data['labels']);
        $this->assertSame([3], $data['datasets'][0]['data']);
        $this->assertSame([1], $data['datasets'][1]['data']);

        $sources = Livewire::test(SourceChart::class)->instance();
        $this->assertSame(['Website'], (fn (): array => $this->getData()['labels'])->call($sources));
    }

    public function test_breakdown_does_not_compare_on_too_little_data(): void
    {
        Lead::factory()->approved()->create(['branch' => 'Harare']);
        Lead::factory()->create(['branch' => 'Bulawayo']);

        Livewire::test(BranchChart::class)->assertSee('Too few enquiries yet to compare branches fairly.');
    }
}
