<?php

namespace Database\Factories;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vacancy_id' => null,
            'name' => fake()->name(),
            'phone' => '+263 77 '.fake()->numerify('### ####'),
            'email' => fake()->safeEmail(),
            'message' => null,
            'cv_original_name' => 'cv.pdf',
            'cv_mime' => 'application/pdf',
            'cv_size' => 13,
            'cv_data' => base64_encode('%PDF-1.4 test'),
        ];
    }
}
