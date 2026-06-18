<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => fake()->unique()->words(2, true).' Cylinder',
            'category' => 'Cylinder',
            'sale_price' => fake()->numberBetween(500, 10000),
            'refill_charge' => fake()->numberBetween(200, 4000),
            'return_deposit' => fake()->numberBetween(100, 2000),
            'unit' => 'pcs',
            'qty' => fake()->numberBetween(0, 50),
            'min_qty' => 5,
            'max_qty' => 50,
        ];
    }
}
