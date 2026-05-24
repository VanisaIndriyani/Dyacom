<?php

namespace Database\Factories;

use App\Models\RestockRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestockRequest>
 */
class RestockRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'approved', 'rejected']);

        return [
            'product_id' => 1,
            'quantity' => fake()->numberBetween(1, 80),
            'note' => fake()->optional()->sentence(),
            'status' => $status,
            'requested_by' => 1,
            'decided_by' => $status === 'pending' ? null : 1,
            'decided_at' => $status === 'pending' ? null : fake()->dateTimeBetween('-14 days', 'now'),
            'decision_note' => $status === 'pending' ? null : fake()->optional()->sentence(),
        ];
    }
}
