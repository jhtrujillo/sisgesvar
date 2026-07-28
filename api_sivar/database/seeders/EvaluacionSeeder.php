<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoEvaluacion;
use App\Models\Evaluacion;
use App\Models\RangoEvaluacion;
use Illuminate\Support\Facades\Schema;

class EvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();        
        RangoEvaluacion::truncate();
        Evaluacion::truncate();
        TipoEvaluacion::truncate();

        // 2. CREAR LOS TIPOS DE EVALUACIÓN
        $tipoViabilidad = TipoEvaluacion::create([
            'name' => 'Evaluación de Viabilidad',
            'keyname' => 'viabilidad'
        ]);

        $tipoMerito = TipoEvaluacion::create([
            'name' => 'Evaluación de Mérito',
            'keyname' => 'merito'
        ]);

        /* =========================================================================
         * PLANTILLA 1: msco_r y carbon (Aplica igual en Viabilidad y Mérito)
         * Representa: <=2 (N1), >2 y <=3 (N2), >3 y <=5 (N3), ..., >30 (N9)
         * ========================================================================= */
        $rangosMscoCarbon = [
            ['min' => null, 'max' => 2,    'calificacion' => 1],
            ['min' => 2,    'max' => 3,    'calificacion' => 2],
            ['min' => 3,    'max' => 5,    'calificacion' => 3],
            ['min' => 5,    'max' => 8,    'calificacion' => 4],
            ['min' => 8,    'max' => 11,   'calificacion' => 5],
            ['min' => 11,   'max' => 15,   'calificacion' => 6],
            ['min' => 15,   'max' => 22,   'calificacion' => 7],
            ['min' => 22,   'max' => 30,   'calificacion' => 8],
            ['min' => 30,   'max' => null, 'calificacion' => 9],
        ];

        /* =========================================================================
         * PLANTILLA 2: volcamiento (Aplica igual en Viabilidad y Mérito)
         * Representa: <10 (N1), <20 (N2), <30 (N3), <49 (N4), Resto (N5)
         * ========================================================================= */
        $rangosVolcamiento = [
            ['min' => null, 'max' => 10,   'calificacion' => 1],
            ['min' => 10,   'max' => 20,   'calificacion' => 2],
            ['min' => 20,   'max' => 30,   'calificacion' => 3],
            ['min' => 30,   'max' => 49,   'calificacion' => 4],
            ['min' => 49,   'max' => null, 'calificacion' => 5],
        ];

        /* =========================================================================
         * PLANTILLA 3: Porcentajes Estándar 1 (tchm, etc.)
         * Representa: >120 (N1), >=110 (N2), >=95 (N3), >=85 (N4), Resto (N5)
         * ========================================================================= */
        $rangosPorcentajeEstandar1 = [
            ['min' => 120,  'max' => null, 'calificacion' => 1],
            ['min' => 110,  'max' => 120,  'calificacion' => 2],
            ['min' => 95,   'max' => 110,  'calificacion' => 3],
            ['min' => 85,   'max' => 95,   'calificacion' => 4],
            ['min' => null, 'max' => 85,   'calificacion' => 5],
        ];

        /* =========================================================================
         * PLANTILLA 4: Porcentajes Estándar 2 (dmtro_tllo, etc. en Viabilidad)
         * Representa: >120 (N1), >=100 (N2), >=90 (N3), >=80 (N4), Resto (N5)
         * ========================================================================= */
        $rangosPorcentajeEstandar2 = [
            ['min' => 120,  'max' => null, 'calificacion' => 1],
            ['min' => 100,  'max' => 120,  'calificacion' => 2],
            ['min' => 90,   'max' => 100,  'calificacion' => 3],
            ['min' => 80,   'max' => 90,   'calificacion' => 4],
            ['min' => null, 'max' => 80,   'calificacion' => 5],
        ];


        // =========================================================================
        // 3. REGISTRO DE EVALUACIONES PARA VIABILIDAD
        // =========================================================================

        // Viabilidad - msco_r y carbon
        $this->crearEvaluacionYRangos($tipoViabilidad->id, ['msco_r', 'carbon'], $rangosMscoCarbon);

        // Viabilidad - tchm
        $this->crearEvaluacionYRangos($tipoViabilidad->id, ['tchm'], $rangosPorcentajeEstandar1);

        // Viabilidad - dmtro_tllo, altura_planta, poblacion, scrsa
        $this->crearEvaluacionYRangos($tipoViabilidad->id, ['dmtro_tllo', 'altura_planta', 'poblacion', 'scrsa'], $rangosPorcentajeEstandar2);

        // Viabilidad - volcamiento
        $this->crearEvaluacionYRangos($tipoViabilidad->id, ['volcamiento'], $rangosVolcamiento);

        // Viabilidad - Valores directos (roya)
        $this->crearEvaluacionYRangos($tipoViabilidad->id, ['rya_cfe_r', 'roya_naranja'], []);


        // =========================================================================
        // 4. REGISTRO DE EVALUACIONES PARA MÉRITO
        // =========================================================================

        // Mérito - msco_r y carbon
        $this->crearEvaluacionYRangos($tipoMerito->id, ['msco_r', 'carbon'], $rangosMscoCarbon);

        // Mérito - tchm, scrsa, dmtro_tllo, altura_planta, poblacion (TODAS usan Estándar 1 en Mérito)
        $this->crearEvaluacionYRangos($tipoMerito->id, ['tchm', 'scrsa', 'dmtro_tllo', 'altura_planta', 'poblacion'], $rangosPorcentajeEstandar1);

        // Mérito - volcamiento
        $this->crearEvaluacionYRangos($tipoMerito->id, ['volcamiento'], $rangosVolcamiento);

        // Mérito - Valores directos (roya)
        $this->crearEvaluacionYRangos($tipoMerito->id, ['rya_cfe_r', 'roya_naranja'], []);
    }

    /**
     * Función auxiliar privada para insertar la Evaluación y asociar sus Rangos
     */
    private function crearEvaluacionYRangos(int $tipo_evaluacionId, array $caracteristicas, array $rangos): void
    {
        $evaluacion = Evaluacion::create([
            'tipo_evaluacion_id' => $tipo_evaluacionId,
            'arrayCharacters' => $caracteristicas,
        ]);

        foreach ($rangos as $rango) {
            RangoEvaluacion::create([
                'evaluacion_id' => $evaluacion->id,
                'valor_minimo'  => $rango['min'],
                'valor_maximo'  => $rango['max'],
                'calificacion'  => $rango['calificacion'],
            ]);
        }
    }
}