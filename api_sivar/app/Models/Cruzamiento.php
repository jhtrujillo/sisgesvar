<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cruzamiento extends Model
{
    protected $connection = 'sivar';
    protected $table = 'cruzamientos';
    protected $primaryKey = 'id_crzmnto';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'plntlas_ttles'
    ];

    protected $hidden = [
    ];

    // protected $append = [
    //     'proyecto'
    // ];

    //Relaciones
    /*public function encabezado(){
        return $this->belongsTo('App\Models\DisenoEncabezado', 'id_dsno_enc',  'id_dsno_enc');
    }*/

    /*public function modules(){
        return $this->belongsToMany('App\Models\Area', 'remote_pg_sipro', 'profile_id', 'module_id')->as('access_control');
    }*/

    /*public function toArray(){
        $this->proyecto;
        return parent::toArray();
    }*/
}
