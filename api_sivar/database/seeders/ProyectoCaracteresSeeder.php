<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\Proyecto;

class ProyectoCaracteresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Alta Biomasa (Pob3)' => 'Mejoramiento Genético de poblaciones',
            'Alta Sacarosa' => 'Mejoramiento Genético de poblaciones',
            'Avance de Lineas S1 a S2' => 'Conformación de grupos heteróticos',
            'Avance de Lineas S1-2011' => 'Conformación de grupos heteróticos',
            'Avance Lineas SRC1S1' => 'Conformación de grupos heteróticos',
            'Caña Erectas' => 'Mejoramiento Genético de poblaciones',
            'Caña Verde' => 'Mejoramiento Genético de poblaciones',
            'Coleccion Universal' => 'Mantenimiento y caracterización del banco de germoplasma',
            'Enfermedades (Australia) (Pob4)' => 'Mejoramiento Genético de poblaciones',
            'EZH' => 'Obtención de variedades para las zonas húmedas',
            'EZP' => 'Obtención de variedades para la zona piedemonte',
            'EZS' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo1' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo2' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo3' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo4' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo5' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo6' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo10' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo12' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo11' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo9' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo8' => 'Obtención de variedades para la zona semiseca',
            'EZS-Grupo7' => 'Obtención de variedades para la zona semiseca',
            'Grupos Heteroticos' => 'Conformación de grupos heteróticos',
            'Introgresion Genetica' => 'Aumento de la Variabilidad Genética',
            'Lineas' => 'Conformación de grupos heteróticos',
            'Maduracion super temprana' => 'maduración temprana a través de la selección recurrente',
            'Maduracion tardia' => 'maduración temprana a través de la selección recurrente',
            'Maduracion temprana' => 'maduración temprana a través de la selección recurrente',
            'Materia seca, sacarosa y fibra' => 'Mejoramiento Genético de poblaciones',
            'Recombinacion C1-SR-2009' => 'Obtención de variedades para la zona semiseca',
            'Recombinacion C1-SR-2013' => 'Obtención de variedades para la zona semiseca',
            'Recombinacion C1-SR-2016' => 'Obtención de variedades para la zona semiseca',
            'Recombinacion Poblaciones' => 'Obtención de variedades para la zona semiseca',
            'Serie 04-05' => 'Obtención de variedades para la zona semiseca',
            'Serie 06-08' => 'Obtención de variedades para la zona semiseca',
            'Serie 09' => 'Obtención de variedades para la zona semiseca',
            'SLR Roya' => 'Seleccion recurrente por roya', // Let the DB look for it
            'SLR-C1 Sacarosa' => 'Seleccion recurrente por sacarosa',
            'SLR-C2 Sacarosa' => 'Seleccion recurrente por sacarosa',
            'SLR-C2 Sacarosa Mx' => 'Seleccion recurrente por sacarosa',
            'Dobles haploides' => 'Obtención de variedades para la zona semiseca',
        ];

        foreach ($data as $caracter => $proyectoStr) {
            // Find project
            $proyecto = Proyecto::where('nm_prycto', 'ilike', '%' . $proyectoStr . '%')->first();

            if ($proyecto) {
                DB::table('proyecto_caracteres')->updateOrInsert(
                    ['proyecto_id' => $proyecto->id_prycto, 'nombre' => $caracter],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            } else {
                echo "No se encontro proyecto para: $proyectoStr\n";
            }
        }
    }
}
