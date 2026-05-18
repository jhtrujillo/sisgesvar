<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'remote_pg_sipro';
    protected $primaryKey = 'id_prycto';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        
    ];

    protected $hidden = [
    ];
}
