<?php

namespace Database\Factories;

use App\Models\Promotion;
use App\PromotionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * A draft running from yesterday to next month, on no placement.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(6),
            'body' => fake()->paragraphs(2, true),
            'terms' => 'Subject to application, affordability assessment and approval. '.fake()->sentence(),
            'product' => null,
            'cta_label' => 'Visit a branch',
            'tracking_code' => Str::upper(Str::random(8)),
            'show_in_banner' => false,
            'show_in_hero' => false,
            'show_in_menu' => false,
            'show_on_product' => false,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ];
    }

    /**
     * Approved and running now.
     */
    public function live(): static
    {
        return $this->afterMaking(function (Promotion $promotion): void {
            $promotion->status = PromotionStatus::Approved;
            $promotion->approved_at = now();
        });
    }

    public function awaitingApproval(): static
    {
        return $this->afterMaking(fn (Promotion $promotion) => $promotion->status = PromotionStatus::AwaitingApproval);
    }

    /**
     * Approved, but its end date has passed.
     */
    public function ended(): static
    {
        return $this->live()->state(fn (): array => [
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subDay(),
        ]);
    }

    /**
     * Approved, but not started yet.
     */
    public function scheduled(): static
    {
        return $this->live()->state(fn (): array => [
            'starts_at' => now()->addWeek(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    /**
     * @param  list<string>  $placements  any of 'banner', 'hero', 'menu', 'product'
     */
    public function placedIn(array $placements): static
    {
        return $this->state(fn (): array => collect($placements)
            ->mapWithKeys(fn (string $placement): array => [Promotion::PLACEMENTS[$placement] => true])
            ->all());
    }

    public function forProduct(string $product): static
    {
        return $this->state(fn (): array => ['product' => $product]);
    }
}
