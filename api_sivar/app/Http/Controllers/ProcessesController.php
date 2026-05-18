<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Validator;
use App\Models\Processes;
// Controlador que se encarga de capturar los parametros que son enviados desde el formulario "Alineamiento de Secuancias" y los envia al clúster parara que sean ejecutados.

class ProcessesController extends Controller
{
    
    public function processes(Request $request) {
        try{

            $data = $request->validate([
                'parametros' => 'required',
            ]);
        
         if(!$data){
                return response("parametros incorrectos", 400);
            }
        date_default_timezone_set('America/Bogota');
            $model = new Processes();
            $model->parametros = $data['parametros'];
            // $model->save();
            $contenido = $model;
            date_default_timezone_set('America/Bogota');
            $nombreArchivo = 'Consulta_' . date('Ymd_His') . '.txt';
            $ssh = new SSH2('192.168.153.184:53670');
            if (!$ssh->login(env('SSH_USERNAME'), env('SSH_PASSWORD'))) {
                throw new Exception('No se pudo conectar al servidor remoto');
            }
            $output = $ssh->exec('cd /biosamba/BioDrive/sebastian '.' && sh runcluster.sh \''.$contenido.'\'');
            // $output = $ssh->exec('cd /biosamba/BioDrive/sebastian && sh test.sh '.$contenido);
    
            // return response()->json([$nombreArchivo.' Creada']);
            return response()->json(['Creada']);
            return response()->json(['message' => 'Archivo generado', 'archivo' => $nombreArchivo]);
        }
        catch (\Exception $e) {
            return response("error al guardar", 500);
        }


        if (!$model) {
            return response()->json(['message' => 'Consulta no encontrada'], 404);
        }
      


        $ssh = new SSH2('192.168.153.238:53670');
        if (!$ssh->login(env('SSH_USERNAME'), env('SSH_PASSWORD'))) {
            throw new Exception('No se pudo conectar al servidor remoto');
        }
        
        $output = $ssh->exec('cd /biosamba/BioDrive/sebastian && ls');
        
        return response()->json([$output]);
            set_time_limit(900);

    }
}

