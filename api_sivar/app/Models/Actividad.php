<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Actividad extends Model
{
    protected $connection = 'sivar';
    protected $table = 'actividads';
    protected $fillable = ['user_id', 'accion', 'descripcion', 'detalles'];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Simple static helper to record log tracks seamlessly from anywhere
     */
    public static function registrar(string $accion, string $descripcion, array $detalles = [])
    {
        return self::create([
            'user_id' => Auth::id(),
            'accion' => strtoupper($accion),
            'descripcion' => $descripcion,
            'detalles' => $detalles
        ]);
    }
}
