<?php

namespace Tests\Feature;

use App\Events\Order\OrderReceived;
use App\Events\Stock\LowStock;
use App\Mail\LowStockMail;
use App\Models\Ingredient;
use App\Models\Product;
use Database\Seeders\BurgerIngredientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected Product $burger;

    protected Ingredient $beef;

    protected Ingredient $cheese;

    protected Ingredient $onion;

    /**
     * Initializing Stock levels per challenge requests
     *
     * @var array|array[]
     */
    protected array $initStock = [
        'beef' => [
            'stock' => 20000,
            'stock_consumed' => 5000,
            'stock_available' => 15000,
        ],
        'cheese' => [
            'stock' => 5000,
            'stock_consumed' => 1000,
            'stock_available' => 4000,
        ],
        'onion' => [
            'stock' => 1000,
            'stock_consumed' => 400,
            'stock_available' => 600,
        ],
    ];

    /**
     * Default Portion Sizes for Burger, per challenge requests
     *
     * @var array|int[]
     */
    protected array $burgerPortionSizes = [
        'beef' => 150,
        'cheese' => 30,
        'onion' => 20,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Seeding initial ingredients with their initial Stock
        $this->seed(BurgerIngredientSeeder::class);

        // Creating Burger Product
        $this->burger = Product::factory()->create([
            'name' => 'Burger',
        ]);
        $this->beef = Ingredient::where('name', 'Beef')->first();
        $this->cheese = Ingredient::where('name', 'Cheese')->first();
        $this->onion = Ingredient::where('name', 'Onion')->first();

        $this->burger->ingredients()->attach($this->beef, [
            'portion_size' => $this->burgerPortionSizes['beef'],
        ]);
        $this->burger->ingredients()->attach($this->cheese, [
            'portion_size' => $this->burgerPortionSizes['cheese'],
        ]);
        $this->burger->ingredients()->attach($this->onion, [
            'portion_size' => $this->burgerPortionSizes['onion'],
        ]);
    }

    public function test_can_see_burger(): void
    {
        $this->assertDatabaseHas('products', [
            'name' => 'Burger',
        ]);
    }

    public function test_burger_has_ingredients()
    {
        // Checking portion sizes are correct
        $this->assertEquals(3, $this->burger->ingredients()->count());
        $this->assertEquals($this->burgerPortionSizes['beef'], $this->burger->ingredients()->where('name', 'Beef')->first()?->pivot->portion_size);
        $this->assertEquals($this->burgerPortionSizes['cheese'], $this->burger->ingredients()->where('name', 'Cheese')->first()?->pivot->portion_size);
        $this->assertEquals($this->burgerPortionSizes['onion'], $this->burger->ingredients()->where('name', 'Onion')->first()?->pivot->portion_size);
    }

    public function test_can_order_burger(): void
    {
        Event::fake([
            OrderReceived::class,
            LowStock::class,
        ]);
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => $this->burger->id,
                    'quantity' => 1,
                ],
            ],
        ]);
        $response->assertStatus(201);
        Event::assertDispatched(OrderReceived::class);
        Event::assertNotDispatched(LowStock::class);
    }

    public function test_burger_can_send_low_stock_email(): void
    {
        $mailable = new LowStockMail($this->beef);
        $this->assertEquals(15, $this->beef->stock_available_in_kg);
        $mailable->assertSeeInHtml('Ingredient');
        $mailable->assertSeeInHtml($this->beef->name);
        $mailable->assertSeeInHtml('Quantity left');
        $mailable->assertSeeInHtml($this->beef->stock_available_in_kg);
    }

    public function test_burger_can_dispatch_low_stock_notification(): void
    {
        Event::fake([
            LowStock::class,
        ]);
        // %50 of stock
        $this->beef->update([
            'stock_available' => $this->beef->stock / 2,
        ]);
        Event::assertDispatched(LowStock::class);
    }

    public function test_burger_cant_dispatch_low_stock_notification(): void
    {
        Event::fake([
            LowStock::class,
        ]);
        // above %50 of stock
        $this->beef->update([
            'stock_available' => $this->beef->stock / 2 + 1,
        ]);
        Event::assertNotDispatched(LowStock::class);
    }

    public function test_it_cant_dispatch_low_stock_notification_after_first_dispatch(): void
    {
        Event::fake([
            LowStock::class,
        ]);
        // below %50 of stock
        $this->beef->update([
            'stock_available' => $this->beef->stock / 2 - 1,
        ]);
        // below %50 of stock
        $this->beef->update([
            'stock_available' => $this->beef->stock / 2 - 1,
        ]);
        // below %50 of stock
        $this->beef->update([
            'stock_available' => $this->beef->stock / 2 - 2,
        ]);
        Event::assertDispatched(LowStock::class, 1);
    }

    public function test_burger_cant_store_order_with_product_above_quantity(): void
    {
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => $this->burger->id,
                    'quantity' => PHP_INT_MAX,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_burger_can_order_reduce_ingredients_available_stock()
    {
        $orderQuantity = random_int(1, 3);
        $response = $this->postJson('/order', [
            'products' => [
                [
                    'product_id' => $this->burger->id,
                    'quantity' => $orderQuantity,
                ],
            ],
        ]);
        $response->assertStatus(201);

        // checking stock levels after order
        foreach ($this->initStock as $ingredient => $stock) {
            /** @var \App\Models\Ingredient $model */
            $model = $this->{$ingredient};
            $model->refresh();
            $this->assertEquals($stock['stock'], $model->stock);
            $this->assertEquals($stock['stock_consumed'] + ($this->burger->ingredients()->where('name', ucfirst($ingredient))->first()->pivot->portion_size * $orderQuantity), $model->stock_consumed);
            $this->assertEquals($stock['stock_available'] - ($this->burger->ingredients()->where('name', ucfirst($ingredient))->first()->pivot->portion_size * $orderQuantity), $model->stock_available);
        }
    }
}
