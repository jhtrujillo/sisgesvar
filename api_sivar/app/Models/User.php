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

    protected $appends = ['id', 'role', 'ambiente'];

    /**
     * Get the user's ID as 'id' for compatibility.
     */
    public function getIdAttribute()
    {
        return $this->id_usrio;
    }

    /**
     * Get the user's role for SIVAR Registro Ensayos.
     */
    public function getRoleAttribute()
    {
        $lgin = strtolower($this->lgin ?? '');
        if ($lgin === 'estuvar4') {
            return 'JEFE';
        }

        $email = strtolower($this->email ?? '');
        if ($email === 'lolopez@cenicana.org' || $email === 'jhtrujillo@cenicana.org') {
            return 'LIDER';
        }
        return 'JEFE';
    }

    /**
     * Get the user's permitted environments.
     */
    public function getAmbienteAttribute()
    {
        $lgin = strtolower($this->lgin ?? '');
        if ($lgin === 'estuvar4') {
            return [];
        }

        $email = strtolower($this->email ?? '');
        if ($email === 'lolopez@cenicana.org') {
            return ['Pie de monte', 'PIEDEMONTE'];
        }
        if ($email === 'jhtrujillo@cenicana.org') {
            return ['Húmedo'];
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
