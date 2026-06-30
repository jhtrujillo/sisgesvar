<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnsayoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Security is enforced inside EnsayoController via auth('api')->user()
    }

    public function rules(): array
    {
        return [
            // Use mimetypes (not mimes) to match the actual MIME type browsers send for xlsx/xls/csv files
            'file' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowed = ['xlsx', 'xls', 'csv'];
                    if (!in_array($extension, $allowed)) {
                        $fail('El archivo debe ser Excel (.xlsx, .xls) o CSV (.csv).');
                    }
                }
            ],
            'ambiente' => 'required|string|max:100',
        ];
    }

}
