<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    protected $connection = 'sivar';
    protected $table = 'adjuntos';
    protected $fillable = ['ensayo_id', 'nombre_archivo', 'ruta', 'mime_type', 'size', 'user_id'];

    public function ensayo()
    {
        return $this->belongsTo(Ensayo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
