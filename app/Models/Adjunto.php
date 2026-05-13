<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    protected $table = 'adjuntos';
    protected $guarded = [];

    public function ensayo()
    {
        return $this->belongsTo(Ensayo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
