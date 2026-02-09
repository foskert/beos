<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 2000);
        return [
            'name'               => fake()->unique()->userName() . ' ' . fake()->word(),
            'description'        => fake()->paragraphs(2, true),
            'price'              => $price,
            'currency_id'        => Currency::factory(),
            'tax_cost'           => $price * 0.15,
            'manufacturing_cost' => fake()->randomFloat(2, 10, $price * 0.5),
        ];
    }
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 5000, 10000),
        ]);
    }
}
