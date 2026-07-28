<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;
    protected $table = 'evaluacions';
    protected $casts = [
        'arrayCharacters' => 'array',
    ];

    public function tipoEvaluacion()
    {
        return $this->belongsTo(TipoEvaluacion::class );
    }

    public function rangos()
    {
        return $this->hasMany(RangoEvaluacion::class);
    }

    public function obtenerCalificacion(float $porcentaje): int
    {

        $rango = $this->rangos
            ->first(function ($r) use ($porcentaje) {
                $minOk = is_null($r->valor_minimo) || $r->valor_minimo < $porcentaje;
                $maxOk = is_null($r->valor_maximo) || $r->valor_maximo >= $porcentaje;
                return $minOk && $maxOk;
            });

        return $rango ? (int) $rango->calificacion : 999;
    }
}
