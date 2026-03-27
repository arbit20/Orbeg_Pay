<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('suppliers.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'supplier_type' => ['required', Rule::in(['empresa', 'persona'])],
            'document_type' => ['required', Rule::in(['NIT', 'CI', 'LICENCIA', 'PASAPORTE'])],
            'document_number' => ['required', 'string', 'max:20', 'unique:suppliers,document_number'],
            'business_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['exclude_if:supplier_type,persona', 'nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'contact_person' => ['exclude_if:supplier_type,persona', 'nullable', 'string', 'max:150'],
            'contact_phone' => ['exclude_if:supplier_type,persona', 'nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:30'],
            'bank_cci' => ['nullable', 'string', 'max:30'],
            'payment_method' => ['required', Rule::in(['efectivo', 'transferencia', 'cheque', 'qr', 'otro'])],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_type.required' => 'El tipo de proveedor es obligatorio.',
            'supplier_type.in' => 'El tipo de proveedor no es valido.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento no es valido.',
            'document_number.required' => 'El numero de documento es obligatorio.',
            'document_number.unique' => 'Ya existe un proveedor con ese numero de documento.',
            'business_name.required' => 'La razon social es obligatoria.',
            'email.email' => 'El correo electronico no es valido.',
            'city.required' => 'La ciudad es obligatoria.',
            'country.required' => 'El pais es obligatorio.',
            'payment_method.required' => 'El metodo de pago es obligatorio.',
            'payment_method.in' => 'El metodo de pago no es valido.',
        ];
    }
}
