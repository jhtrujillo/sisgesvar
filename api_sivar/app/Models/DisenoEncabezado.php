<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisenoEncabezado extends Model
{
    protected $connection = 'sivar';
    protected $table = 'diseno_enc';
    protected $primaryKey = 'id_dsno_enc';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'nm_ensyo', 'srie', 'estdo', 'tpo_ensyo', 'id_pr', 'id_ambnte',
    ];

    protected $hidden = [
    ];

    // protected $append = [
    //     'proyecto'
    // ];

    //Relaciones
    public function proyecto(){
        return $this->belongsTo('App\Models\Proyecto', 'id_pr',  'id_prycto');
    }

    public function detalle(){
        return $this->hasMany('App\Models\DisenoDetalle', 'id_dsno_enc',  'id_dsno_enc');
    }

    /*public function modules(){
        return $this->belongsToMany('App\Models\Area', 'remote_pg_sipro', 'profile_id', 'module_id')->as('access_control');
    }*/

    public function toArray(){
        $this->proyecto;
        $this->detalle;
        return parent::toArray();
    }
}
