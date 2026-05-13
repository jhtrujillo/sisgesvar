<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteStandardizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['JEFE', 'ADMIN']);
    }

    public function rules(): array
    {
        return [
            'correcciones' => 'required|array',
            'correcciones.*.field' => 'required|string',
            'correcciones.*.valor_origen' => 'required|string',
            'correcciones.*.valor_destino' => 'required|string',
        ];
    }
}
