<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangoEvaluacion extends Model
{
    use HasFactory;
    protected $table = "rango_evaluacions";

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }
}
