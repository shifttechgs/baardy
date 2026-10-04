<?php

namespace Tests\Feature;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Filament\Resources\JobApplications\Pages\ViewJobApplication;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Filament\Resources\Vacancies\Pages\CreateVacancy;
use App\Filament\Resources\Vacancies\VacancyResource;
use App\Models\JobApplication;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Vacancy;
use App\PromotionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminListsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_applications_default_to_the_ones_still_to_review(): void
    {
        $waiting = JobApplication::factory()->create(['reviewed_at' => null]);
        $done = JobApplication::factory()->create(['reviewed_at' => now()]);

        Livewire::test(ListJobApplications::class)
            ->assertCanSeeTableRecords([$waiting])
            ->assertCanNotSeeTableRecords([$done])
            ->set('activeTab', 'reviewed')
            ->assertCanSeeTableRecords([$done])
            ->assertCanNotSeeTableRecords([$waiting]);
    }

    public function test_an_application_opens_on_its_own_page_with_its_cv_and_actions(): void
    {
        $vacancy = Vacancy::factory()->create(['title' => 'Loan Officer']);
        $application = JobApplication::factory()->create([
            'vacancy_id' => $vacancy->id,
            'name' => 'Nyasha Dube',
            'message' => 'I have five years in credit control.',
            'reviewed_at' => null,
        ]);

        $this->get(JobApplicationResource::getUrl('index'))->assertOk();

        $this->get(JobApplicationResource::getUrl('view', ['record' => $application]))
            ->assertOk()
            ->assertSee('Nyasha Dube')
            ->assertSee('Loan Officer')
            ->assertSee('I have five years in credit control.')
            ->assertSee('cv.pdf');

        Livewire::test(ViewJobApplication::class, ['record' => $application->getRouteKey()])
            ->assertActionVisible('markReviewed')
            ->callAction('markReviewed')
            ->assertActionHidden('markReviewed');

        $this->assertTrue($application->refresh()->isReviewed());
    }

    public function test_the_cv_can_be_downloaded_from_the_list(): void
    {
        $application = JobApplication::factory()->create();

        Livewire::test(ListJobApplications::class)
            ->callTableAction('downloadCv', $application)
            ->assertFileDownloaded('cv.pdf', '%PDF-1.4 test');
    }

    public function test_mark_reviewed_works_for_one_application_and_in_bulk(): void
    {
        $one = JobApplication::factory()->create(['reviewed_at' => null]);
        $others = JobApplication::factory()->count(2)->create(['reviewed_at' => null]);

        Livewire::test(ListJobApplications::class)
            ->callTableAction('markReviewed', $one)
            ->assertNotified()
            ->callTableBulkAction('markReviewed', $others);

        $this->assertSame(0, JobApplication::query()->whereNull('reviewed_at')->count());
    }

    public function test_a_vacancy_is_created_and_edited_on_its_own_pages(): void
    {
        $this->get(VacancyResource::getUrl('create'))->assertOk();

        Livewire::test(CreateVacancy::class)
            ->fillForm([
                'title' => 'Branch Manager',
                'location' => config('company.branches')[0]['name'],
                'employment_type' => 'Full-time',
                'summary' => 'Lead a branch team.',
                'description' => 'Run the branch day to day.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $vacancy = Vacancy::query()->where('title', 'Branch Manager')->firstOrFail();
        $this->assertSame('branch-manager', $vacancy->slug);

        $this->get(VacancyResource::getUrl('edit', ['record' => $vacancy]))->assertOk();
    }

    public function test_promotion_tabs_split_by_where_a_promotion_is_in_its_life(): void
    {
        $live = Promotion::factory()->create(['status' => PromotionStatus::Approved, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay()]);
        $awaiting = Promotion::factory()->create(['status' => PromotionStatus::AwaitingApproval]);
        $draft = Promotion::factory()->create(['status' => PromotionStatus::Draft]);

        Livewire::test(ListPromotions::class)
            ->assertCanSeeTableRecords([$live, $awaiting, $draft])
            ->set('activeTab', 'live')
            ->assertCanSeeTableRecords([$live])
            ->assertCanNotSeeTableRecords([$awaiting, $draft])
            ->set('activeTab', 'awaiting')
            ->assertCanSeeTableRecords([$awaiting])
            ->assertCanNotSeeTableRecords([$live, $draft]);
    }
}
