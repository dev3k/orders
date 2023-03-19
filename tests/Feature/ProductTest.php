<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_cant_store_empty_order(): void
    {
        $response = $this->postJson('/order');

        $response->assertStatus(422);

        $response = $this->postJson('/order', ['products' => []]);

        $response->assertStatus(422);
    }

    public function test_it_cant_store_order_with_unknown_product(): void
    {
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => -1,
                    'quantity' => 1,
                ],
                [
                    'product_id' => 1,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_it_cant_store_zero_quantities_order(): void
    {
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 0,
                ],
                [
                    'product_id' => 2,
                    'quantity' => 0,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_it_can_store_order(): void
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

        $response->assertStatus(201);
    }
}
