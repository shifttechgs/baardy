<?php

namespace Tests\Feature;

use App\Mail\JobApplicationReceived;
use App\Models\JobApplication;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CareersPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_links_to_careers(): void
    {
        $this->get(route('home'))->assertOk()->assertSee(route('careers'), false);
    }

    public function test_page_lists_only_open_vacancies(): void
    {
        Vacancy::factory()->create(['title' => 'Loan Officer']);
        Vacancy::factory()->draft()->create(['title' => 'Hidden Draft Role']);
        Vacancy::factory()->closed()->create(['title' => 'Expired Role']);

        $this->get(route('careers'))
            ->assertOk()
            ->assertSee('Loan Officer')
            ->assertDontSee('Hidden Draft Role')
            ->assertDontSee('Expired Role');
    }

    public function test_page_still_takes_applications_with_no_vacancies(): void
    {
        $this->get(route('careers'))
            ->assertOk()
            ->assertSee('No roles are open right now')
            ->assertSee('Apply with your CV');
    }

    public function test_application_stores_the_cv_in_the_database_and_emails_it(): void
    {
        Mail::fake();
        $vacancy = Vacancy::factory()->create();
        $cv = UploadedFile::fake()->createWithContent('my-cv.pdf', '%PDF-1.4 hello');

        $this->post(route('careers.apply'), [
            'vacancy_id' => $vacancy->id,
            'name' => 'Tendai Moyo',
            'phone' => '+263771234567',
            'cv' => $cv,
        ])->assertRedirect(route('careers').'#apply')->assertSessionHas('application_sent');

        $application = JobApplication::query()->firstOrFail();
        $this->assertSame($vacancy->id, $application->vacancy_id);
        $this->assertSame('my-cv.pdf', $application->cv_original_name);
        $this->assertSame('%PDF-1.4 hello', $application->cvContents());

        Mail::assertSent(JobApplicationReceived::class, function (JobApplicationReceived $mail): bool {
            $this->assertTrue($mail->hasTo('vacancies@baardymicrocapital.com'));
            $mail->assertHasAttachedData('%PDF-1.4 hello', 'my-cv.pdf', ['mime' => 'application/pdf']);

            return true;
        });
    }

    public function test_cv_is_required_and_must_be_a_document(): void
    {
        $base = ['name' => 'Tendai Moyo', 'phone' => '+263771234567'];

        $this->post(route('careers.apply'), $base)->assertSessionHasErrors('cv');
        $this->post(route('careers.apply'), $base + ['cv' => UploadedFile::fake()->create('cv.exe', 10)])->assertSessionHasErrors('cv');
        $this->post(route('careers.apply'), $base + ['cv' => UploadedFile::fake()->create('cv.pdf', 6000, 'application/pdf')])->assertSessionHasErrors('cv');

        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_cannot_apply_to_a_closed_vacancy(): void
    {
        $vacancy = Vacancy::factory()->closed()->create();

        $this->post(route('careers.apply'), [
            'vacancy_id' => $vacancy->id,
            'name' => 'Tendai Moyo',
            'phone' => '+263771234567',
            'cv' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('vacancy_id');
    }

    public function test_honeypot_submissions_are_discarded_quietly(): void
    {
        Mail::fake();

        $this->post(route('careers.apply'), [
            'name' => 'Bot',
            'phone' => '1',
            'website' => 'http://spam.example',
            'cv' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'),
        ])->assertSessionHas('application_sent');

        $this->assertDatabaseCount('job_applications', 0);
        Mail::assertNothingSent();
    }
}
