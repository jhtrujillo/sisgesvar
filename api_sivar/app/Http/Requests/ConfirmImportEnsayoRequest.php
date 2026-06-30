<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmImportEnsayoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Security is enforced inside EnsayoController via auth('api')->user()
    }

    public function rules(): array
    {
        return [
            'tempPath' => 'required|string',
            'ambiente' => 'required|string',
            'mappings' => 'required|array',
        ];
    }
}
