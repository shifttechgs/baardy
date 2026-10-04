<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_reads_a_pdf_cv_inside_the_browser(): void
    {
        $application = JobApplication::factory()->create([
            'cv_original_name' => 'My CV.pdf',
            'cv_mime' => 'application/pdf',
            'cv_data' => base64_encode('%PDF-1.4 hello'),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('applications.cv', $application))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Disposition', 'inline; filename="My CV.pdf"')
            ->assertSee('%PDF-1.4 hello', false);
    }

    public function test_a_word_cv_is_sent_as_a_download_not_shown(): void
    {
        $application = JobApplication::factory()->create([
            'cv_original_name' => 'cv.docx',
            'cv_mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'cv_data' => base64_encode('PK word file'),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('applications.cv', $application))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/octet-stream')
            ->assertHeader('Content-Disposition', 'attachment; filename="cv.docx"');
    }

    public function test_a_file_that_only_claims_to_be_a_pdf_is_never_shown_inline(): void
    {
        $application = JobApplication::factory()->create([
            'cv_original_name' => 'cv.pdf',
            'cv_mime' => 'application/pdf',
            'cv_data' => base64_encode('<html><script>alert(1)</script></html>'),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('applications.cv', $application))
            ->assertHeader('Content-Type', 'application/octet-stream')
            ->assertHeader('Content-Disposition', 'attachment; filename="cv.pdf"');
    }

    public function test_visitors_and_non_admins_get_nothing(): void
    {
        $application = JobApplication::factory()->create();

        $this->get(route('applications.cv', $application))->assertNotFound();

        $this->actingAs(User::factory()->create())
            ->get(route('applications.cv', $application))
            ->assertNotFound();
    }
}
