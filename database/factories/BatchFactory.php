<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'stock' => fake()->numberBetween(5, 50),
            'expiry_date' => now()->addDays(fake()->numberBetween(1, 30))->toDateString(),
            'status' => 'active',
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => now()->subDays(5)->toDateString(),
            'status' => 'expired',
        ]);
    }

    public function soldOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'status' => 'sold_out',
        ]);
    }
}
