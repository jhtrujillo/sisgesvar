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

    protected $appends = ['cortes'];

    public function getCortesAttribute()
    {
        $vivero = $this->vivero;
        if (!$vivero) {
            return [];
        }

        $parcelLabel = $this->numero_parcela_origen ?: $this->numero_parcela;
        $plotId = $vivero->identificador_unico . '-' . $parcelLabel;

        return Vivero::where(function($query) use ($plotId) {
                $query->where('origen_parcela', $plotId)
                      ->orWhere('origen_parcela', 'like', $plotId . '-%');
            })
            ->select('id', 'identificador_unico', 'nombre', 'fecha_siembra')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'identificador_unico' => $item->identificador_unico,
                    'nombre' => $item->nombre,
                    'fecha_siembra' => $item->fecha_siembra ? $item->fecha_siembra->format('Y-m-d') : null,
                    'consecutivo_corte' => $item->consecutivo_corte
                ];
            });
    }

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
