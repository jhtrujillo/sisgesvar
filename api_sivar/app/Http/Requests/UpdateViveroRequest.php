<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateViveroRequest extends FormRequest
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
            'proyecto_id' => 'nullable|integer|exists:sivar.remote_pg_sipro,id_prycto',
            'ingenio' => 'nullable|string',
            'hacienda' => 'nullable|string',
            'nombre' => 'nullable|string|max:255',
            'fecha_siembra' => 'nullable|date',
            'origen_ingenio' => 'nullable|string',
            'origen_hacienda' => 'nullable|string',
            'origen_suerte' => 'nullable|string',
            'origen_anio' => 'nullable|integer',
            'origen_parcela' => 'nullable|string',
            'origen_lote_id' => 'nullable|integer|exists:lotes,id',
            'origen_vivero_id' => 'nullable|integer|exists:viveros,id',
            'lote_id' => 'nullable|integer|exists:lotes,id',
            'consecutivo_vivero_ingenio' => 'nullable|integer',
            'estado' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'ambiente' => 'nullable|integer',
            'responsable_id' => 'nullable|integer',
            'numero_corte' => 'nullable|integer',
            'temporada_floracion' => 'nullable|string',
            'condicion' => 'nullable|string',
            'caracter_id' => 'nullable|integer',
            'es_corte' => 'nullable|boolean',
        ];
    }
}
