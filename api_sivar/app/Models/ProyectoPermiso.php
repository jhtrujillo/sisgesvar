<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoPermiso extends Model
{
    use HasFactory;

    protected $connection = 'sivar';
    protected $table = 'proyecto_permisos';

    protected $fillable = [
        'proyecto_id',
        'usuario_id',
        'rol',
        'asignado_por'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id', 'id_prycto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usrio');
    }

    public function asignador()
    {
        return $this->belongsTo(User::class, 'asignado_por', 'id_usrio');
    }
}
