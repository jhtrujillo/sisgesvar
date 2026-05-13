<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $fillable = ['categoria', 'valor', 'alias'];
}
