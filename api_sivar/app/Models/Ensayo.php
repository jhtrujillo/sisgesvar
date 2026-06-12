<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ensayo extends Model
{
    protected $connection = 'sivar';
    protected $fillable = [
        'nombre_ensayo', 'nombre_env', 'proyecto', 'estado_seleccion', 
        'serie', 'amb_seleccion', 'amb_evaluacion', 'objetivo', 
        'ingenio', 'hacienda', 'suerte', 'zona_agroecologia', 'consociacion', 
        'corte', 'entradas', 'testigos', 'clones', 'total_parcelas', 
        'diseno', 'surcos', 'longitud_surco', 'longitud_callejon', 
        'distancia_surco', 'area_total', 'red_meteorologica', 
        'fecha_siembra', 'fecha_cosecha_final', 'fecha_evaluacion', 
        'meses_evaluacion', 'fecha_cosecha_programada', 'estado_actual', 
        'ano_siembra', 'mes_siembra', 'tipo_cosecha', 'comentarios', 
        'ubicacion_fisica', 'nombre_reporte', 'investigador', 'user_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fecha_siembra' => 'date',
        'fecha_cosecha_final' => 'date',
        'fecha_evaluacion' => 'date',
        'fecha_cosecha_programada' => 'date',
        'entradas' => 'integer',
        'testigos' => 'integer',
        'clones' => 'integer',
        'total_parcelas' => 'integer',
        'surcos' => 'integer',
        'longitud_surco' => 'float',
        'longitud_callejon' => 'float',
        'distancia_surco' => 'float',
        'area_total' => 'float',
    ];

    /**
     * Get the user that owns the record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usrio');
    }

    /**
     * Access all complementary files attached to this specific trial.
     */
    public function adjuntos()
    {
        return $this->hasMany(Adjunto::class, 'ensayo_id');
    }
}
