<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['ADMIN', 'JEFE']);
    }

    public function rules(): array
    {
        return [
            'source_id' => 'required|exists:catalogos,id',
            'target_id' => 'required|exists:catalogos,id|different:source_id',
        ];
    }
}
