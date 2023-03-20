<?php

namespace App\Models;

use App\Exceptions\OrderSaveException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public function products(): hasManyThrough
    {
        return $this->hasManyThrough(Product::class, OrderProduct::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public static function createOrder(array $orderDetails)
    {
        try {
            // Use a database transaction to ensure atomicity
            DB::beginTransaction();

            $order = new Order();
            $order->save();
            $records = [];
            foreach ($orderDetails['products'] as $orderProduct) {
                $records[] = [
                    'order_id' => $order->id,
                    'product_id' => $orderProduct['product_id'],
                    'quantity' => $orderProduct['quantity'],
                ];
            }
            $order->orderProducts()->createMany($records);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $exception) {
            // Roll back the transaction if an error occurs
            DB::rollBack();
            throw new OrderSaveException('Cant save order');
        }
    }
}
