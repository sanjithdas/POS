<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
/**
 *
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition(): array
    {
        if (!file_exists(public_path('storage/images'))) {
            mkdir(public_path('storage/images'), 0777, true);
        }

        $imagePath = $this->faker->image(public_path('storage/images'), 640, 480, null, false);
        \Log::info('Generated image path: ' . $imagePath);

        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence(),
            'stock' => $this->faker->numberBetween(1, 100),
            'image' => url('storage/images/' . $imagePath),
        ];
    }
}
