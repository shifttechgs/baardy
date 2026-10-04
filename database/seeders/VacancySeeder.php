<?php

namespace Database\Seeders;

use App\Models\Vacancy;
use Illuminate\Database\Seeder;

/**
 * Example vacancies, for seeing the careers page with open roles.
 *
 * These are placeholders, not real openings: run it on a local or staging
 * database only, and unpublish or delete them in the admin before launch.
 * Safe to run again: each role is matched on its slug.
 */
class VacancySeeder extends Seeder
{
    /**
     * Seed the example vacancies.
     */
    public function run(): void
    {
        $roles = [
            [
                'title' => 'Loan Officer',
                'location' => 'Harare',
                'employment_type' => 'Full-time',
                'summary' => 'Guide customers through salary-based and small-business loans.',
                'description' => "You will meet customers in person, explain every cost in writing and prepare applications for review.\nYou will work with a small team at our Harare branch.",
                'requirements' => "Diploma in banking, finance or business\n2+ years in customer-facing financial services\nFluent in English and Shona",
                'closes_on' => today()->addDays(30),
            ],
            [
                'title' => 'Branch Cashier',
                'location' => 'Bulawayo',
                'employment_type' => 'Full-time',
                'summary' => 'Handle disbursements and repayments with care and accuracy.',
                'description' => 'You will process the day\'s transactions, issue receipts and balance the till at our Bulawayo branch.',
                'requirements' => "Accounting qualification\nHonesty and attention to detail\nFluent in English and Ndebele",
                'closes_on' => null,
            ],
            [
                'title' => 'Collections Assistant',
                'location' => 'Harare',
                'employment_type' => 'Contract',
                'summary' => 'Follow up on repayments politely and professionally.',
                'description' => 'You will keep customers on track with their repayments and record every conversation.',
                'requirements' => "Experience in collections or customer service\nGood phone manner",
                'closes_on' => today()->addDays(14),
            ],
        ];

        foreach ($roles as $role) {
            Vacancy::updateOrCreate(
                ['slug' => str($role['title'])->slug()->toString()],
                $role + ['is_published' => true],
            );
        }
    }
}
