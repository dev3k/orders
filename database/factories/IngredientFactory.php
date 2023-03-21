<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //$stock = fake()->randomNumber(3);
        $stock = $this->faker->numberBetween($min = 1000, $max = 5000);
        $stock_consumed = $this->faker->numberBetween(0, $min);

        return [
            'name' => fake()->words(2, true),
            'stock' => $stock,
            'stock_consumed' => $stock_consumed,
            'stock_available' => $stock - $stock_consumed,
        ];
    }
}
