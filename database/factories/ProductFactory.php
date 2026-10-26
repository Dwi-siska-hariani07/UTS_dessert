<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Sesuai Slide 29 Pertemuan 5
     */
    public function definition()
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10000, 500000),
            'stock' => $this->faker->numberBetween(0, 100),
            'minimum_stock' => 5,
        ];
    }
}
