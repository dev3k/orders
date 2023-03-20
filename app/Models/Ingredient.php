<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stock',
        'stock_consumed',
        'stock_available',
    ];

    protected $casts = [
        'stock' => 'integer',
        'stock_consumed' => 'integer',
        'stock_available' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, ProductIngredient::class)
            ->withPivot('portion_size');
    }
}
