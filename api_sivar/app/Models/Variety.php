<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variety extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'maestro_V_VIC_BG';
    protected $primaryKey = 'id_nm_vrdad';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nm_vrdad', 'vrdad_madre'
    ];

    protected $hidden = [
    ];
}
