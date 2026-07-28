<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluacions';

    protected $casts = [
        'arrayCharacters' => 'array'
    ];

    public function tipoEvaluacion()
    {
        return $this->belongsTo(TipoEvaluacion::class, 'tipo_evaluacion_id');
    }

    public function rangos()
    {
        return $this->hasMany(RangoEvaluacion::class, 'evaluacion_id');
    }

    /**
     * Obtiene la calificación correspondiente al valor evaluado en base a los rangos definidos.
     */
    public function obtenerCalificacion($valor): int
    {
        foreach ($this->rangos as $rango) {
            $minOk = is_null($rango->valor_minimo) || $valor >= $rango->valor_minimo;
            $maxOk = is_null($rango->valor_maximo) || $valor <= $rango->valor_maximo;

            if ($minOk && $maxOk) {
                return $rango->calificacion;
            }
        }

        return 0;
    }
}
