<?php

declare(strict_types=1);

namespace App\Http\Requests\Metal;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('metals.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:metals,name'],
            'symbol' => ['required', 'string', 'max:5', 'unique:metals,symbol'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del metal es obligatorio.',
            'name.unique' => 'Ya existe un metal con ese nombre.',
            'symbol.required' => 'El simbolo quimico es obligatorio.',
            'symbol.unique' => 'Ya existe un metal con ese simbolo.',
        ];
    }
}
