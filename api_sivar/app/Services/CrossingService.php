<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Vivero;
use App\Models\Projects;
use Carbon\Carbon;
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
                    'proyecto' => $florA->id_pr ?? null,
                    'proyecto2' => $florB->id_pr ?? null,
                    'caracter' => $florA->id_crcter ?? null,
                    'caracter2' => $florB->id_crcter ?? null,
                    'nombre_proyecto' => $florA->nombre_proyecto ?? null,
                    'nombre_proyecto2' => $florB->nombre_proyecto ?? null,
                    'id_caracter' => $florA->id_caracter ?? null,
                    'id_caracter2' => $florB->id_caracter ?? null
                );
                $vm = 0;
                $vm2 = 0;

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
                        $isDisease = in_array($caracteristica, ['msco_r', 'carbon', 'rya_cfe_r', 'roya_naranja']);
                        $hasA = $florA_eval && isset($florA_eval->$caracteristica) && $florA_eval->$caracteristica !== '' && !is_null($florA_eval->$caracteristica);
                        $hasT = $isDisease || ($testigo != null && isset($testigo->$caracteristica) && $testigo->$caracteristica !== '' && !is_null($testigo->$caracteristica));
                        if ($hasT) {
                            $testigoVal = ($testigo != null && isset($testigo->$caracteristica)) ? $testigo->$caracteristica : null;
                            
                            $hasA = $florA_eval && isset($florA_eval->$caracteristica) && $florA_eval->$caracteristica !== '' && !is_null($florA_eval->$caracteristica);
                            if ($hasA) {
                                $vm += ($this->calcularValorMerito($caracteristica, $florA_eval, $ponderado->ponderado, $testigoVal)) / 100;
                            }
                            
                            $hasB = $florB_eval && isset($florB_eval->$caracteristica) && $florB_eval->$caracteristica !== '' && !is_null($florB_eval->$caracteristica);
                            if ($hasB) {
                                $vm2 += ($this->calcularValorMerito($caracteristica, $florB_eval, $ponderado->ponderado, $testigoVal)) / 100;
                            }

                            if ($hasA && $hasB) {
                                if (!$this->calcularViabilidadCaracteristica($caracteristica, $florA_eval, $florB_eval, $ponderado, $testigoVal)) {
                                    $viabilidad['viabilidad'] = false;
                                }
                            }
                        }
                    }
                }

                // Otras condiciones 
                if (($florB->sxo == "Hembra" || $florB->sxo == "HD" || $florB->sxo == "HF")) {
                    $viabilidad['viabilidad'] = false;
                }
                if (($florA->sxo == "Macho" || $florA->sxo == "MD" || $florA->sxo == "MF")) {
                    $viabilidad['viabilidad'] = false;
                }

                $viabilidad['vm'] = round($vm, 2);
                $viabilidad['vm2'] = round($vm2, 2);
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
    public function generateMatrix($proy, $proyecto, $testigo, $ambiente = 'Semiseco')
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');

        $proyectos = explode(",", $proy);

        $ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin(DB::raw('(SELECT ponderados_valor_merito.* FROM ponderados_valor_merito JOIN remote_pg_sipro ON ponderados_valor_merito.id_proyecto = remote_pg_sipro.cd_cntble ) ponderados_valor_merito'), function ($join) use ($proyecto, $ambiente) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();

        $flores_BG = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftJoin('caracterizacion_banco_germoplasma', function ($join) {
                $join->on('caracterizacion_banco_germoplasma.variedad', '=', 'floracion.vrdad');
            })
            ->whereIn('remote_pg_sipro.cd_cntble', $proyectos)
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.polen")
            ->select(DB::raw('"floracion"."vrdad", 
                        "floracion"."sxo", 
                        "floracion"."polen",
                        avg(CAST(REPLACE(CAST(mosaico_p AS TEXT), \',\', \'.\') AS FLOAT)) msco_r, 
                        avg(CAST(REPLACE(CAST(roya_cafe_r AS TEXT), \',\', \'.\') AS FLOAT)) rya_cfe_r, 
                        avg(CAST(REPLACE(CAST(roya_naranja_r AS TEXT), \',\', \'.\') AS FLOAT)) roya_naranja, 
                        avg(CAST(REPLACE(CAST(carbon_p AS TEXT), \',\', \'.\') AS FLOAT)) carbon,
                        avg(CAST(REPLACE(CAST(tchm AS TEXT), \',\', \'.\') AS FLOAT)) tchm, 
                        avg(CAST(REPLACE(CAST(diametro_tallo AS TEXT), \',\', \'.\') AS FLOAT)) dmtro_tllo, 
                        avg(CAST(REPLACE(CAST(volcamiento AS TEXT), \',\', \'.\') AS FLOAT)) volcamiento, 
                        avg(CAST(REPLACE(CAST(altura_planta AS TEXT), \',\', \'.\') AS FLOAT)) altura_planta, 
                        avg(CAST(REPLACE(CAST(poblacion_1m AS TEXT), \',\', \'.\') AS FLOAT)) poblacion, 
                        avg(CAST(REPLACE(CAST(sacarosa AS TEXT), \',\', \'.\') AS FLOAT)) scrsa'))
            ->distinct()
            ->orderBy('floracion.vrdad')
            ->get();

        $flores_PR = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftJoin('datos_campo_crudos', function ($join) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', 5);
            })
            ->whereIn('remote_pg_sipro.cd_cntble', $proyectos)
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->groupBy('floracion.vrdad', "floracion.sxo")
            ->select(DB::raw('"floracion"."vrdad", 
                        "floracion"."sxo", 
                        avg(CAST(REPLACE(CAST(polen AS TEXT), \',\', \'.\') AS FLOAT)) polen, 
                        avg(CAST(REPLACE(CAST(mosaico AS TEXT), \',\', \'.\') AS FLOAT)) msco_r, 
                        avg(CAST(REPLACE(CAST(roya AS TEXT), \',\', \'.\') AS FLOAT)) rya_cfe_r, 
                        avg(CAST(REPLACE(CAST("179" AS TEXT), \',\', \'.\') AS FLOAT)) roya_naranja, 
                        avg(CAST(REPLACE(CAST(carbon AS TEXT), \',\', \'.\') AS FLOAT)) carbon, 
                        avg(CAST(REPLACE(CAST("173" AS TEXT), \',\', \'.\') AS FLOAT)) tchm, 
                        avg(CAST(REPLACE(CAST("163" AS TEXT), \',\', \'.\') AS FLOAT)) scrsa, 
                        avg(CAST(REPLACE(CAST("Cepas afectadas Carbón" AS TEXT), \',\', \'.\') AS FLOAT)) volcamiento,
                        avg(CAST(REPLACE(CAST("Tallo Altura (cm)" AS TEXT), \',\', \'.\') AS FLOAT)) altura_planta, 
                        avg(CAST(REPLACE(CAST("Población (m)" AS TEXT), \',\', \'.\') AS FLOAT)) poblacion, 
                        avg(CAST(REPLACE(CAST("DiametroTallo" AS TEXT), \',\', \'.\') AS FLOAT)) dmtro_tllo'))
            ->distinct()
            ->orderBy('floracion.vrdad')
            ->get();

        $flores_EIII = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftJoin('datos_campo_crudos', function ($join) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', 3);
            })
            ->whereIn('remote_pg_sipro.cd_cntble', $proyectos)
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->groupBy('floracion.vrdad', "floracion.sxo")
            ->select(DB::raw('"floracion"."vrdad", 
                        "floracion"."sxo", 
                        avg(CAST(REPLACE(CAST(polen AS TEXT), \',\', \'.\') AS FLOAT)) polen, 
                        avg(CAST(REPLACE(CAST(mosaico AS TEXT), \',\', \'.\') AS FLOAT)) msco_r, 
                        avg(CAST(REPLACE(CAST(roya AS TEXT), \',\', \'.\') AS FLOAT)) rya_cfe_r, 
                        avg(CAST(REPLACE(CAST("179" AS TEXT), \',\', \'.\') AS FLOAT)) roya_naranja, 
                        avg(CAST(REPLACE(CAST(carbon AS TEXT), \',\', \'.\') AS FLOAT)) carbon, 
                        avg(CAST(REPLACE(CAST("173" AS TEXT), \',\', \'.\') AS FLOAT)) tchm, 
                        avg(CAST(REPLACE(CAST("163" AS TEXT), \',\', \'.\') AS FLOAT)) scrsa, 
                        avg(CAST(REPLACE(CAST("Cepas afectadas Carbón" AS TEXT), \',\', \'.\') AS FLOAT)) volcamiento,
                        avg(CAST(REPLACE(CAST("Tallo Altura (cm)" AS TEXT), \',\', \'.\') AS FLOAT)) altura_planta, 
                        avg(CAST(REPLACE(CAST("Población (m)" AS TEXT), \',\', \'.\') AS FLOAT)) poblacion, 
                        avg(CAST(REPLACE(CAST("DiametroTallo" AS TEXT), \',\', \'.\') AS FLOAT)) dmtro_tllo'))
            ->distinct()
            ->orderBy('floracion.vrdad')
            ->get();

        $variedad_testigo = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
            ->select(DB::raw('"variedad", 
                        avg(CAST(REPLACE(CAST(mosaico_p AS TEXT), \',\', \'.\') AS FLOAT)) msco_r, 
                        avg(CAST(REPLACE(CAST(roya_cafe_r AS TEXT), \',\', \'.\') AS FLOAT)) rya_cfe_r, 
                        avg(CAST(REPLACE(CAST(roya_naranja_r AS TEXT), \',\', \'.\') AS FLOAT)) roya_naranja, 
                        avg(CAST(REPLACE(CAST(carbon_p AS TEXT), \',\', \'.\') AS FLOAT)) carbon, 
                        avg(CAST(REPLACE(CAST(tchm AS TEXT), \',\', \'.\') AS FLOAT)) tchm, 
                        avg(CAST(REPLACE(CAST(diametro_tallo AS TEXT), \',\', \'.\') AS FLOAT)) dmtro_tllo, 
                        avg(CAST(REPLACE(CAST(volcamiento AS TEXT), \',\', \'.\') AS FLOAT)) volcamiento,
                        avg(CAST(REPLACE(CAST(altura_planta AS TEXT), \',\', \'.\') AS FLOAT)) altura_planta, 
                        avg(CAST(REPLACE(CAST(poblacion_1m AS TEXT), \',\', \'.\') AS FLOAT)) poblacion, 
                        avg(CAST(REPLACE(CAST(sacarosa AS TEXT), \',\', \'.\') AS FLOAT)) scrsa'))
            ->where('variedad', '=', $testigo)
            ->groupBy("variedad")
            ->first();

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

        $proyectos = Projects::selectRaw('remote_pg_sipro.nm_prycto,remote_pg_sipro.cd_cntble,remote_pg_sipro.id_prycto, count(*) as numero')
            ->join('floracion', 'floracion.id_pr', '=', 'remote_pg_sipro.id_prycto')
            ->groupBy('remote_pg_sipro.id_prycto', 'remote_pg_sipro.nm_prycto', 'remote_pg_sipro.cd_cntble')
            ->havingRaw('count(*)> 0')
            //Restricción a dos últimos días
            ->whereBetween('floracion.fcha', [Carbon::yesterday(), Carbon::today()])
            ->where('floracion.estado', '=', 0)
            ->select('id_prycto', 'cd_cntble')
            ->get();

        $flores = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            // ->whereIn('remote_pg_sipro.cd_cntble', $proyectos )
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))
            ->groupBy('floracion.vrdad', 'floracion.id_pr', 'floracion.id_crcter')
            //->orderBy('floracion.sxo', 'asc')
            ->orderBy('floracion.vrdad', 'desc')
            ->get();
        $ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin(DB::raw('(SELECT ponderados_valor_merito.* FROM ponderados_valor_merito JOIN remote_pg_sipro ON ponderados_valor_merito.id_proyecto = remote_pg_sipro.cd_cntble ) ponderados_valor_merito'), function ($join) use ($proyecto, $ambiente) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();

        $ambiente_sitio = "";
        $ambiente_estados = "";

        if ($ambiente == 'Semiseco' || $ambiente == 'Seco Semiseco' || $ambiente == 'Seco-Semiseco') {
            $ambiente_sitio = 'Seco-Semiseco';
            $ambiente_estados = 'Caracterizacion 2005';
        } else if ($ambiente == 'Humedo') {
            $ambiente_sitio = 'Humedo';
            $ambiente_estados = 'Caracterizacion 2005';
        } else {
            $ambiente_sitio = 'Piedemonte';
            $ambiente_estados = 'Caracterizacion 2014';
        }

        $viabilidad = array();
        // $distancias = array();
        $distancias = $this->obtenerDistanciaGeneticaConjunto($flores);

        foreach ($proyectos as $key => $proyecto) {
            $flores_BG = DB::connection('sivar')->table('floracion')
                ->leftjoin('remote_pg_sipro', function ($join) {
                    $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
                })
                ->leftJoin('caracterizacion_banco_germoplasma', function ($join) use ($ambiente_sitio, $ambiente_estados) {
                    $join->on('caracterizacion_banco_germoplasma.variedad', '=', 'floracion.vrdad');
                })
                ->leftjoin('caracteres', function ($join) {
                    $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
                })
                ->where('remote_pg_sipro.id_prycto', $proyecto->id_prycto)
                ->whereBetween('floracion.fcha', array($fechai, $fechaf))
                ->where('floracion.estado', '=', 0)
                ->where('floracion.bolsa_comun', '=', 0)
                ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.polen", "floracion.id_pr", "caracteres.id_crcter", "caracteres.nmbre_crcter", "remote_pg_sipro.nm_prycto")
                ->select(DB::raw('"floracion"."vrdad", 
                                        "floracion"."sxo", 
                                        "floracion"."polen",
                                        "floracion"."id_pr",
                                        "caracteres"."nmbre_crcter" as id_crcter,
                                        "caracteres"."id_crcter" as id_caracter,
                                        "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                                        avg(mosaico_p::float) msco_r, 
                                        avg(roya_cafe_r::float) rya_cfe_r, 
                                        avg(roya_naranja_r::float) roya_naranja, 
                                        avg(carbon_p::float) carbon,
                                        avg(tchm::float) tchm, 
                                        avg(diametro_tallo::float) dmtro_tllo, 
                                        avg(volcamiento::float) volcamiento, 
                                        avg(altura_planta::float) altura_planta, 
                                        avg(poblacion_1m::float) poblacion, 
                                        avg(sacarosa::float) scrsa'))
                ->distinct()
                ->orderBy('floracion.polen')
                ->get();

            $flores_PR = DB::connection('sivar')->table('floracion')
                ->join('remote_pg_sipro', function ($join) {
                    $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
                })
                ->leftJoin('datos_campo_crudos', function ($join) {
                    $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                        ->where('datos_campo_crudos.estdo_slccion', '=', 5);
                })
                ->leftjoin('caracteres', function ($join) {
                    $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
                })
                ->where('remote_pg_sipro.id_prycto', $proyecto->id_prycto)
                ->whereBetween('floracion.fcha', array($fechai, $fechaf))
                ->where('floracion.estado', '=', 0)
                ->where('floracion.bolsa_comun', '=', 0)
                ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.id_pr", "caracteres.nmbre_crcter", "caracteres.id_crcter", "floracion.polen", "remote_pg_sipro.nm_prycto")
                ->select(DB::raw('"floracion"."vrdad", 
                                            "floracion"."sxo", 
                                            "floracion"."id_pr",
                                            "caracteres"."nmbre_crcter" as id_crcter,
                                            "caracteres"."id_crcter" as id_caracter,
                                            "floracion"."polen",
                                            "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                                            avg(mosaico::float) msco_r, 
                                            avg(roya::float) rya_cfe_r, 
                                            avg("179"::float) roya_naranja, 
                                            avg(carbon::float) carbon, 
                                            avg("173"::float) tchm, 
                                            avg("163"::float) scrsa, 
                                            avg("Cepas afectadas Carbón"::float) volcamiento,
                                            avg("Tallo Altura (cm)"::float) altura_planta, 
                                            avg("Población (m)"::float) poblacion, 
                                            avg("DiametroTallo"::float) dmtro_tllo'))
                ->distinct()
                ->orderBy('floracion.polen')
                ->get();

            $flores_EIII = DB::connection('sivar')->table('floracion')
                ->join('remote_pg_sipro', function ($join) {
                    $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
                })
                ->leftJoin('datos_campo_crudos', function ($join) {
                    $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                        ->where('datos_campo_crudos.estdo_slccion', '=', 3);
                })
                ->leftjoin('caracteres', function ($join) {
                    $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
                })
                ->where('remote_pg_sipro.id_prycto', $proyecto->id_prycto)
                ->whereBetween('floracion.fcha', array($fechai, $fechaf))
                ->where('floracion.estado', '=', 0)
                ->where('floracion.bolsa_comun', '=', 0)
                ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.id_pr", "caracteres.id_crcter", "caracteres.nmbre_crcter", "floracion.polen", "remote_pg_sipro.nm_prycto")
                ->select(DB::raw('"floracion"."vrdad", 
                                            "floracion"."sxo", 
                                            "floracion"."id_pr",
                                            "caracteres"."nmbre_crcter" as id_crcter,
                                            "caracteres"."id_crcter" as id_caracter,
                                            "floracion"."polen",
                                            "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                                            avg(mosaico::float) msco_r, 
                                            avg(roya::float) rya_cfe_r, 
                                            avg("179"::float) roya_naranja, 
                                            avg(carbon::float) carbon, 
                                            avg("173"::float) tchm, 
                                            avg("163"::float) scrsa, 
                                            avg("Cepas afectadas Carbón"::float) volcamiento,
                                            avg("Tallo Altura (cm)"::float) altura_planta, 
                                            avg("Población (m)"::float) poblacion, 
                                            avg("DiametroTallo"::float) dmtro_tllo'))
                ->distinct()
                ->orderBy('floracion.polen')
                ->get();

            $variedad_testigo = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
                ->select(DB::raw('"variedad", 
                                            avg(mosaico_p::float) msco_r, 
                                            avg( roya_cafe_r::float) rya_cfe_r, 
                                            avg(roya_naranja_r::float) roya_naranja, 
                                            avg(carbon_p::float) carbon, 
                                            avg(tchm::float) tchm, 
                                            avg(diametro_tallo::float) dmtro_tllo, 
                                            avg(volcamiento::float) volcamiento,
                                            avg(altura_planta::float) altura_planta, 
                                            avg(poblacion_1m::float) poblacion, 
                                            avg(sacarosa::float) scrsa'))
                ->where('variedad', '=', $testigo)
                ->groupBy("variedad")
                ->first();


            $arreglo = $this->calcularViabilidad($flores_BG, $flores_PR, $flores_EIII, $ponderados, $variedad_testigo);


            array_push($viabilidad, $arreglo);
            //echo var_dump($arreglo)."<br>";
        }

        return response()->json([
            'proyectos' => $proy,
            'proyecto' => $proyecto,
            'fecha_i' => $fechai,
            'fecha_f' => $fechaf,
            'flores' => $flores,
            'testigo' => $testigo,
            'viabilidades' => $viabilidad,
            'distancias' => $distancias,
            'ambiente' => $ambiente,
            'proyectos' => $proyectos
        ]);
    }
    public function sugerenciasCruzamientosBolsaComun($proy, $proyecto, $testigo, $ambiente)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');

        $proyectos = Projects::selectRaw('remote_pg_sipro.nm_prycto,remote_pg_sipro.cd_cntble,remote_pg_sipro.id_prycto, count(*) as numero')
            ->join('floracion', 'floracion.id_pr', '=', 'remote_pg_sipro.id_prycto')
            ->groupBy('remote_pg_sipro.id_prycto', 'remote_pg_sipro.nm_prycto', 'remote_pg_sipro.cd_cntble')
            ->havingRaw('count(*)> 0')
            //Restricción a dos últimos días
            ->whereBetween('floracion.fcha', [Carbon::yesterday(), Carbon::today()])
            //->where($id, 'ILIKE', '%'.$term.'%')->orWhere($text, 'ILIKE', '%'.$term.'%')
            ->get();
        $flores = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            //->whereIn('remote_pg_sipro.cd_cntble', $proyectos )
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 1)
            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))
            ->groupBy('floracion.vrdad', 'floracion.id_pr', 'floracion.id_crcter')
            //->orderBy('floracion.sxo', 'asc')
            ->orderBy('floracion.vrdad', 'desc')
            ->get();

        $ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin(DB::raw('(SELECT ponderados_valor_merito.* FROM ponderados_valor_merito JOIN remote_pg_sipro ON ponderados_valor_merito.id_proyecto = remote_pg_sipro.cd_cntble ) ponderados_valor_merito'), function ($join) use ($proyecto, $ambiente) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();;

        $ambiente_sitio = "";
        $ambiente_estados = "";

        if ($ambiente == "Semiseco") {
            $ambiente_sitio = "Seco-Semiseco";
            $ambiente_estados = "Caracterizacion 2005";
        } else if ($ambiente == "Humedo") {
            $ambiente_sitio = "Humedo";
            $ambiente_estados = "Caracterizacion 2005";
        } else {
            $ambiente_sitio = "Piedemonte";
            $ambiente_estados = "Caracterizacion 2014";
        }

        $flores_BG = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->leftJoin('caracterizacion_banco_germoplasma', function ($join) use ($ambiente_sitio, $ambiente_estados) {
                $join->on('caracterizacion_banco_germoplasma.variedad', '=', 'floracion.vrdad');
            })
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 1)
            //->where('caracterizacion_banco_germoplasma.sitio_seleccion', '=', $ambiente_sitio)
            //->where('caracterizacion_banco_germoplasma.estado_seleccion', '=', $ambiente_estados)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.polen", "floracion.id_pr", "caracteres.id_crcter", "caracteres.nmbre_crcter", "remote_pg_sipro.nm_prycto")
            ->select(DB::raw('"floracion"."vrdad", 
                        "floracion"."sxo", 
                        "floracion"."polen",
                        "floracion"."id_pr",
                        "caracteres"."nmbre_crcter" as id_crcter,
                        "caracteres"."id_crcter" as id_caracter,
                        "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                        avg(mosaico_p::float) msco_r, 
                        avg(roya_cafe_r::float) rya_cfe_r, 
                        avg(roya_naranja_r::float) roya_naranja, 
                        avg(carbon_p::float) carbon,
                        avg(tchm::float) tchm, 
                        avg(diametro_tallo::float) dmtro_tllo, 
                        avg(volcamiento::float) volcamiento, 
                        avg(altura_planta::float) altura_planta, 
                        avg(poblacion_1m::float) poblacion, 
                        avg(sacarosa::float) scrsa'))
            ->distinct()
            ->orderBy('floracion.polen')
            ->get();

        $flores_PR = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->leftJoin('datos_campo_crudos', function ($join) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', 5);
            })
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 1)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.id_pr", "caracteres.nmbre_crcter", "caracteres.id_crcter", "floracion.polen", "remote_pg_sipro.nm_prycto")
            ->select(DB::raw('"floracion"."vrdad", 
                            "floracion"."sxo", 
                            "floracion"."id_pr",
                            "caracteres"."nmbre_crcter" as id_crcter,
                            "caracteres"."id_crcter" as id_caracter,
                            "floracion"."polen",
                            "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                            avg(mosaico::float) msco_r, 
                            avg(roya::float) rya_cfe_r, 
                            avg("179"::float) roya_naranja, 
                            avg(carbon::float) carbon, 
                            avg("173"::float) tchm, 
                            avg("163"::float) scrsa, 
                            avg("Cepas afectadas Carbón"::float) volcamiento,
                            avg("Tallo Altura (cm)"::float) altura_planta, 
                            avg("Población (m)"::float) poblacion, 
                            avg("DiametroTallo"::float) dmtro_tllo'))
            ->distinct()
            ->orderBy('floracion.polen')
            ->get();

        $flores_EIII = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftJoin('datos_campo_crudos', function ($join) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', 3);
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 1)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.id_pr", "caracteres.nmbre_crcter", "caracteres.id_crcter", "floracion.polen", "remote_pg_sipro.nm_prycto")
            ->select(DB::raw('"floracion"."vrdad", 
                            "floracion"."sxo", 
                            "floracion"."id_pr",
                            "caracteres"."nmbre_crcter" as id_crcter,
                            "caracteres"."id_crcter" as id_caracter,
                            "floracion"."polen",
                            "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                            avg(mosaico::float) msco_r, 
                            avg(roya::float) rya_cfe_r, 
                            avg("179"::float) roya_naranja, 
                            avg(carbon::float) carbon, 
                            avg("173"::float) tchm, 
                            avg("163"::float) scrsa, 
                            avg("Cepas afectadas Carbón"::float) volcamiento,
                            avg("Tallo Altura (cm)"::float) altura_planta, 
                            avg("Población (m)"::float) poblacion, 
                            avg("DiametroTallo"::float) dmtro_tllo'))
            ->distinct()
            ->orderBy('floracion.polen')
            ->get();

        $variedad_testigo = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
            ->select(DB::raw('"variedad", 
                            avg(mosaico_p::float) msco_r, 
                            avg( roya_cafe_r::float) rya_cfe_r, 
                            avg(roya_naranja_r::float) roya_naranja, 
                            avg(carbon_p::float) carbon, 
                            avg(tchm::float) tchm, 
                            avg(diametro_tallo::float) dmtro_tllo, 
                            avg(volcamiento::float) volcamiento,
                            avg(altura_planta::float) altura_planta, 
                            avg(poblacion_1m::float) poblacion, 
                            avg(sacarosa::float) scrsa'))
            ->where('variedad', '=', $testigo)
            ->groupBy("variedad")
            ->first();


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
    public function suggestionCrossingsPerProject($proy, $proyecto, $testigo, $ambiente)
    {
        $fechaf = Carbon::today()->format('Y-m-d');
        $fechai = Carbon::yesterday()->format('Y-m-d');


        $flores = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->where('remote_pg_sipro.id_prycto', $proy)
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->select(DB::raw('count(*) as numero, floracion.vrdad, floracion.id_pr, floracion.id_crcter'))
            ->groupBy('floracion.vrdad', 'floracion.id_pr', 'floracion.id_crcter')
            //->orderBy('floracion.sxo', 'asc')
            ->orderBy('floracion.vrdad', 'desc')
            ->get();

        $ponderados = DB::connection('sivar')->table('caracteristicas_valor_merito')
            ->leftJoin(DB::raw('(SELECT ponderados_valor_merito.* FROM ponderados_valor_merito JOIN remote_pg_sipro ON ponderados_valor_merito.id_proyecto = remote_pg_sipro.cd_cntble ) ponderados_valor_merito'), function ($join) use ($proyecto, $ambiente) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', '=', $proyecto)
                    ->where('ponderados_valor_merito.ambiente', '=', $ambiente);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.equivalente', 'caracteristicas_valor_merito.equivalente_estados')
            ->get();;

        $ambiente_sitio = "";
        $ambiente_estados = "";

        if ($ambiente == "Semiseco" || $ambiente == "Seco Semiseco" || $ambiente == "Seco-Semiseco") {
            $ambiente_sitio = "Seco-Semiseco";
            $ambiente_estados = "Caracterizacion 2005";
        } else if ($ambiente == "Humedo") {
            $ambiente_sitio = "Humedo";
            $ambiente_estados = "Caracterizacion 2005";
        } else {
            $ambiente_sitio = "Piedemonte";
            $ambiente_estados = "Caracterizacion 2014";
        }


        $flores_BG = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->leftJoin('caracterizacion_banco_germoplasma', function ($join) use ($ambiente_sitio, $ambiente_estados) {
                $join->on('caracterizacion_banco_germoplasma.variedad', '=', 'floracion.vrdad');
            })
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->where('remote_pg_sipro.id_prycto', $proy)
            //->where('caracterizacion_banco_germoplasma.sitio_seleccion', '=', $ambiente_sitio)
            //->where('caracterizacion_banco_germoplasma.estado_seleccion', '=', $ambiente_estados)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.polen", "floracion.id_pr", "caracteres.id_crcter", "caracteres.nmbre_crcter", "remote_pg_sipro.nm_prycto")
            ->select(DB::raw('"floracion"."vrdad", 
                    "floracion"."sxo", 
                    "floracion"."polen",
                    "floracion"."id_pr",
                    "caracteres"."nmbre_crcter" as id_crcter,
                    "caracteres"."id_crcter" as id_caracter,
                    "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                    avg(mosaico_p::float) msco_r, 
                    avg(roya_cafe_r::float) rya_cfe_r, 
                    avg(roya_naranja_r::float) roya_naranja, 
                    avg(carbon_p::float) carbon,
                    avg(tchm::float) tchm, 
                    avg(diametro_tallo::float) dmtro_tllo, 
                    avg(volcamiento::float) volcamiento, 
                    avg(altura_planta::float) altura_planta, 
                    avg(poblacion_1m::float) poblacion, 
                    avg(sacarosa::float) scrsa'))
            ->distinct()
            ->orderBy('floracion.polen')
            ->get();

        $flores_PR = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->leftJoin('datos_campo_crudos', function ($join) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', 5);
            })
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->where('remote_pg_sipro.id_prycto', $proy)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.id_pr", "caracteres.nmbre_crcter", "caracteres.id_crcter", "floracion.polen", "remote_pg_sipro.nm_prycto")
            ->select(DB::raw('"floracion"."vrdad", 
                        "floracion"."sxo", 
                        "floracion"."id_pr",
                        "caracteres"."nmbre_crcter" as id_crcter,
                        "caracteres"."id_crcter" as id_caracter,
                        "floracion"."polen",
                        "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                        avg(mosaico::float) msco_r, 
                        avg(roya::float) rya_cfe_r, 
                        avg("179"::float) roya_naranja, 
                        avg(carbon::float) carbon, 
                        avg("173"::float) tchm, 
                        avg("163"::float) scrsa, 
                        avg("Cepas afectadas Carbón"::float) volcamiento,
                        avg("Tallo Altura (cm)"::float) altura_planta, 
                        avg("Población (m)"::float) poblacion, 
                        avg("DiametroTallo"::float) dmtro_tllo'))
            ->distinct()
            ->orderBy('floracion.polen')
            ->get();

        $flores_EIII = DB::connection('sivar')->table('floracion')
            ->join('remote_pg_sipro', function ($join) {
                $join->on('remote_pg_sipro.id_prycto', '=', 'floracion.id_pr');
            })
            ->leftJoin('datos_campo_crudos', function ($join) {
                $join->on('datos_campo_crudos.nm_vrdad', '=', 'floracion.vrdad')
                    ->where('datos_campo_crudos.estdo_slccion', '=', 3);
            })
            ->leftjoin('caracteres', function ($join) {
                $join->on('caracteres.id_crcter', '=', 'floracion.id_crcter');
            })
            ->whereBetween('floracion.fcha', array($fechai, $fechaf))
            ->where('floracion.estado', '=', 0)
            ->where('floracion.bolsa_comun', '=', 0)
            ->where('remote_pg_sipro.id_prycto', $proy)
            ->groupBy('floracion.vrdad', "floracion.sxo", "floracion.id_pr", "caracteres.nmbre_crcter", "caracteres.id_crcter", "floracion.polen", "remote_pg_sipro.nm_prycto")
            ->select(DB::raw('"floracion"."vrdad", 
                        "floracion"."sxo", 
                        "floracion"."id_pr",
                        "caracteres"."nmbre_crcter" as id_crcter,
                        "caracteres"."id_crcter" as id_caracter,
                        "floracion"."polen",
                        "remote_pg_sipro"."nm_prycto" as nombre_proyecto,
                        avg(mosaico::float) msco_r, 
                        avg(roya::float) rya_cfe_r, 
                        avg("179"::float) roya_naranja, 
                        avg(carbon::float) carbon, 
                        avg("173"::float) tchm, 
                        avg("163"::float) scrsa, 
                        avg("Cepas afectadas Carbón"::float) volcamiento,
                        avg("Tallo Altura (cm)"::float) altura_planta, 
                        avg("Población (m)"::float) poblacion, 
                        avg("DiametroTallo"::float) dmtro_tllo'))
            ->distinct()
            ->orderBy('floracion.polen')
            ->get();

        $variedad_testigo = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
            ->select(DB::raw('"variedad", 
                        avg(mosaico_p::float) msco_r, 
                        avg( roya_cafe_r::float) rya_cfe_r, 
                        avg(roya_naranja_r::float) roya_naranja, 
                        avg(carbon_p::float) carbon, 
                        avg(tchm::float) tchm, 
                        avg(diametro_tallo::float) dmtro_tllo, 
                        avg(volcamiento::float) volcamiento,
                        avg(altura_planta::float) altura_planta, 
                        avg(poblacion_1m::float) poblacion, 
                        avg(sacarosa::float) scrsa'))
            ->where('variedad', '=', $testigo)
            ->groupBy("variedad")
            ->first();


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
        $query = DB::connection('sivar')->table('cruzamientos');

        // Búsqueda Global
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('vrdad_mdre', 'ilike', '%' . $search . '%')
                  ->orWhere('vrdad_pdre1', 'ilike', '%' . $search . '%')
                  ->orWhere('vrdad_pdre2', 'ilike', '%' . $search . '%')
                  ->orWhere('vrdad_pdre3', 'ilike', '%' . $search . '%')
                  ->orWhere('vrdad_pdre4', 'ilike', '%' . $search . '%')
                  ->orWhere('vrdad_pdre5', 'ilike', '%' . $search . '%')
                  ->orWhere('pdgree', 'ilike', '%' . $search . '%')
                  ->orWhere('id_crzmnto', 'like', '%' . $search . '%');
            });
        }

        // Filtros por Columna (Tabla Dinámica)
        if (!empty($filtersJson)) {
            $filters = json_decode($filtersJson, true);
            if (is_array($filters)) {
                foreach ($filters as $col => $val) {
                    if (!empty($val)) {
                        if ($col === 'padres') {
                            $query->where(function ($q) use ($val) {
                                $q->where('vrdad_pdre1', 'ilike', '%' . $val . '%')
                                  ->orWhere('vrdad_pdre2', 'ilike', '%' . $val . '%')
                                  ->orWhere('vrdad_pdre3', 'ilike', '%' . $val . '%')
                                  ->orWhere('vrdad_pdre4', 'ilike', '%' . $val . '%')
                                  ->orWhere('vrdad_pdre5', 'ilike', '%' . $val . '%');
                            });
                        } else if ($col === 'id_crzmnto') {
                            $query->where('id_crzmnto', 'like', '%' . $val . '%');
                        } else {
                            $query->where($col, 'ilike', '%' . $val . '%');
                        }
                    }
                }
            }
        }

        $query->orderBy('id_crzmnto', 'desc');
        
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
            'scrsa',
            'volcamiento'
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
}

