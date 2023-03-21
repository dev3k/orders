<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderReceived;

class UpdateStockListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderReceived $event): void
    {
        $orderProducts = $event->order->orderProducts()->with('product.ingredients')->get();
        foreach ($orderProducts as $orderProduct) {
            foreach ($orderProduct->product->ingredients as $ingredient) {
                $consumptionSize = ($orderProduct->quantity * $ingredient->pivot->portion_size);
                $stockConsumed = $ingredient->stock_consumed + $consumptionSize;
                $ingredient->update([
                    'stock_available' => $ingredient->stock_available - $consumptionSize,
                    'stock_consumed' => $stockConsumed,
                ]);
            }
        }
    }
}
