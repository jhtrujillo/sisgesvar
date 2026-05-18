<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Controlador que contiene cada una de las herramientas bioinformaticas en forma de array que van a ser embebidas en esta plataforma
class LinksController extends Controller
{
    public function links(Request $request){
        
        return response()->json([
            array(
                "id" => 1,
                "name" => "Blastn",
                "url" => "http://192.168.153.238:4567",
            ),
            array(
                "id" => 2,
                "name" => "Wiki Cenicaña",
                "url" => "http://192.168.153.238/bioinformatica/",
            ),
            array(
                "id" => 3,
                "name" => "JBrowse",
                "url" => "http://192.168.153.238/jbrowse2/",
            ),
            array(
                "id" => 4,
                "name" => "RStudio Server",
                "url" => "http://192.168.153.238:8002/",
            ),
            array(
                "id" => 5,
                "name" => "Galaxy",
                "url" => null,
            ),
            array(
                "id" => 6,
                "name" => "Bash",
                "url" => "http://192.168.153.238/rscripts/runR.php",
            )
            
        ]);
    }
}
