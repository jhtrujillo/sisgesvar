<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'area',
        'consumible',
        'actividad',
        'codigo_cg1',
        'descripcion_item',
        'marca',
        'unidad',
        'cantidad_en_stock',
        'cantidad_critica',
        'ubicacion',
        'solicitante',
        'fecha_solicitud',
        'fecha_ultima_revision',
        'observaciones'
    ];

    protected $appends = ['condicion'];

    public function getCondicionAttribute()
    {
        return $this->cantidad_en_stock - $this->cantidad_critica;
    }

    public function movements()
    {
        return $this->hasMany(LabInventoryMovement::class);
    }
}
