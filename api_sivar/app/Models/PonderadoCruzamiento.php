<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PonderadoCruzamiento extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'ponderados_cruzamiento';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'caracteristica_id','id_ponderado','nivel','ponderado'
    ];

    protected $hidden = [
    ];
}
