<?php

namespace App\Observers;

use App\Events\Stock\LowStock;
use App\Models\Ingredient;
use Carbon\Carbon;

class IngredientObserver
{
    /**
     * Handle the Ingredient "created" event.
     */
    public function created(Ingredient $ingredient): void
    {
        //
    }

    /**
     * Handle the Ingredient "updated" event.
     */
    public function updated(Ingredient $ingredient): void
    {
        if ($ingredient->low_stock_notified_at === null) {
            if ($ingredient->stock_available <= ($ingredient->stock / 2)) {
                LowStock::dispatch($ingredient);
                $ingredient->updateQuietly([
                    'low_stock_notified_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Handle the Ingredient "deleted" event.
     */
    public function deleted(Ingredient $ingredient): void
    {
        //
    }

    /**
     * Handle the Ingredient "restored" event.
     */
    public function restored(Ingredient $ingredient): void
    {
        //
    }

    /**
     * Handle the Ingredient "force deleted" event.
     */
    public function forceDeleted(Ingredient $ingredient): void
    {
        //
    }
}
