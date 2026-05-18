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
