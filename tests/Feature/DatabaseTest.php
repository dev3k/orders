<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_can_be_instantiated(): void
    {
        Ingredient::factory()->count(5)->create();
        $this->assertDatabaseCount('ingredients', 5);

        Product::factory()->count(5)->create();
        $this->assertDatabaseCount('products', 5);

        Order::factory()->count(5)->create();
        $this->assertDatabaseCount('orders', 5);

        $product = Product::factory()
            ->create();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $product->ingredients);
        $product->ingredients()->attach(Ingredient::factory()->create(), ['portion_size' => fake()->randomNumber()]);
        $product->ingredients()->attach(Ingredient::factory()->create(), ['portion_size' => fake()->randomNumber()]);
        $product->ingredients()->attach(Ingredient::factory()->create(), ['portion_size' => fake()->randomNumber()]);
        $this->assertDatabaseCount('products', 6);
        $this->assertDatabaseCount('product_ingredient', 3);

        $order = Order::factory()->create();
        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseCount('order_products', 1);
    }

    public function test_models_can_be_deleted()
    {
        $ingredient = Ingredient::factory()->create();
        $ingredient->delete();
        $this->assertModelMissing($ingredient);

        $product = Product::factory()
            ->create();
        $product->ingredients()->attach(Ingredient::factory()->create(), ['portion_size' => fake()->randomNumber()]);
        $product->ingredients()->attach(Ingredient::factory()->create(), ['portion_size' => fake()->randomNumber()]);
        $product->ingredients()->attach(Ingredient::factory()->create(), ['portion_size' => fake()->randomNumber()]);
        $product->delete();
        $this->assertModelMissing($product);
        $this->assertDatabaseCount('product_ingredient', 0);
        $order = Order::factory()->create();
        $order->delete();
        $this->assertModelMissing($order);

        $product = Product::factory()
            ->create();
        $order = Order::factory()->create();
        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $order->delete();
        $this->assertDatabaseCount('order_products', 0);
    }
}
