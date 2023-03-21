<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_order_cant_be_empty(): void
    {
        $response = $this->postJson('/order');

        $response->assertStatus(422);

        $response = $this->postJson('/order', ['products' => []]);

        $response->assertStatus(422);
    }

    public function test_order_cant_be_stored_with_unknown_product(): void
    {
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 1,
                ],
                [
                    'product_id' => 2,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_order_cant_be_stored_with_invalid_json(): void
    {
        $response = $this->postJson('/order', [
            'products_asd' => [
                [
                    'product_id_sd' => 1,
                    'quantity_asd' => 1,
                ],
                [
                    'product_id_asd' => 2,
                    'quantity_asd' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_order_cant_store_without_quantity(): void
    {
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => Product::factory()->create()->id,
                    'quantity' => 0,
                ],
            ],
        ]);

        $response->assertStatus(422);

        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => Product::factory()->create()->id,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }
}
