<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Vivero;
use App\Models\Projects;
use Carbon\Carbon;
use App\Models\Flowering;
// Include other necessary models if required by the logic

class CrossingService
{
    public function calcularViabilidadCaracteristica($caracteristica, $florA, $florB, $ponderado, $testigo)
    {
        $nivel_florA = $this->obtenerNivelEvaluacion($florA, $caracteristica, $testigo, 'viabilidad');
        $nivel_florB = $this->obtenerNivelEvaluacion($florB, $caracteristica, $testigo, 'viabilidad');

        return ($nivel_florA + $nivel_florB) <= $ponderado->nivel;
    }

    public function calcularValorMerito($caracteristica, $florA, $ponderado, $testigo)
    {
        $nivel_florA = $this->obtenerNivelEvaluacion($florA, $caracteristica, $testigo, 'merito');

        return $ponderado * $nivel_florA;
    }

    public function calcularViabilidad($flores, $flores_PR, $flores_EIII, $ponderados, $testigo)
    {
        // Indexar las flores regionales por nombre de variedad para una búsqueda exacta libre de bugs
        // --- KINSHIP FETCH BLOCK ---
        $allUniqueVars = [];
        if ($flores) { foreach ($flores as $f) { $allUniqueVars[] = $f->vrdad; } }
        if ($flores_PR) { foreach ($flores_PR as $f) { $allUniqueVars[] = $f->vrdad; } }
        if ($flores_EIII) { foreach ($flores_EIII as $f) { $allUniqueVars[] = $f->vrdad; } }
        $allUniqueVars = array_unique($allUniqueVars);

        $kinshipMap = [];
        if (!empty($allUniqueVars)) {
            $kinshipList = \DB::connection('sivar')->table('matriz_kinship')
                ->whereIn('variedad_1', $allUniqueVars)
                ->whereIn('variedad_2', $allUniqueVars)
                ->get();
            foreach ($kinshipList as $k) {
                $v1 = trim(strtoupper($k->variedad_1));
                $v2 = trim(strtoupper($k->variedad_2));
                $kinshipMap[$v1 . '|' . $v2] = (float)$k->kinship;
                $kinshipMap[$v2 . '|' . $v1] = (float)$k->kinship;
            }
        }
        // ---------------------------

        $floresPRMap = [];
        if ($flores_PR) {
            foreach ($flores_PR as $fPR) {
                $floresPRMap[$fPR->vrdad] = $fPR;
            }
        }

        $floresEIIIMap = [];
        if ($flores_EIII) {
            foreach ($flores_EIII as $fEIII) {
                $floresEIIIMap[$fEIII->vrdad] = $fEIII;
            }
        }

        $arreglo = array();
        for ($i = 0; $i < sizeof($flores); $i++) {
            $florA = $flores[$i];
            $florA_PR = isset($floresPRMap[$florA->vrdad]) ? $floresPRMap[$florA->vrdad] : null;
            $florA_EIII = isset($floresEIIIMap[$florA->vrdad]) ? $floresEIIIMap[$florA->vrdad] : null;
            $arregloFlorA = array();

            for ($j = 0; $j < sizeof($flores); $j++) {
                $florB = $flores[$j];
                $florB_PR = isset($floresPRMap[$florB->vrdad]) ? $floresPRMap[$florB->vrdad] : null;
                $florB_EIII = isset($floresEIIIMap[$florB->vrdad]) ? $floresEIIIMap[$florB->vrdad] : null;
                
                $viabilidad = array(
                    'varA' => $florA->vrdad,
                    'varB' => $florB->vrdad,
                    'viabilidad' => true,
                    'vm' => '',
                    'vm2' => '',
                    'polen' => $florA->polen ?? null,
                    'polen2' => $florB->polen ?? null,
                    'sxo' => $florA->sxo ?? null,
                    'sxo2' => $florB->sxo ?? null,
                    'cantidad_flores' => $florA->cantidad_flores ?? 0,
                    'cantidad_flores2' => $florB->cantidad_flores ?? 0,
                    'proyecto' => $florA->id_pr ?? null,
                    'proyecto2' => $florB->id_pr ?? null,
                    'caracter' => $florA->id_crcter ?? null,
                    'caracter2' => $florB->id_crcter ?? null,
                    'nombre_proyecto' => $florA->nombre_proyecto ?? null,
                    'nombre_proyecto2' => $florB->nombre_proyecto ?? null,

                    'id_caracter' => $florA->id_caracter ?? null,
                    'id_caracter2' => $florB->id_caracter ?? null,
                    'kinship_score' => isset($kinshipMap[trim(strtoupper($florA->vrdad)) . '|' . trim(strtoupper($florB->vrdad))]) ? $kinshipMap[trim(strtoupper($florA->vrdad)) . '|' . trim(strtoupper($florB->vrdad))] : 0
                );
                $vm = 0;
                $vm2 = 0;
                $pesoEvaluadoA = 0;
                $pesoEvaluadoB = 0;
                $pesoTotalActivo = 0;

                foreach ($ponderados as $key => $ponderado) {
                    $caracteristica = $ponderado->equivalente;

                    // Crear copias locales para evitar la reasignación silenciosa en el bucle de ponderados
                    $florA_eval = $florA;
                    $florB_eval = $florB;

                    if ($florA_eval && (!isset($florA_eval->$caracteristica) || is_null($florA_eval->$caracteristica) || $florA_eval->$caracteristica === '')) {
                        $florA_eval = $florA_PR;
                        if (!$florA_PR || (!isset($florA_PR->$caracteristica) || is_null($florA_PR->$caracteristica) || $florA_PR->$caracteristica === '')) {
                            $florA_eval = $florA_EIII;
                        }
                    }
                    if ($florB_eval && (!isset($florB_eval->$caracteristica) || is_null($florB_eval->$caracteristica) || $florB_eval->$caracteristica === '')) {
                        $florB_eval = $florB_PR;
                        if (!$florB_PR || (!isset($florB_PR->$caracteristica) || is_null($florB_PR->$caracteristica) || $florB_PR->$caracteristica === '')) {
                            $florB_eval = $florB_EIII;
                        }
                    }

                    if ($ponderado->ponderado > 0) {
                        $pesoTotalActivo += (float) $ponderado->ponderado;
                        $isDisease = in_array($caracteristica, ['msco_r', 'carbon', 'rya_cfe_r', 'roya_naranja', 'volcamiento']);
                        $hasA = $florA_eval && isset($florA_eval->$caracteristica) && $florA_eval->$caracteristica !== '' && !is_null($florA_eval->$caracteristica);
                        $hasT = $isDisease || ($testigo != null && isset($testigo->$caracteristica) && $testigo->$caracteristica !== '' && !is_null($testigo->$caracteristica));
                        if ($hasT) {
                            $testigoVal = ($testigo != null && isset($testigo->$caracteristica)) ? $testigo->$caracteristica : null;

                            if ($hasA) {
                                $vm += ($this->calcularValorMerito($caracteristica, $florA_eval, $ponderado->ponderado, $testigoVal)) / 100;
                                $pesoEvaluadoA += (float) $ponderado->ponderado;
                            }

                            $hasB = $florB_eval && isset($florB_eval->$caracteristica) && $florB_eval->$caracteristica !== '' && !is_null($florB_eval->$caracteristica);
                            if ($hasB) {
                                $vm2 += ($this->calcularValorMerito($caracteristica, $florB_eval, $ponderado->ponderado, $testigoVal)) / 100;
                                $pesoEvaluadoB += (float) $ponderado->ponderado;
                            }

                            if ($hasA && $hasB) {
                                if (!$this->calcularViabilidadCaracteristica($caracteristica, $florA_eval, $florB_eval, $ponderado, $testigoVal)) {
                                    $viabilidad['viabilidad'] = false;

                                    $nombresLegibles = [
                                        'scrsa' => 'Sacarosa',
                                        'tchm' => 'TCHM (Producción)',
                                        'msco_r' => 'Mosaico',
                                        'rya_cfe_r' => 'Roya',
                                        'roya' => 'Roya',
                                        'roya_naranja' => 'Roya Naranja',
                                        'carbon' => 'Carbón',
                                        'volcamiento' => 'Volcamiento',
                                        'altura_planta' => 'Altura de Planta',
                                        'poblacion' => 'Población',
                                        'dmtro_tllo' => 'Diámetro de Tallo'
                                    ];
                                    $nombreLegible = $nombresLegibles[$caracteristica] ?? strtoupper($caracteristica);
                                    $nuevaCausa = $nombreLegible . " excede límite (" . $ponderado->nivel . ")";
                                    $viabilidad['causa_veto'] = !empty($viabilidad['causa_veto'])
                                        ? $viabilidad['causa_veto'] . " | " . $nuevaCausa
                                        : $nuevaCausa;
                                }
                            }
                        }
                    }
                }

                // Otras condiciones 
                if (($florB->sxo == "Hembra" || $florB->sxo == "HD" || $florB->sxo == "HF")) {
                    $prev = isset($viabilidad['causa_veto']) && $viabilidad['viabilidad'] === false ? $viabilidad['causa_veto'] . " | " : "";
                    $viabilidad['viabilidad'] = false;
                    $viabilidad['causa_veto'] = $prev . "Incompatibilidad de sexo (Ambos son Hembra)";
                }
                if (($florA->sxo == "Macho" || $florA->sxo == "MD" || $florA->sxo == "MF")) {
                    $prev = isset($viabilidad['causa_veto']) && $viabilidad['viabilidad'] === false ? $viabilidad['causa_veto'] . " | " : "";
                    $viabilidad['viabilidad'] = false;
                    $viabilidad['causa_veto'] = $prev . "Incompatibilidad de sexo (Ambos son Macho)";
                }

                // Normalizar VM proporcionalmente a los pesos evaluados para evitar sesgo por datos faltantes
                $viabilidad['vm'] = $pesoEvaluadoA > 0
                    ? round(($vm * ($pesoTotalActivo / $pesoEvaluadoA)), 2)
                    : null;
                $viabilidad['vm2'] = $pesoEvaluadoB > 0
                    ? round(($vm2 * ($pesoTotalActivo / $pesoEvaluadoB)), 2)
                    : null;
                array_push($arregloFlorA, $viabilidad);
            }
            array_push($arreglo, $arregloFlorA);
        }
        return $arreglo;
    }

    public function obtenerDistanciaGenetica($florA, $florB)
    {
        $distancia = DB::connection('sivar')
            ->table('caracterizacion_molecular_BG')
            ->where('fla', $florA)
            ->where('clmna', $florB)
            ->select('vlor')
            ->first();

        if (!$distancia) {
            return "-";
        } else {
            return $distancia->vlor;
        }
    }
    public function obtenerDistanciaGeneticaConjunto($fl)
    {
        $flores = array();
        foreach ($fl as $f) {
            array_push($flores, $f->vrdad);
        }

        $distancia = DB::connection('sivar')->table('maestro_V_VIC_BG')
            ->leftJoin('caracterizacion_molecular_BG', function ($join) use ($flores) {
                $join->on('caracterizacion_molecular_BG.fla', '=', 'maestro_V_VIC_BG.nm_vrdad')
                    ->where(function ($q) use ($flores) {
                        $q->whereIn('fla', $flores)
                            ->whereIn('clmna', $flores);
                    });
            })
            ->whereIn('nm_vrdad', $flores)
            ->orderBy('fla')
            ->orderBy('clmna')
            ->select('vlor', 'fla', 'clmna')
            ->get();

        $dist = array();
        foreach ($distancia as $d) {
            $dist[$d->fla][$d->clmna] = $d->vlor;
        }

        return $dist;
    }
    private function resolverSitioAmbiente(?string $ambiente): ?string
    {
        if (in_array($ambiente, ["Semiseco", "Seco Semiseco", "Seco-Semiseco"], true)) {
            return "Seco-Semiseco";
        }
        if (in_array($ambiente, ["Humedo", "Húmedo"], true)) {
            return "Humedo";
        }
        if ($ambiente === "Piedemonte") {
            return "Piedemonte";
        }
        return null;
    }

    private function obtenerPonderados($proyecto, $ambiente)
    {
        return DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin('ponderados_valor_merito', function ($join) use ($proyecto, $ambiente) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();
    }

    /**
     * Genera las expresiones SQL para los 10 caracteres del Banco de Germoplasma.
     * Prioriza el promedio del Mega Ambiente seleccionado y hace fallback al promedio general si falta dato.
     */
    private function buildBgTraitsSql(?string $sitioAmbiente): string
    {
        $cols = [
            'mosaico_p' => 'msco_r',
            'roya_cafe_r' => 'rya_cfe_r',
            'roya_naranja_r' => 'roya_naranja',
            'carbon_p' => 'carbon',
            'tchm' => 'tchm',
            'diametro_tallo' => 'dmtro_tllo',
            'volcamiento' => 'volcamiento',
            'altura_planta' => 'altura_planta',
            'poblacion_1m' => 'poblacion',
            'sacarosa' => 'scrsa',
        ];

        $parts = [];
        $sitioSql = $sitioAmbiente ? DB::connection('sivar')->getPdo()->quote($sitioAmbiente) : null;

        foreach ($cols as $col => $alias) {
            $expr = "avg(NULLIF(REPLACE(CAST({$col} AS TEXT), ',', '.'), '')::numeric)";
            if ($sitioSql) {
                $parts[] = "COALESCE({$expr} FILTER (WHERE caracterizacion_banco_germoplasma.sitio_seleccion = {$sitioSql}), {$expr}) as {$alias}";
            } else {
                $parts[] = "{$expr} as {$alias}";
            }
        }

        return implode(", ", $parts);
    }

    private function buildCampoTraitsSql(): string
    {
        return "avg(NULLIF(REPLACE(CAST(mosaico AS TEXT), ',', '.'), '')::numeric) as msco_r, " .
            "avg(NULLIF(REPLACE(CAST(roya AS TEXT), ',', '.'), '')::numeric) as rya_cfe_r, " .
            "avg(NULLIF(REPLACE(CAST(\"179\" AS TEXT), ',', '.'), '')::numeric) as roya_naranja, " .
            "avg(NULLIF(REPLACE(CAST(carbon AS TEXT), ',', '.'), '')::numeric) as carbon, " .
            "avg(NULLIF(REPLACE(CAST(\"173\" AS TEXT), ',', '.'), '')::numeric) as tchm, " .
            "avg(NULLIF(REPLACE(CAST(\"163\" AS TEXT), ',', '.'), '')::numeric) as scrsa, " .
            "avg(NULLIF(REPLACE(CAST(\"Cepa Incidencia de Volcamiento (Escala)\" AS TEXT), ',', '.'), '')::numeric) as volcamiento, " .
            "avg(NULLIF(REPLACE(CAST(\"Tallo Altura (cm)\" AS TEXT), ',', '.'), '')::numeric) as altura_planta, " .
            "avg(NULLIF(REPLACE(CAST(\"Población (m)\" AS TEXT), ',', '.'), '')::numeric) as poblacion, " .
            "avg(NULLIF(REPLACE(CAST(\"DiametroTallo\" AS TEXT), ',', '.'), '')::numeric) as dmtro_tllo";
    }

    private function obtenerVariedadTestigo(string $testigo, ?string $ambiente = null)
    {
        $sitioAmbiente = $this->resolverSitioAmbiente($ambiente);
        $traitsSql = $this->buildBgTraitsSql($sitioAmbiente);

        return DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
            ->select(DB::raw("\"variedad\", {$traitsSql}"))
            ->where('variedad', '=', $testigo)
            ->groupBy('variedad')
            ->first();
    }

    private function aplicarFiltroCaracter($query, $caracter): void
    {
        if (!$caracter) {
            return;
        }
        if (strpos((string) $caracter, ',') !== false) {
            $query->whereIn('floracion.id_crcter', explode(',', (string) $caracter));
        } else {
            $query->where('floracion.id_crcter', $caracter);
        }
    }

    /**
     * Consulta unificada de flores con datos fenotípicos para BG, PR (estdo_slccion=5) y EIII (estdo_slccion=3).
     */
    private function obtenerFloresConFenotipo(string $fuente, array $opts)
    {
        $includeMeta = $opts['include_project_meta'] ?? true;
        $sitioAmbiente = $this->resolverSitioAmbiente($opts['ambiente'] ?? null);

        $q = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');

        if ($includeMeta) {
            $q->leftJoin('proyecto_caracteres', 'proyecto_caracteres.id', '=', 'floracion.id_crcter');
        }

        if ($fuente === 'bg') {
            $q->leftJoin('caracterizacion_banco_germoplasma', 'caracterizacion_banco_germoplasma.variedad', '=', 'floracion.vrdad');
            $traitsSql = $this->buildBgTraitsSql($sitioAmbiente);
        } else {
            $estadoSeleccion = $fuente === 'pr' ? 5 : 3;
            $q->leftJoin('datos_campo_crudos', function ($join) use ($estadoSeleccion) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', $estadoSeleccion);
            });
            $traitsSql = $this->buildCampoTraitsSql();
        }

        if (!empty($opts['id_prycto'])) {
            $q->where('remote_pg_sipro.id_prycto', $opts['id_prycto']);
        }
        if (!empty($opts['cd_cntble_list'])) {
            $q->whereIn('remote_pg_sipro.cd_cntble', $opts['cd_cntble_list']);
        }
        if (array_key_exists('bolsa_comun', $opts) && !is_null($opts['bolsa_comun'])) {
            $q->where('floracion.bolsa_comun', '=', $opts['bolsa_comun']);
        }

        $this->aplicarFiltroCaracter($q, $opts['caracter'] ?? null);

        $q->whereBetween('floracion.fcha', [$opts['fechai'], $opts['fechaf']])
          ->where('floracion.estado', '=', 0);

        if ($includeMeta) {
            $q->groupBy(
                'floracion.vrdad',
                'floracion.sxo',
                'floracion.polen',
                'floracion.id_pr',
                'proyecto_caracteres.id',
                'proyecto_caracteres.nombre',
                'remote_pg_sipro.nm_prycto'
            )->select(DB::raw(
                "\"floracion\".\"vrdad\", COUNT(DISTINCT \"floracion\".\"id_flrcion\") as cantidad_flores, " .
                "\"floracion\".\"sxo\", \"floracion\".\"polen\", \"floracion\".\"id_pr\", " .
                "\"proyecto_caracteres\".\"nombre\" as id_crcter, \"proyecto_caracteres\".\"id\" as id_caracter, " .
                "\"remote_pg_sipro\".\"nm_prycto\" as nombre_proyecto, {$traitsSql}"
            ))->orderBy('floracion.polen');
        } else {
            if ($fuente === 'bg') {
                $q->groupBy('floracion.vrdad', 'floracion.sxo', 'floracion.polen')
                  ->select(DB::raw(
                      "\"floracion\".\"vrdad\", COUNT(DISTINCT \"floracion\".\"id_flrcion\") as cantidad_flores, " .
                      "\"floracion\".\"sxo\", \"floracion\".\"polen\", {$traitsSql}"
                  ));
            } else {
                $q->groupBy('floracion.vrdad', 'floracion.sxo')
                  ->select(DB::raw(
                      "\"floracion\".\"vrdad\", COUNT(DISTINCT \"floracion\".\"id_flrcion\") as cantidad_flores, " .
                      "\"floracion\".\"sxo\", avg(NULLIF(REPLACE(CAST(polen AS TEXT), ',', '.'), '')::numeric) as polen, {$traitsSql}"
                  ));
            }
            $q->orderBy('floracion.vrdad');
        }

        return $q->distinct()->get();
    }

    public function generateMatrix($proy, $proyecto, $testigo, $ambiente = 'Semiseco', $caracter = null)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');
        $proyectos = explode(",", $proy);
        $ponderados = $this->obtenerPonderados($proyecto, $ambiente);
        $hasSpecificProject = !in_array('General', $proyectos) && !empty($proyectos) && $proyectos[0] !== 'all';

        $baseOpts = [
            'fechai' => $fechai,
            'fechaf' => $fechaf,
            'ambiente' => $ambiente,
            'caracter' => $caracter,
            'include_project_meta' => false,
            'cd_cntble_list' => $hasSpecificProject ? $proyectos : null,
        ];

        $flores_BG = $this->obtenerFloresConFenotipo('bg', $baseOpts);
        if ($flores_BG->isEmpty() && $hasSpecificProject) {
            $baseOpts['cd_cntble_list'] = null;
            $flores_BG = $this->obtenerFloresConFenotipo('bg', $baseOpts);
        }

        $flores_PR = $this->obtenerFloresConFenotipo('pr', $baseOpts);
        $flores_EIII = $this->obtenerFloresConFenotipo('eiii', $baseOpts);
        $variedad_testigo = $this->obtenerVariedadTestigo($testigo, $ambiente);

        $arreglo = $this->calcularViabilidad($flores_BG, $flores_PR, $flores_EIII, $ponderados, $variedad_testigo);
        $distancias = $this->obtenerDistanciaGeneticaConjunto($flores_BG);

        return response()->json([
            'proyectos' => $proy,
            'proyecto' => $proyecto,
            'fecha_i' => $fechai,
            'fecha_f' => $fechaf,
            'flores' => $flores_BG,
            'viabilidad' => $arreglo,
            'distancias' => $distancias,
            'testigo' => $testigo,
        ]);
    }

    public function suggestionCrossings($proy, $proyecto, $testigo, $ambiente)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');

        $proyectos = Projects::selectRaw('remote_pg_sipro.nm_prycto, remote_pg_sipro.cd_cntble, remote_pg_sipro.id_prycto, count(*) as numero')
            ->join('floracion', 'floracion.id_pr', '=', 'remote_pg_sipro.id_prycto')
            ->groupBy('remote_pg_sipro.id_prycto', 'remote_pg_sipro.nm_prycto', 'remote_pg_sipro.cd_cntble')
            ->havingRaw('count(*) > 0')
            ->whereBetween('floracion.fcha', [Carbon::yesterday(), Carbon::today()])
            ->where('floracion.estado', '=', 0)
            ->select('id_prycto', 'cd_cntble')
            ->get();

        $flores = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr')
            ->leftJoin('proyecto_caracteres', 'proyecto_caracteres.id', '=', 'floracion.id_crcter')
            ->whereBetween('floracion.fcha', [$fechai, $fechaf])
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))
            ->groupBy('floracion.vrdad', 'floracion.id_pr', 'floracion.id_crcter')
            ->orderBy('floracion.vrdad', 'desc')
            ->get();

        $ponderados = $this->obtenerPonderados($proyecto, $ambiente);
        $variedad_testigo = $this->obtenerVariedadTestigo($testigo, $ambiente);
        $distancias = $this->obtenerDistanciaGeneticaConjunto($flores);
        $viabilidad = [];

        foreach ($proyectos as $projItem) {
            $opts = [
                'fechai' => $fechai,
                'fechaf' => $fechaf,
                'id_prycto' => $projItem->id_prycto,
                'bolsa_comun' => 0,
                'ambiente' => $ambiente,
                'include_project_meta' => true,
            ];

            $flores_BG = $this->obtenerFloresConFenotipo('bg', $opts);
            $flores_PR = $this->obtenerFloresConFenotipo('pr', $opts);
            $flores_EIII = $this->obtenerFloresConFenotipo('eiii', $opts);

            $viabilidad[] = $this->calcularViabilidad($flores_BG, $flores_PR, $flores_EIII, $ponderados, $variedad_testigo);
        }

        return response()->json([
            'proyectos_filtro' => $proy,
            'proyecto' => $proyecto,
            'fecha_i' => $fechai,
            'fecha_f' => $fechaf,
            'flores' => $flores,
            'testigo' => $testigo,
            'viabilidades' => $viabilidad,
            'distancias' => $distancias,
            'ambiente' => $ambiente,
            'proyectos' => $proyectos,
        ]);
    }

    public function sugerenciasCruzamientosBolsaComun($proy, $proyecto, $testigo, $ambiente)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');

        $flores = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr')
            ->leftJoin('proyecto_caracteres', 'proyecto_caracteres.id', '=', 'floracion.id_crcter')
            ->whereBetween('floracion.fcha', [$fechai, $fechaf])
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 1)
            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))
            ->groupBy('floracion.vrdad', 'floracion.id_pr', 'floracion.id_crcter')
            ->orderBy('floracion.vrdad', 'desc')
            ->get();

        $ponderados = $this->obtenerPonderados($proyecto, $ambiente);
        $opts = [
            'fechai' => $fechai,
            'fechaf' => $fechaf,
            'bolsa_comun' => 1,
            'ambiente' => $ambiente,
            'include_project_meta' => true,
        ];

        $flores_BG = $this->obtenerFloresConFenotipo('bg', $opts);
        $flores_PR = $this->obtenerFloresConFenotipo('pr', $opts);
        $flores_EIII = $this->obtenerFloresConFenotipo('eiii', $opts);
        $variedad_testigo = $this->obtenerVariedadTestigo($testigo, $ambiente);

        $arreglo = $this->calcularViabilidad($flores_BG, $flores_PR, $flores_EIII, $ponderados, $variedad_testigo);
        $distancias = $this->obtenerDistanciaGeneticaConjunto($flores);

        return response()->json([
            'flores' => $flores,
            'viabilidades' => $arreglo,
            'distancias' => $distancias,
            'testigo_limpio' => $variedad_testigo,
            'flores_bg' => $flores_BG,
            'flores_pr' => $flores_PR,
            'flores_eiii' => $flores_EIII,
        ]);
    }

    public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente, $caracter = null)
    {
        \Log::info("Called suggestionCrossingsPerProject: proy=$proy, proyecto=$proyecto, ambiente=$ambiente, caracter=" . ($caracter ?? 'NULL'));
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');

        $floresQuery = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr')
            ->leftJoin('proyecto_caracteres', 'proyecto_caracteres.id', '=', 'floracion.id_crcter')
            ->where('remote_pg_sipro.id_prycto', $proy)
            ->whereBetween('floracion.fcha', [$fechai, $fechaf])
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0);

        $this->aplicarFiltroCaracter($floresQuery, $caracter);

        $flores = $floresQuery
            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))
            ->groupBy('floracion.vrdad', 'floracion.id_pr', 'floracion.id_crcter')
            ->orderBy('floracion.vrdad', 'desc')
            ->get();

        $ponderados = $this->obtenerPonderados($proyecto, $ambiente);
        $opts = [
            'fechai' => $fechai,
            'fechaf' => $fechaf,
            'id_prycto' => $proy,
            'bolsa_comun' => 0,
            'caracter' => $caracter,
            'ambiente' => $ambiente,
            'include_project_meta' => true,
        ];

        $flores_BG = $this->obtenerFloresConFenotipo('bg', $opts);
        $flores_PR = $this->obtenerFloresConFenotipo('pr', $opts);
        $flores_EIII = $this->obtenerFloresConFenotipo('eiii', $opts);
        $variedad_testigo = $this->obtenerVariedadTestigo($testigo, $ambiente);

        $arreglo = $this->calcularViabilidad($flores_BG, $flores_PR, $flores_EIII, $ponderados, $variedad_testigo);
        $distancias = $this->obtenerDistanciaGeneticaConjunto($flores);

        return response()->json([
            'flores' => $flores,
            'viabilidades' => $arreglo,
            'distancias' => $distancias,
            'testigo_limpio' => $variedad_testigo,
            'flores_bg' => $flores_BG,
            'flores_pr' => $flores_PR,
            'flores_eiii' => $flores_EIII,
        ]);
    }

    public function crossingList($perPage, $search, $filtersJson)
    {
        $query = DB::connection('sivar')->table('cruzamientos')
            ->leftJoin('floracion', 'cruzamientos.id_flrcion_mdre', '=', 'floracion.id_flrcion')
            ->select(
                'cruzamientos.*',
                DB::raw("COALESCE(
                    cruzamientos.ubccion_nvra, 
                    cruzamientos.id_actual_nvra, 
                    floracion.vivero
                ) AS vivero_plot")
            );

        // Búsqueda Global
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cruzamientos.vrdad_mdre', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.vrdad_pdre1', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.vrdad_pdre2', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.vrdad_pdre3', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.vrdad_pdre4', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.vrdad_pdre5', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.pdgree', 'ilike', '%' . $search . '%')
                  ->orWhere('cruzamientos.id_crzmnto', 'like', '%' . $search . '%')
                  ->orWhere('floracion.vivero', 'ilike', '%' . $search . '%');
            });
        }

        // Filtros por Columna (Tabla Dinámica) con lista blanca para prevenir SQL Injection
        $allowedFilterColumns = [
            'vrdad_mdre', 'vrdad_pdre1', 'vrdad_pdre2', 'vrdad_pdre3', 'vrdad_pdre4', 'vrdad_pdre5',
            'pdgree', 'proyecto', 'fcha_crzmnto', 'ano', 'obsrvcnes', 'orgen', 'nm_fmlias',
            'cngldor', 'ubccion_nvra', 'id_actual_nvra', 'grpo_crzmnto_mdre', 'grpo_crzmnto_pdre',
            'plntlas_ttles', 'plntlas_grmndas', 'plntlas_trrzas', 'plantas_finales_seleccion'
        ];
        if (!empty($filtersJson)) {
            $filters = json_decode($filtersJson, true);
            if (is_array($filters)) {
                foreach ($filters as $col => $val) {
                    if (!empty($val)) {
                        if ($col === 'padres') {
                            $query->where(function ($q) use ($val) {
                                $q->where('cruzamientos.vrdad_pdre1', 'ilike', '%' . $val . '%')
                                  ->orWhere('cruzamientos.vrdad_pdre2', 'ilike', '%' . $val . '%')
                                  ->orWhere('cruzamientos.vrdad_pdre3', 'ilike', '%' . $val . '%')
                                  ->orWhere('cruzamientos.vrdad_pdre4', 'ilike', '%' . $val . '%')
                                  ->orWhere('cruzamientos.vrdad_pdre5', 'ilike', '%' . $val . '%');
                            });
                        } else if ($col === 'id_crzmnto') {
                            $query->whereRaw('CAST(cruzamientos.id_crzmnto AS TEXT) ILIKE ?', ['%' . $val . '%']);
                        } else if ($col === 'vivero_plot') {
                            $query->where(function ($q) use ($val) {
                                $q->where('cruzamientos.ubccion_nvra', 'ilike', '%' . $val . '%')
                                  ->orWhere('cruzamientos.id_actual_nvra', 'ilike', '%' . $val . '%')
                                  ->orWhere('floracion.vivero', 'ilike', '%' . $val . '%');
                            });
                        } else if (in_array($col, $allowedFilterColumns, true)) {
                            $query->where('cruzamientos.' . $col, 'ilike', '%' . $val . '%');
                        }
                    }
                }
            }
        }

        $query->orderBy('cruzamientos.id_crzmnto', 'desc');
        
        return $query->paginate($perPage);
    }

    public function crossingInitialData()
    {
        return Projects::selectRaw('remote_pg_sipro.nm_prycto, remote_pg_sipro.cd_cntble, remote_pg_sipro.id_prycto, count(*) as numero')
            ->join('floracion', 'floracion.id_pr', '=', 'remote_pg_sipro.id_prycto')
            ->groupBy('remote_pg_sipro.id_prycto', 'remote_pg_sipro.nm_prycto', 'remote_pg_sipro.cd_cntble')
            ->havingRaw('count(*) > 0')
            ->whereBetween('floracion.fcha', [Carbon::yesterday(), Carbon::today()])
            ->where('floracion.estado', 0)
            ->get();
    }

    public function listarFlores($proyectos, $fechai, $fechaf)
    {
        $proyectosArray = explode(",", $proyectos);

        $floresMadre = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->whereIn('remote_pg_sipro.cd_cntble', $proyectosArray)
            ->whereIn('floracion.sxo', ['Hembra', 'HF', 'HD'])
            ->whereBetween('floracion.fcha', [$fechai, $fechaf])
            ->select('floracion.*')
            ->get();

        $floresPadre = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->whereIn('remote_pg_sipro.cd_cntble', $proyectosArray)
            ->whereIn('floracion.sxo', ['Macho', 'MF', 'MD'])
            ->whereBetween('floracion.fcha', [$fechai, $fechaf])
            ->select('floracion.*')
            ->get();

        return [
            "flores_madre" => $floresMadre,
            "flores_padre" => $floresPadre
        ];
    }

    public function parametizeWeightedCrossing($proyecto, $ambiente)
    {
        $ponderados = [];
        $sumaPonderados = 0;

        if ($proyecto === 'General' && !empty($ambiente)) {
            $count = DB::connection('sivar')->table('ponderados_valor_merito')
                ->where('id_proyecto', 'General')
                ->where('ambiente', $ambiente)
                ->count();
            if ($count == 0) {
                $base = DB::connection('sivar')->table('ponderados_valor_merito')
                    ->where('id_proyecto', '010105')
                    ->where('ambiente', $ambiente)
                    ->get();
                foreach ($base as $row) {
                    DB::connection('sivar')->table('ponderados_valor_merito')->insert([
                        'id_proyecto' => 'General',
                        'ambiente' => $ambiente,
                        'id_caracteristica' => $row->id_caracteristica,
                        'nivel' => $row->nivel,
                        'ponderado' => $row->ponderado
                    ]);
                }
            }
        }

        if ($proyecto != 'x') {
            $ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
                ->leftJoin('ponderados_valor_merito', function ($join) use ($proyecto, $ambiente) {
                    $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                        ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                        ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
                })
                ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.nombre', 'caracteristicas_valor_merito.id_caracteristica', 'caracteristicas_valor_merito.equivalente')
                ->get();

            $sumaPonderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
                ->leftJoin('ponderados_valor_merito', function ($join) use ($proyecto, $ambiente) {
                    $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                        ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                        ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
                })
                ->sum('ponderados_valor_merito.ponderado');
        }

        if ($sumaPonderados == 0) {
            $sumaPonderados = 1;
        }

        return [
            "proyectos" => $proyecto,
            "ponderados" => $ponderados,
            "suma_ponderados" => $sumaPonderados,
            "ambiente" => $ambiente
        ];
    }

    public function modifyFeatures($car, $proyecto, $nivel, $ponderado, $ambiente, $nuevo)
    {
        $caracteristica = \App\Models\PonderadoVM::where('id_caracteristica', $car)
            ->where('id_proyecto', $proyecto)
            ->where('ambiente', $ambiente)
            ->first();
            
        if (!$caracteristica) {
            $caracteristica = new \App\Models\PonderadoVM;
            $caracteristica->id_proyecto = $proyecto;
            $caracteristica->id_caracteristica = $car;
            $caracteristica->ambiente = $ambiente;
        }
        
        $caracteristica->nivel = $nivel;
        $caracteristica->ponderado = $ponderado;
        $caracteristica->save();
    }

    private static $evaluacionesCache = null;

    private function obtenerEvaluacion(string $tipoEvaluacion, string $caracteristica)
    {
        if (is_null(self::$evaluacionesCache)) {
            self::$evaluacionesCache = \App\Models\Evaluacion::with(['tipoEvaluacion', 'rangos'])->get();
        }

        return self::$evaluacionesCache->first(function ($eval) use ($tipoEvaluacion, $caracteristica) {
            return $eval->tipoEvaluacion->keyname === $tipoEvaluacion &&
                in_array($caracteristica, $eval->arrayCharacters ?? []);
        });
    }

    private function obtenerNivelEvaluacion($flor, string $caracteristica, $testigo, string $tipoEvaluacion = 'viabilidad')
    {
        $valor = $flor->$caracteristica ?? null;

        if (is_null($valor) || $valor === '') {
            return $tipoEvaluacion === 'viabilidad' ? 999 : 0;
        }

        $evaluacion = $this->obtenerEvaluacion($tipoEvaluacion, $caracteristica);

        if (!$evaluacion) {
            return $tipoEvaluacion === 'viabilidad' ? 999 : 0;
        }

        if (in_array($caracteristica, ['rya_cfe_r', 'roya_naranja'])) {
            return (float) $valor;
        }

        $requiereTestigo = in_array($caracteristica, [
            'tchm',
            'dmtro_tllo',
            'altura_planta',
            'poblacion',
            'scrsa'
        ]);

        if ($requiereTestigo) {
            if (is_null($testigo) || $testigo == 0) {
                return 0;
            }
            $valorAEvaluar = ($valor * 100) / $testigo;
        } else {
            $valorAEvaluar = (float) $valor;
        }

        return $evaluacion->obtenerCalificacion($valorAEvaluar);
    }
    public function enviarABolsaComun($variedad)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');
        $info = explode("_", $variedad);

        $flor = Flowering::where('floracion.id_pr', '=', $info[1])
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.id_crcter', '=', $info[2])
            ->where('floracion.bolsa_comun', '=', 0)
            ->where('floracion.vrdad', '=', $info[0])
            ->first();

        if ($flor) {
            $flor->bolsa_comun = 1;
            $flor->save();
            return ['status' => true, 'message' => 'Flower sent to common bag successfully'];
        }

        return ['status' => false, 'message' => 'Flower not found or already sent to common bag'];
    }

    public function enviarFlorAProyecto($variedad, $proyecto, $bolsa)
    {
        $var = explode("_", $variedad);
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::today()->subDays(60)->format('Y-m-d');

        // Resolve target project id_prycto if cd_cntble was passed
        $targetProj = DB::connection('sivar')->table('remote_pg_sipro')
            ->where('cd_cntble', $proyecto)
            ->orWhere('id_prycto', $proyecto)
            ->first();
        $targetIdPrycto = $targetProj ? $targetProj->id_prycto : $proyecto;

        $idPrOrigin = str_replace("9999", "", $var[1] ?? '');
        $idCrcter = $var[2] ?? null;
        $vrdadName = $var[0] ?? '';

        $query = DB::connection('sivar')->table('floracion')
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', '0')
            ->where('floracion.vrdad', '=', $vrdadName);

        if ($idCrcter) {
            $query->where('floracion.id_crcter', '=', $idCrcter);
        }

        if ((int)$bolsa === 1) {
            $query->where('floracion.bolsa_comun', 1);
        } else {
            if ($idPrOrigin !== '') {
                $query->where('floracion.id_pr', '=', $idPrOrigin);
            }
            $query->where('floracion.bolsa_comun', 0);
        }

        $flor = $query->first();

        if ($flor) {
            $id_flor = $flor->id_flrcion;
            DB::connection('sivar')->table('floracion')
                ->where('id_flrcion', '=', $id_flor)
                ->update(['id_pr' => $targetIdPrycto, 'bolsa_comun' => 0]);

            return ['status' => true, 'message' => 'Flor asignada al proyecto con éxito'];
        }

        return ['status' => false, 'message' => 'Flor no encontrada o ya asignada'];
    }

    public function devolverFlorABolsaComun($variedad, $proyecto)
    {
        $var = explode("_", $variedad);
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::today()->subDays(60)->format('Y-m-d');

        $targetProj = DB::connection('sivar')->table('remote_pg_sipro')
            ->where('cd_cntble', $proyecto)
            ->orWhere('id_prycto', $proyecto)
            ->first();
        $targetIdPrycto = $targetProj ? $targetProj->id_prycto : $proyecto;

        $idCrcter = $var[2] ?? null;
        $vrdadName = $var[0] ?? '';

        $query = DB::connection('sivar')->table('floracion')
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', '0')
            ->where('floracion.vrdad', '=', $vrdadName)
            ->where('floracion.id_pr', '=', $targetIdPrycto)
            ->where('floracion.bolsa_comun', 0);

        if ($idCrcter) {
            $query->where('floracion.id_crcter', '=', $idCrcter);
        }

        $flor = $query->first();

        if ($flor) {
            DB::connection('sivar')->table('floracion')
                ->where('id_flrcion', '=', $flor->id_flrcion)
                ->update(['bolsa_comun' => 1]);

            return ['status' => true, 'message' => 'Flor devuelta a la Bolsa Común con éxito'];
        }

        return ['status' => false, 'message' => 'No se encontró la flor para devolver'];
    }

    public function floresOtrosProyectos($proyectoActual)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::today()->subDays(60)->format('Y-m-d');

        $currentIdPrycto = null;
        if (!empty($proyectoActual)) {
            $currentProj = DB::connection('sivar')->table('remote_pg_sipro')
                ->where('cd_cntble', $proyectoActual)
                ->orWhere('id_prycto', $proyectoActual)
                ->first();
            $currentIdPrycto = $currentProj ? $currentProj->id_prycto : $proyectoActual;
        }

        $query = DB::connection('sivar')->table('floracion')
            ->leftJoin('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr')
            ->leftJoin('proyecto_caracteres', 'proyecto_caracteres.id', '=', 'floracion.id_crcter')
            ->whereBetween('floracion.fcha', [$fechai, $fechaf])
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 1);

        $flores = $query->groupBy(
                'floracion.vrdad',
                'floracion.sxo',
                'floracion.polen',
                'floracion.id_pr',
                'floracion.id_crcter',
                'floracion.bolsa_comun',
                'remote_pg_sipro.nm_prycto',
                'remote_pg_sipro.cd_cntble',
                'proyecto_caracteres.nombre'
            )
            ->select(DB::raw('
                floracion.vrdad,
                floracion.sxo,
                floracion.polen,
                floracion.id_pr,
                floracion.id_crcter,
                floracion.bolsa_comun,
                remote_pg_sipro.nm_prycto as nombre_proyecto,
                remote_pg_sipro.cd_cntble as codigo_proyecto,
                proyecto_caracteres.nombre as nombre_caracter,
                count(*) as cantidad
            '))
            ->orderBy('floracion.vrdad', 'asc')
            ->get();

        return $flores->map(function($f) {
            $idPrOrigin = $f->id_pr ?? '9999';
            $variedadKey = $f->vrdad . '_' . $idPrOrigin . '_' . $f->id_crcter;
            return [
                'vrdad' => $f->vrdad,
                'sxo' => $f->sxo,
                'polen' => $f->polen,
                'id_pr' => $f->id_pr,
                'codigo_proyecto' => $f->codigo_proyecto,
                'nombre_proyecto' => $f->bolsa_comun == 1 ? 'Bolsa Común' : ($f->nombre_proyecto ?? ('Proyecto ID: ' . $f->id_pr)),
                'bolsa_comun' => $f->bolsa_comun,
                'cantidad' => $f->cantidad,
                'id_crcter' => $f->id_crcter,
                'nombre_caracter' => $f->nombre_caracter,
                'variedad_key' => $variedadKey
            ];
        });
    }

    public function criteriosBancoGermoplasmaPorVariedad($variedad)
    {
        $sql = "SELECT avg(NULLIF(REPLACE(mosaico_p::text, ',', '.'), '')::numeric) mosaico, 
        avg(NULLIF(REPLACE(roya_cafe_r::text, ',', '.'), '')::numeric) roya_cafe, 
        avg(NULLIF(REPLACE(roya_naranja_r::text, ',', '.'), '')::numeric) roya_naranja, 
        avg(NULLIF(REPLACE(carbon_p::text, ',', '.'), '')::numeric) carbon,
        avg(NULLIF(REPLACE(tchm::text, ',', '.'), '')::numeric) tchm, 
        avg(NULLIF(REPLACE(diametro_tallo::text, ',', '.'), '')::numeric) diametro_tallo, 
        avg(NULLIF(REPLACE(volcamiento::text, ',', '.'), '')::numeric) volcamiento, 
        avg(NULLIF(REPLACE(altura_planta::text, ',', '.'), '')::numeric) altura_planta, 
        avg(NULLIF(REPLACE(poblacion_1m::text, ',', '.'), '')::numeric) poblacion, 
        avg(NULLIF(REPLACE(sacarosa::text, ',', '.'), '')::numeric) sacarosa,
        avg(NULLIF(REPLACE(dds::text, ',', '.'), '')::numeric) dds,
        avg(NULLIF(REPLACE(tubos::text, ',', '.'), '')::numeric) tubos,
        avg(NULLIF(REPLACE(raiz_nf::text, ',', '.'), '')::numeric) raiz_nf,
        avg(NULLIF(REPLACE(numero_entrenudos::text, ',', '.'), '')::numeric) numero_entrenudos,
        avg(NULLIF(REPLACE(longitud_entrenudo::text, ',', '.'), '')::numeric) longitud_entrenudo,
        avg(NULLIF(REPLACE(longitud_cogollo::text, ',', '.'), '')::numeric) longitud_cogollo,
        avg(NULLIF(REPLACE(longitud_hoja::text, ',', '.'), '')::numeric) longitud_hoja,
        avg(NULLIF(REPLACE(floracion_tllos::text, ',', '.'), '')::numeric) floracion_tallos,
        avg(NULLIF(REPLACE(floracion_p::text, ',', '.'), '')::numeric) floracion_p,
        avg(NULLIF(REPLACE(aspecto_planta::text, ',', '.'), '')::numeric) aspecto_planta,
        avg(NULLIF(REPLACE(aspecto_seleccion::text, ',', '.'), '')::numeric) aspecto_seleccion,
        avg(NULLIF(REPLACE(pelusa::text, ',', '.'), '')::numeric) pelusa,
        avg(NULLIF(REPLACE(deshoje::text, ',', '.'), '')::numeric) deshoje,
        avg(NULLIF(REPLACE(materia_seca::text, ',', '.'), '')::numeric) materia_seca,
        avg(NULLIF(REPLACE(humedad::text, ',', '.'), '')::numeric) humedad,
        avg(NULLIF(REPLACE(brix::text, ',', '.'), '')::numeric) brix,
        avg(NULLIF(REPLACE(fibra::text, ',', '.'), '')::numeric) fibra,
        avg(NULLIF(REPLACE(pureza::text, ',', '.'), '')::numeric) pureza,
        avg(NULLIF(REPLACE(are::text, ',', '.'), '')::numeric) are,
        avg(NULLIF(REPLACE(reductores::text, ',', '.'), '')::numeric) reductores,
        avg(NULLIF(REPLACE(atr::text, ',', '.'), '')::numeric) atr,
        avg(NULLIF(REPLACE(peso::text, ',', '.'), '')::numeric) peso,
        avg(NULLIF(REPLACE(tch::text, ',', '.'), '')::numeric) tch,
        avg(NULLIF(REPLACE(tah::text, ',', '.'), '')::numeric) tah,
        avg(NULLIF(REPLACE(tsh::text, ',', '.'), '')::numeric) tsh,
        avg(NULLIF(REPLACE(tahm::text, ',', '.'), '')::numeric) tahm,
        avg(NULLIF(REPLACE(tshm::text, ',', '.'), '')::numeric) tshm,
        avg(NULLIF(REPLACE(lsdte::text, ',', '.'), '')::numeric) lsdte,
        avg(NULLIF(REPLACE(lsdtt::text, ',', '.'), '')::numeric) lsdtt,
        avg(NULLIF(REPLACE(lsdtv::text, ',', '.'), '')::numeric) lsdtv,
        avg(NULLIF(REPLACE(lsdt::text, ',', '.'), '')::numeric) lsdt,
        avg(NULLIF(REPLACE(rsd::text, ',', '.'), '')::numeric) rsd,
        avg(NULLIF(REPLACE(sclyv::text, ',', '.'), '')::numeric) sclyv,
        avg(NULLIF(REPLACE(rajadura_inc::text, ',', '.'), '')::numeric) rajadura_inc,
        avg(NULLIF(REPLACE(entrenudos_tallo::text, ',', '.'), '')::numeric) entrenudos_tallo,
        avg(NULLIF(REPLACE(entrenudos_rajados::text, ',', '.'), '')::numeric) entrenudos_rajados,
        avg(NULLIF(REPLACE(hojas_erectas::text, ',', '.'), '')::numeric) hojas_erectas,
        avg(NULLIF(REPLACE(raices_tallos::text, ',', '.'), '')::numeric) raices_tallos,
        avg(NULLIF(REPLACE(yemas_protuberantes::text, ',', '.'), '')::numeric) yemas_protuberantes,
        avg(NULLIF(REPLACE(medula::text, ',', '.'), '')::numeric) medula,
        avg(NULLIF(REPLACE(habito_de_crecimiento::text, ',', '.'), '')::numeric) habito_de_crecimiento,
        avg(NULLIF(REPLACE(germinacion::text, ',', '.'), '')::numeric) germinacion,
        avg(NULLIF(REPLACE(tolerancia_herbicida::text, ',', '.'), '')::numeric) tolerancia_herbicida,
        avg(NULLIF(REPLACE(raices_adventicias::text, ',', '.'), '')::numeric) raices_adventicias
    FROM public.\"caracterizacion_banco_germoplasma\"";

        $bg = DB::select($sql . " WHERE true");

        $bg_var = DB::select($sql . " WHERE variedad = ?", [$variedad]);

        $criterios = "";

        if (!empty($bg) && !empty($bg_var)) {
            foreach ($bg[0] as $key => $c) {
                if ($bg[0]->$key !== null && $bg[0]->$key !== "" && $bg_var[0]->$key !== null && $bg_var[0]->$key !== "") {
                    if ($bg[0]->$key > $bg_var[0]->$key) {
                        $criterios .= "<text style='color:red;'>" . str_replace("_", " ", strtoupper($key)) . " <i class='fa fa-arrow-down'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</text>";
                    } else {
                        $criterios .= "<text style='color:green;'>" . str_replace("_", " ", strtoupper($key)) . "  <i class='fa fa-arrow-up'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</text>";
                    }
                }
            }
        }

        return $criterios;
    }

    public function proyectosConFlores($modelName, $term, $id, $text)
    {
        $allowedModels = ['Projects'];
        $allowedColumns = ['id_prycto', 'cd_cntble', 'nm_prycto'];

        if (!in_array($modelName, $allowedModels, true) || !in_array($id, $allowedColumns, true) || !in_array($text, $allowedColumns, true)) {
            return null;
        }

        $modelClass = "\\App\\Models\\" . $modelName;

        if (!class_exists($modelClass)) {
            return null;
        }

        $formattedTags = [];

        $tags = $modelClass::selectRaw('remote_pg_sipro.nm_prycto, remote_pg_sipro.cd_cntble, remote_pg_sipro.id_prycto, count(*)')
            ->join('floracion', 'floracion.id_pr', '=', 'remote_pg_sipro.id_prycto')
            ->groupBy('remote_pg_sipro.id_prycto', 'remote_pg_sipro.nm_prycto', 'remote_pg_sipro.cd_cntble')
            ->havingRaw('count(*) > 0')
            ->whereBetween('floracion.fcha', [Carbon::today()->subDays(1), Carbon::today()])
            ->where(function ($query) use ($id, $text, $term) {
                $query->where($id, 'ILIKE', '%' . $term . '%')
                    ->orWhere($text, 'ILIKE', '%' . $term . '%');
            })
            ->get();

        foreach ($tags as $tag) {
            $formattedTags[] = ['id' => $tag->$id, 'text' => ($tag->$id . " - " . $tag->$text)];
        }

        return $formattedTags;
    }
}
