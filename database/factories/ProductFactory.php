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

        $originalImageUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRwm0rdbOAslibv0mLIxWKZ6C6r9m8fujTIBA&s';

        $imageContents = file_get_contents($originalImageUrl);

        $imageName = uniqid() . '.jpg';

        Storage::disk('public')->put('products_images/' . $imageName, $imageContents);

        $imagePath = 'products_images/' . $imageName;

        return [
            'name' => $name,
            'description' => fake()->sentence(6),
            'price' => fake()->randomFloat(2, 1, 1000),
            'stock' => fake()->numberBetween(1, 100),
            'image' => $imagePath,
        ];
    }
}
