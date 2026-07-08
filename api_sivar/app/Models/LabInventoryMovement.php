<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabInventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_inventory_id',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'user_id',
        'user_name',
        'observaciones'
    ];

    public function labInventory()
    {
        return $this->belongsTo(LabInventory::class);
    }
}
