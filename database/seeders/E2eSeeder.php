<?php

namespace Database\Seeders;

use App\LeadStage;
use App\Models\JobApplication;
use App\Models\Lead;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * The fixed world the Playwright end-to-end suite runs against (tests/e2e).
 *
 * It is only ever run on the throwaway SQLite database that suite creates
 * (database/e2e.sqlite), never on a real one. The password for both staff
 * accounts comes from E2E_PASSWORD, which the suite generates fresh for each
 * run, so no credential lives in the repository.
 *
 * Every name and number here is known to the specs, so changing one means
 * changing them too.
 */
class E2eSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'e2e-admin@example.test';

    public const STAFF_EMAIL = 'e2e-user@example.test';

    public const PROMOTION_SLUG = 'e2e-back-to-school';

    public const PROMOTION_CODE = 'E2ETEST';

    public const ENDED_SLUG = 'e2e-ended-offer';

    public function run(): void
    {
        $password = env('E2E_PASSWORD') ?: throw new RuntimeException('E2E_PASSWORD is not set; run this through the Playwright suite.');

        User::factory()->admin()->create(['name' => 'E2E Admin', 'email' => self::ADMIN_EMAIL, 'password' => Hash::make($password)]);
        User::factory()->create(['name' => 'E2E Staff', 'email' => self::STAFF_EMAIL, 'password' => Hash::make($password)]);

        Vacancy::factory()->create(['title' => 'E2E Loan Officer', 'slug' => 'e2e-loan-officer', 'location' => 'Harare', 'summary' => 'Guide customers through loans.', 'requirements' => "Diploma in finance\nTwo years in customer service\nFluent in English", 'closes_on' => today()->addDays(30)]);
        Vacancy::factory()->create(['title' => 'E2E Branch Cashier', 'slug' => 'e2e-branch-cashier', 'location' => 'Bulawayo', 'summary' => 'Handle disbursements and repayments.', 'closes_on' => today()->addDays(20)]);
        Vacancy::factory()->draft()->create(['title' => 'E2E Hidden Draft', 'slug' => 'e2e-hidden-draft']);
        Vacancy::factory()->closed()->create(['title' => 'E2E Closed Role', 'slug' => 'e2e-closed-role']);

        Promotion::factory()->live()->placedIn(['banner', 'hero', 'menu', 'product'])->forProduct('Salary-Based Loans')->create([
            'title' => 'E2E Back to School',
            'slug' => self::PROMOTION_SLUG,
            'summary' => 'Term fees, spread across the term',
            'tracking_code' => self::PROMOTION_CODE,
            'image_path' => 'e2e/promo.webp',
            'image_alt' => 'A school fees poster',
            'ends_at' => now()->addDays(20),
        ]);

        Promotion::factory()->ended()->create([
            'title' => 'E2E Ended Offer',
            'slug' => self::ENDED_SLUG,
            'tracking_code' => 'E2EGONE',
            'cta_label' => 'Take it up now',
        ]);

        $this->leads();
        $this->application();
    }

    private function leads(): void
    {
        $waiting = Lead::factory()->create(['name' => 'E2E Waiting Lead', 'phone' => '+263 77 111 0001', 'branch' => 'Harare', 'interest' => 'Salary-Based Loans']);
        $waiting->forceFill(['created_at' => now()->subHours(5)])->saveQuietly();

        Lead::factory()->contacted()->create(['name' => 'E2E Contacted Lead', 'phone' => '+263 77 111 0002', 'branch' => 'Bulawayo']);
        Lead::factory()->approved()->create(['name' => 'E2E Approved Lead', 'phone' => '+263 77 111 0003']);
        Lead::factory()->lost()->create(['name' => 'E2E Lost Lead', 'phone' => '+263 77 111 0004']);

        $quiet = Lead::factory()->create(['name' => 'E2E Quiet Application', 'phone' => '+263 77 111 0005']);
        $quiet->forceFill(['stage' => LeadStage::Application, 'updated_at' => now()->subDays(6)])->saveQuietly();
    }

    private function application(): void
    {
        $role = Vacancy::query()->where('slug', 'e2e-loan-officer')->firstOrFail();
        $pdf = $this->samplePdf('E2E Applicant');

        JobApplication::factory()->create([
            'vacancy_id' => $role->id,
            'name' => 'E2E Applicant',
            'phone' => '+263 77 222 0001',
            'email' => 'e2e-applicant@example.test',
            'message' => 'I would like to join the team.',
            'cv_original_name' => 'e2e-applicant-cv.pdf',
            'cv_mime' => 'application/pdf',
            'cv_size' => strlen($pdf),
            'cv_data' => base64_encode($pdf),
            'reviewed_at' => null,
        ]);
    }

    /**
     * A one-page PDF, built by hand so the CV reader has something real to draw.
     */
    private function samplePdf(string $name): string
    {
        $stream = "BT\n/F1 22 Tf 1 0 0 1 56 760 Tm ({$name}) Tj\n/F1 11 Tf 1 0 0 1 56 730 Tm (End to end test CV) Tj\nET\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($stream)." >>\nstream\n{$stream}endstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $body) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$body}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf."trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";
    }
}
