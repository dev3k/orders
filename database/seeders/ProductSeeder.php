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
        $product = Product::factory()
            ->create([
                'name' => 'Burger',
            ]);

        $product->ingredients()->attach(Ingredient::where('name', 'beef')->first(), ['portion_size' => 150]);
        $product->ingredients()->attach(Ingredient::where('name', 'cheese')->first(), ['portion_size' => 30]);
        $product->ingredients()->attach(Ingredient::where('name', 'onion')->first(), ['portion_size' => 20]);
    }
}
