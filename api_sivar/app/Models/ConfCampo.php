<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfCampo extends Model
{
    protected $connection = 'sivar';
    protected $table = 'conf_campos';
    protected $primaryKey = 'id_conf_campos';
    public $incrementing = true;

    protected $fillable = [
        'id_conf_campos', 'nmro_cmpo', 'nmbre_cmpo', 'area', 'tpo_cmpo', 'oblgtrio', 'lsta', 'dscrpcion'
    ];

    protected $hidden = [
    ];
}
