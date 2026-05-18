<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flowering extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'floracion';
    protected $primaryKey = 'id_flrcion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'vrdad', 'grpo','fcha', 'hra'
    ];

    protected $hidden = [
    ];
}
