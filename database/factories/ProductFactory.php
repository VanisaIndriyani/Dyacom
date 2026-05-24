<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => null,
            'name' => Str::title(fake()->words(fake()->numberBetween(2, 4), true)),
            'category' => fake()->randomElement(['Kertas', 'Tinta', 'ATK', 'Plastik', 'Lainnya']),
            'unit' => fake()->randomElement(['pcs', 'rim', 'box', 'pack']),
            'stock' => fake()->numberBetween(0, 120),
            'min_stock' => fake()->numberBetween(5, 20),
            'price' => fake()->randomFloat(2, 1000, 75000),
            'description' => fake()->sentence(),
        ];
    }
}
