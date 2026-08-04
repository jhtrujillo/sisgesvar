<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViveroCosecha extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'vivero_cosechas';

    protected $fillable = [
        'vivero_id',
        'fecha_cosecha',
        'nueva_fecha_siembra',
        'numero_corte_anterior',
        'ambiente',
    ];

    protected $casts = [
        'fecha_cosecha' => 'date',
        'nueva_fecha_siembra' => 'date',
    ];

    public function vivero()
    {
        return $this->belongsTo(Vivero::class);
    }
}
