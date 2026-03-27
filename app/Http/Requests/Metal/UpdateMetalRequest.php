<?php

declare(strict_types=1);

namespace App\Http\Requests\Metal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMetalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('metals.edit');
    }

    public function rules(): array
    {
        $metalId = $this->route('metal')->id;

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('metals', 'name')->ignore($metalId)],
            'symbol' => ['required', 'string', 'max:5', Rule::unique('metals', 'symbol')->ignore($metalId)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
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
