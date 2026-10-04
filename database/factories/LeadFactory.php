<?php

namespace Database\Factories;

use App\LeadLostReason;
use App\LeadStage;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName().' '.fake()->lastName(),
            'phone' => '+263 77 '.fake()->unique()->numerify('### ####'),
            'email' => fake()->optional()->safeEmail(),
            'interest' => fake()->randomElement(collect(config('marketing.products'))->pluck('name')->all()),
            'branch' => fake()->randomElement(array_column(config('company.branches'), 'name')),
            'message' => fake()->optional()->sentence(),
        ];
    }

    public function contacted(): static
    {
        return $this->afterMaking(function (Lead $lead): void {
            $lead->stage = LeadStage::Contacted;
            $lead->first_contacted_at = now();
        });
    }

    public function lost(LeadLostReason $reason = LeadLostReason::NotInterested): static
    {
        return $this->afterMaking(function (Lead $lead) use ($reason): void {
            $lead->stage = LeadStage::Lost;
            $lead->lost_reason = $reason;
            $lead->first_contacted_at = now();
            $lead->closed_at = now();
        });
    }

    public function approved(): static
    {
        return $this->afterMaking(function (Lead $lead): void {
            $lead->stage = LeadStage::Approved;
            $lead->first_contacted_at = now();
            $lead->closed_at = now();
        });
    }
}
