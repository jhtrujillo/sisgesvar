<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCruzamientoRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'crossings' => 'required|array',
            'crossings.*.madre' => 'required|string',
            'crossings.*.padres' => 'nullable|string',
            'crossings.*.observaciones' => 'nullable|string',
            'crossings.*.id_ponderados' => 'nullable|integer',
            'crossings.*.id_ponderado' => 'nullable|integer',
            'crossings.*.autofecundado' => 'nullable|integer',
        ];
    }
}
