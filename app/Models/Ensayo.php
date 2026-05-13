<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ensayo extends Model
{
    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = ['id'];

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
        return $this->belongsTo(User::class);
    }

    /**
     * Access all complementary files attached to this specific trial.
     */
    public function adjuntos()
    {
        return $this->hasMany(Adjunto::class, 'ensayo_id');
    }
}
