<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViveroLoteHistorial extends Model
{
    use HasFactory;

    protected $table = 'vivero_lote_historial';

    protected $fillable = [
        'vivero_id',
        'lote_id',
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean'
    ];

    public function vivero()
    {
        return $this->belongsTo(Vivero::class, 'vivero_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
