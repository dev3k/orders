<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::factory()
            ->count(50)
            ->create();
        foreach ($products as $product) {
            $ingredients = Ingredient::all()->random(rand(0, 3))->pluck('id');
            foreach ($ingredients as $ingredient) {
                $product->ingredients()->attach($ingredient, ['quantity' => random_int(10, 300)]);
            }
        }
    }
}
