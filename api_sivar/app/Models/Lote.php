<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lotes';

    protected $fillable = [
        'ingenio_codigo',
        'hacienda_codigo',
        'nombre_lote',
        'capacidad_maxima',
        'total_parcelas_vivero'
    ];

    public function viveros()
    {
        return $this->hasMany(Vivero::class, 'lote_id');
    }

    public function historial()
    {
        return $this->hasMany(ViveroLoteHistorial::class, 'lote_id');
    }
}
