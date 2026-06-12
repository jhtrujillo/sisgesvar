<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $connection = 'sivar';
    protected $fillable = ['categoria', 'valor', 'alias'];
}
