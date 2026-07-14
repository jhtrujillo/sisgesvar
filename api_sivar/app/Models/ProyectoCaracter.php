<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoCaracter extends Model
{
    use HasFactory;
    protected $table = 'proyecto_caracteres';
    protected $fillable = ['proyecto_id', 'nombre'];
}
