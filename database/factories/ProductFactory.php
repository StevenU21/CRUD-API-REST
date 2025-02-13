<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 *  @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $name = fake()->unique()->sentence(2);

        return [
            'name' => $name,
            'description' => fake()->sentence(6),
            'price' => fake()->randomFloat(2, 1, 1000),
            'stock' => fake()->numberBetween(1, 100),
            'image' => null,
        ];
    }
}
