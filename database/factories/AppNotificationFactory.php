<?php

namespace Database\Factories;

use App\Models\AppNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppNotification>
 */
class AppNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'type' => fake()->randomElement(['low_stock', 'restock_request', 'restock_status']),
            'title' => fake()->sentence(4),
            'body' => fake()->optional()->sentence(),
            'link' => null,
            'read_at' => fake()->optional()->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
