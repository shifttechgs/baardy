<?php

namespace Database\Factories;

use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(3), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'location' => fake()->randomElement(['Harare', 'Bulawayo']),
            'employment_type' => 'Full-time',
            'summary' => fake()->sentence(10),
            'description' => fake()->paragraphs(2, true),
            'requirements' => null,
            'closes_on' => null,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }

    public function closed(): static
    {
        return $this->state(['closes_on' => today()->subDay()]);
    }
}
