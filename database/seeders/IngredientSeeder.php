<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ingredient::factory()
            ->createMany([
                [
                    'name' => 'Beef',
                    'stock' => 20000,
                    'stock_consumed' => 5000,
                    'stock_available' => 15000,
                ],
                [
                    'name' => 'Cheese',
                    'stock' => 5000,
                    'stock_consumed' => 1000,
                    'stock_available' => 4000,
                ],
                [
                    'name' => 'Onion',
                    'stock' => 1000,
                    'stock_consumed' => 400,
                    'stock_available' => 600,
                ],
            ]);
    }
}
