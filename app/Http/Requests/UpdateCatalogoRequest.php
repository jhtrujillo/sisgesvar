<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['ADMIN', 'JEFE']);
    }

    public function rules(): array
    {
        return [
            'valor' => 'required|string|max:255',
        ];
    }
}
