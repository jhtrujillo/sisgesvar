<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCellEnsayoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Security is enforced inside EnsayoController via auth('api')->user()
    }

    public function rules(): array
    {
        return [
            'field' => 'required|string',
            'value' => 'nullable',
        ];
    }
}
