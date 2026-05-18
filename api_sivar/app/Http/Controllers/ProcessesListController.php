<?php

namespace App\Http\Controllers;

use phpseclib3\Net\SSH2;
use Exception;

use Illuminate\Http\Request;
// Controlador que se encarga de traer los datos de los procesos que han sido completados, filtrarlos y posteriormente mandarlos a la interfaz de usuario
class ProcessesListController extends Controller
{
     private function connectSSH()
    {
        $ssh = new SSH2('192.168.153.184:53670');
        if (!$ssh->login(env('SSH_USERNAME'), env('SSH_PASSWORD'))) {
            throw new Exception('No se pudo conectar al servidor remoto');
        }
        
        return $ssh;
    }

    public function processesList(Request $request)
    {
        $ssh = $this->connectSSH();
        $output = $ssh->exec('condor_history estuvar4 | head');
        $outputLines = explode("\n", trim($output));
        $data = [];
        foreach ($outputLines as $line) {
            // Puedes realizar algún procesamiento adicional en cada línea si es necesario
            // $fields = explode(" ", $line);
            $fields = preg_split('/\s+/', $line);

             // Verificar que haya suficientes campos antes de asignarlos al proceso
             if (count($fields) >= 12) {
                $process = [
                    'ID' => $fields[0] ?? '',
                    'OWNER' => $fields[1] ?? '',
                    'SUBMITTED' => $fields[2] ?? '',
                    'RUN_TIME' => $fields[3] ?? '',
                    'ST' => $fields[4] ?? '',
                    'COMPLETED' => $fields[5] ?? '',
                    'CMD' => $fields[8].' '.$fields[9].' '.$fields[10].' '.$fields[11].' '.$fields[12] ?? ''
                ];

                // Agregar el proceso al array de datos
                $data[] = $process;
            }
        }

        return response()->json($data);
    }
}

        // split
        // print_r($data);
        // $output = $ssh->exec('cd /biosamba/BioDrive/sebastian && sh test.sh '.$contenido);

        // return response()->json([$nombreArchivo.' Creada']);