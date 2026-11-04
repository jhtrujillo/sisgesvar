<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Controlador que contiene cada una de las herramientas bioinformaticas en forma de array que van a ser embebidas en esta plataforma
class LinksController extends Controller
{
    public function links(Request $request){
        $baseUrl = config('services.biotools.base_url');

        return response()->json([
            array(
                "id" => 1,
                "name" => "Blastn",
                "url" => "{$baseUrl}:4567",
            ),
            array(
                "id" => 2,
                "name" => "Wiki Cenicaña",
                "url" => "{$baseUrl}/bioinformatica/",
            ),
            array(
                "id" => 3,
                "name" => "JBrowse",
                "url" => "{$baseUrl}:8080/",
            ),
            array(
                "id" => 4,
                "name" => "RStudio Server",
                "url" => "{$baseUrl}:8002/",
            ),
            array(
                "id" => 5,
                "name" => "Galaxy",
                "url" => null,
            ),
            array(
                "id" => 6,
                "name" => "Bash",
                "url" => "{$baseUrl}/rscripts/runR.php",
            )
            
        ]);
    }
}
