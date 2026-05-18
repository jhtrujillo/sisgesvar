<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $connection = 'sivar';
    protected $table = 'remote_pg_sipro';
    protected $primaryKey = 'id_prycto';
    public $incrementing = true;

    protected $fillable = [
        'id_prycto', 'id_area', 'id_area_trbjo', 'nm_prycto'
    ];

    protected $hidden = [
    ];

    protected $append = [
        'area'
    ];

    //Relaciones
    public function area(){
        return $this->belongsTo('App\Models\Area', 'id_area_trbjo',  'id_area_trbjo');
    }

    public function toArray(){
        $this->area;
        return parent::toArray();
    }
}
