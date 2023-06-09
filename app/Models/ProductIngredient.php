<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductIngredient extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'portion_size',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'ingredient_id' => 'integer',
        'portion_size' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
