<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderSaveException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            Order::createOrder($validated);

            return response()->json([], 201);
        } catch (OrderSaveException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
