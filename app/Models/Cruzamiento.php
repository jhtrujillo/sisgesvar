<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cruzamiento extends Model
{
    protected $fillable = [
        'codigo',
        'madre',
        'padre',
        'tipo_cruzamiento',
        'fecha',
        'observaciones',
    ];
}
