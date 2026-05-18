<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Modelo que se encarga de capturar de manera correcta los parametros que son enviados desde el formulario de "Alinemaineto de secuancias"
class Processes extends Model
{
    use HasFactory;
    protected $fillable = [
        'parametros',
    ];

    protected $casts = [
        'parametros' => 'array',
    ];
}
