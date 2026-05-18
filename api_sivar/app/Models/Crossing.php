<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crossing extends Model
{
    use HasFactory;
    protected $connection = 'sivar';
    protected $table = 'cruzamientos';
    protected $primaryKey = 'id_crzmnto';
    public $timestamps = false;

    protected $fillable = [
        'id_crzmnto', 'pdgree', 'vrdad_mdre', 'vrdad_pdre1', 'vrdad_pdre2', 'vrdad_pdre3', 'vrdad_pdre4', 'vrdad_pdre5'
    ];

    protected $hidden = [];

    public static function limpiarString($string)
    {
        $string = trim($string);
        $string = str_replace(['á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'], ['a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'], $string);
        $string = str_replace(['é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'], ['e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'], $string);
        $string = str_replace(['í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'], ['i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'], $string);
        $string = str_replace(['ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'], ['o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'], $string);
        $string = str_replace(['ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'], ['u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'], $string);
        $string = str_replace(['ñ', 'Ñ', 'ç', 'Ç'], ['n', 'N', 'c', 'C'], $string);
        
        // Esta parte se encarga de eliminar cualquier caracter extraño
        $string = preg_replace('/[^A-Za-z0-9\-_]+/', '', $string);

        return $string;
    }
}

