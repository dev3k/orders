<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'products' => 'products',
            'products.*.product_id' => 'product',
            'products.*.quantity' => 'quantity',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array|min:1',
            //removed exist to reduce database queries, will check for it in checkProductUnavailability
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function withValidator(Validator $validator): void
    {
        if (! $validator->fails()) {
            $validator->after(function (Validator $validator) {
                $invalidProducts = $this->checkProductUnavailability($validator->validated());
                foreach ($invalidProducts as $index => $product) {
                    if (! $product) {
                        $validator->errors()->add('products.'.$index.'.product_id', 'Product is invalid');
                    } else {
                        $validator->errors()->add('products.'.$index.'.product_id', 'Product unavailable');
                    }
                }
            });
        }
    }

    protected function checkProductUnavailability(array $validated): array
    {
        $orderProducts = $validated['products'];
        $productModels = Product::with('ingredients')
            ->whereIn('id', collect($orderProducts)->pluck('product_id'))
            ->get()
            ->keyBy('id');
        $unavailableProducts = [];
        foreach ($orderProducts as $index => $orderProduct) {
            /* @var $productModel \App\Models\Product */
            $productModel = $productModels[$orderProduct['product_id']] ?? null;
            if (! $productModel) {
                $unavailableProducts[$index] = null;
                break;
            }
            foreach ($productModel->ingredients as $ingredient) {
                if ($ingredient->stock_available < $ingredient->pivot->portion_size * $orderProduct['quantity']) {
                    $unavailableProducts[$index] = $productModel->id;
                    break;
                }
            }
        }

        return $unavailableProducts;
    }
}
