<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaracterizacionBancoGermoplasma  extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'caracterizacion_banco_germoplasma';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ensayo'
    ];

    protected $hidden = [
    ];
}
