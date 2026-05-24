<?php

namespace Database\Factories;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => 1,
            'supplier_id' => null,
            'user_id' => 1,
            'restock_request_id' => null,
            'type' => fake()->randomElement(['in', 'out']),
            'quantity' => fake()->numberBetween(1, 30),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
