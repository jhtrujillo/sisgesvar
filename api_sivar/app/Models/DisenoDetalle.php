<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisenoDetalle extends Model
{
    protected $connection = 'sivar';
    protected $table = 'diseno_det';
    protected $primaryKey = 'id_dsno_det';
    public $timestamps = false;
    //public $incrementing = true;

    protected $fillable = [
        'id_dsno_enc', 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes', 'tpo_prcla',
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


    /*public function cruzamiento(){
        return $this->belongsTo('App\Models\Cruzamiento', 'trtmnto',  'id_crzmnto');
    }

    public function toArray(){
        $this->cruzamiento;
        return parent::toArray();
    }*/

    /*public function modules(){
        return $this->belongsToMany('App\Models\Area', 'remote_pg_sipro', 'profile_id', 'module_id')->as('access_control');
    }*/

    /*public function toArray(){
        $this->proyecto;
        return parent::toArray();
    }*/
}
