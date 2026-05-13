<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmImportEnsayoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
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
