<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViveroParcela extends Model
{
    use HasFactory;

    protected $table = 'vivero_parcelas';

    protected $fillable = [
        'vivero_id',
        'numero_parcela',
        'numero_parcela_origen',
        'variedad_id',
        'id_plot_origen',
        'caracter_id',
    ];

    public function vivero()
    {
        return $this->belongsTo(Vivero::class, 'vivero_id');
    }

    public function variedad()
    {
        // Reference to the catalog of varieties
        return $this->belongsTo(Variety::class, 'variedad_id', 'id_nm_vrdad');
    }

    public function caracter()
    {
        return $this->belongsTo(ProyectoCaracter::class, 'caracter_id');
    }
}
