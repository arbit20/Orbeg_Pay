<?php

declare(strict_types=1);

namespace App\Http\Requests\MetalPrice;

use App\Constants\MetalUnits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMetalPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('metal_prices.create');
    }

    public function rules(): array
    {
        $metalId = $this->route('metal')->id;

        return [
            'price_per_gram' => ['required', 'numeric', 'min:0.0001', 'max:9999999999.9999'],
            'currency' => ['required', 'string', 'size:3'],
            'effective_date' => [
                'required',
                'date',
                Rule::unique('metal_prices')->where(function ($query) use ($metalId) {
                    return $query->where('metal_id', $metalId)
                        ->where('currency', $this->input('currency', 'USD'));
                }),
            ],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Calcula automaticamente el precio por onza troy a partir del precio por gramo.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->hasAny(['price_per_gram'])) {
                $pricePerGram = (string) $this->input('price_per_gram');
                $pricePerTroyOunce = bcmul($pricePerGram, MetalUnits::TROY_OUNCE_IN_GRAMS, MetalUnits::FINANCIAL_SCALE);
                $this->merge(['price_per_troy_ounce' => $pricePerTroyOunce]);
            }
        });
    }

    public function messages(): array
    {
        return [
            'price_per_gram.required' => 'El precio por gramo es obligatorio.',
            'price_per_gram.min' => 'El precio por gramo debe ser mayor a cero.',
            'effective_date.required' => 'La fecha de vigencia es obligatoria.',
            'effective_date.unique' => 'Ya existe un precio para este metal, moneda y fecha.',
        ];
    }
}
