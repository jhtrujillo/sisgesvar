<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['ADMIN', 'JEFE']);
    }

    public function rules(): array
    {
        return [
            'categoria' => 'required|string|in:PROYECTO,INGENIO,AMBIENTE',
            'valor' => 'required|string|max:255',
        ];
    }
}
