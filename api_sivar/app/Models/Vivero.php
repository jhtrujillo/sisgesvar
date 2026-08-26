<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vivero extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'viveros';

    protected $fillable = [
        'identificador_unico',
        'nombre',
        'ingenio',
        'hacienda',
        'suerte',
        'proyecto_id',
        'ambiente',
        'responsable_id',
        'fecha_siembra',
        'numero_corte',
        'temporada_floracion',
        'condicion',
        'caracter_id',
        'origen_ingenio',
        'origen_hacienda',
        'origen_suerte',
        'origen_anio',
        'origen_parcela',
        'origen_lote_id',
        'origen_vivero_id',
        'lote_id',
        'consecutivo_vivero_ingenio',
        'total_parcelas'
    ];

    protected $casts = [
        'fecha_siembra' => 'date',
    ];

    protected $appends = [
        'nombre_proyecto',
        'nombre_responsable',
        'nombre_ambiente',
        'consecutivo_corte'
    ];


    public function origenLote()
    {
        return $this->belongsTo(Lote::class, 'origen_lote_id');
    }

    public function origenVivero()
    {
        return $this->belongsTo(Vivero::class, 'origen_vivero_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(\App\Models\Proyecto::class, 'proyecto_id', 'id_prycto');
    }

    public function responsable()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsable_id', 'id_usrio');
    }

    public function caracter()
    {
        return $this->belongsTo(\App\Models\ProyectoCaracter::class, 'caracter_id');
    }

    public function getNombreProyectoAttribute()
    {
        return $this->proyecto?->nm_prycto;
    }

    public function getNombreResponsableAttribute()
    {
        return $this->responsable?->nmbre;
    }

    public function getNombreAmbienteAttribute()
    {
        if (!$this->ambiente) {
            return null;
        }

        if (is_numeric($this->ambiente)) {
            static $megaAmbientes = null;

            if ($megaAmbientes === null) {
                $megaAmbientes = \Illuminate\Support\Facades\DB::connection('sivar')
                    ->table('mega_ambiente')
                    ->get()
                    ->keyBy('id_ambnte');
            }

            $amb = $megaAmbientes->get($this->ambiente);
            return $amb ? $amb->nm_ambnte : null;
        }

        return $this->ambiente;
    }

    public function getConsecutivoCorteAttribute()
    {
        if (!$this->origen_parcela) {
            return null;
        }

        static $consecutivoCorteCache = [];

        $key = $this->origen_parcela;

        if (!isset($consecutivoCorteCache[$key])) {
            $consecutivoCorteCache[$key] = static::where('origen_parcela', $key)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();
        }

        $index = array_search($this->id, $consecutivoCorteCache[$key]);
        return $index !== false ? ($index + 1) : 1;
    }
    public function cosechas()
    {
        return $this->hasMany(ViveroCosecha::class, 'vivero_id');
    }

    public function parcelas()
    {
        return $this->hasMany(ViveroParcela::class, 'vivero_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function historialLotes()
    {
        return $this->hasMany(ViveroLoteHistorial::class, 'vivero_id');
    }
}
