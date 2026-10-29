<?php

/*ESTE ES UN MODELO PERSONALIZADO PARA AUTH DE USUARIO EN EL API */

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Extensions\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements JWTSubject, AuthenticatableContract
{
    use Notifiable;

    protected $connection = 'users';
    protected $table = 'usuario';
    public $timestamps = false;
    protected $primaryKey = 'id_usrio';
    public $incrementing = false;

    protected $appends = ['id', 'role', 'ambiente', 'name'];

    /**
     * Get the user's ID as 'id' for compatibility.
     */
    public function getIdAttribute()
    {
        return $this->id_usrio;
    }

    /**
     * Get the user's name as 'name' for compatibility.
     */
    public function getNameAttribute()
    {
        return $this->nmbre;
    }

    /**
     * Get the user's role for SIVAR Registro Ensayos.
     */
    public function getRoleAttribute()
    {
        // Buscar si el usuario tiene rol ADMIN en algún proyecto (equivale a JEFE)
        $hasAdmin = DB::connection('sivar')->table('proyecto_permisos')
            ->where('usuario_id', $this->id_usrio)
            ->where('rol', 'ADMIN')
            ->exists();

        if ($hasAdmin) {
            return 'JEFE';
        }

        // Buscar si tiene rol EDITOR (equivale a LIDER)
        $hasEditor = DB::connection('sivar')->table('proyecto_permisos')
            ->where('usuario_id', $this->id_usrio)
            ->where('rol', 'EDITOR')
            ->exists();

        if ($hasEditor) {
            return 'LIDER';
        }

        // Por defecto, rol JEFE (mantiene comportamiento anterior)
        return 'JEFE';
    }

    /**
     * Get the user's permitted environments.
     * Determina los ambientes permitidos según los proyectos asignados.
     */
    public function getAmbienteAttribute()
    {
        // Buscar si el usuario tiene rol ADMIN en algún proyecto → acceso a todos los ambientes
        $hasAdmin = DB::connection('sivar')->table('proyecto_permisos')
            ->where('usuario_id', $this->id_usrio)
            ->where('rol', 'ADMIN')
            ->exists();

        if ($hasAdmin) {
            return \App\Models\Catalogo::where('categoria', 'AMBIENTE')
                ->orderBy('valor')
                ->pluck('valor')
                ->toArray();
        }

        // Buscar ambientes de los proyectos donde el usuario tiene permisos
        $proyectoIds = DB::connection('sivar')->table('proyecto_permisos')
            ->where('usuario_id', $this->id_usrio)
            ->pluck('proyecto_id');

        if ($proyectoIds->isNotEmpty()) {
            // Obtener ambientes asociados a los viveros de esos proyectos
            $ambientes = DB::connection('sivar')->table('viveros')
                ->whereIn('proyecto_id', $proyectoIds)
                ->whereNotNull('ambiente')
                ->distinct()
                ->pluck('ambiente')
                ->toArray();

            if (!empty($ambientes)) {
                return $ambientes;
            }
        }

        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public static function getAuthPasswordName()
    {
        return 'clve';
    }

    public function getAuthPassword()
    {
        return $this->clve;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
