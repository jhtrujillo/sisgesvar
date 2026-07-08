<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabInventoryAlertEmail extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre', 'email', 'activo'];
}
