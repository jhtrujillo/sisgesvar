<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $connection = 'sivar';
    protected $table = 'remote_pg_areas_cc';
    protected $primaryKey = 'id_area_trbjo';
    public $incrementing = true;

    protected $fillable = [
        'id_area_trbjo', 'nm_area_trbjo', 'id_area', 'nmbre'
    ];

    protected $hidden = [
    ];
}
