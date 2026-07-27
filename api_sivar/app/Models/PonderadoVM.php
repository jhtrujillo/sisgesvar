<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PonderadoVM extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'ponderados_valor_merito';
    protected $primaryKey = 'id_ponderado';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'caracteristica_id','proyecto','nivel','ponderado','ambiente'
    ];

    protected $hidden = [
    ];
}
