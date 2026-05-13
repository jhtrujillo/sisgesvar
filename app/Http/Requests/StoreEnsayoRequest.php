<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnsayoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'ambiente' => 'required|string|max:100',
        ];
    }
}
