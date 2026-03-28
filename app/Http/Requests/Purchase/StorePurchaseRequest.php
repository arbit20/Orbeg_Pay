<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('purchases.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'purchase_datetime' => ['required', 'date'],
            'weight_grams' => ['required', 'numeric', 'min:0.0001'],
            'lot_count' => ['required', 'integer', 'min:1'],
            'purity' => ['required', 'numeric', 'min:0', 'max:1'],
            'unit_price_bs' => ['required', 'numeric', 'min:0.01'],
            'total_bs' => ['required', 'numeric', 'min:0.01'],
            'reference_quote_usd_oz' => ['nullable', 'numeric', 'min:0'],
            'quote_source' => ['nullable', 'string', 'max:50'],
            'exchange_rate_bs_usd' => ['nullable', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'evidence' => ['nullable', 'image', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_datetime.required' => 'La fecha y hora son obligatorias.',
            'weight_grams.required' => 'La cantidad en gramos es obligatoria.',
            'weight_grams.min' => 'La cantidad debe ser mayor a 0.',
            'lot_count.required' => 'El numero de lotes es obligatorio.',
            'lot_count.min' => 'Debe haber al menos 1 lote.',
            'purity.required' => 'La ley es obligatoria.',
            'purity.max' => 'La ley no puede ser mayor a 1 (100%).',
            'unit_price_bs.required' => 'El precio unitario es obligatorio.',
            'total_bs.required' => 'El total es obligatorio.',
            'evidence.image' => 'La evidencia debe ser una imagen.',
            'evidence.max' => 'La imagen no debe superar 5MB.',
        ];
    }
}
