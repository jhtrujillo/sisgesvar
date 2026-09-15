<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Crossing;
use App\Models\Projects;
use App\Models\PonderadoVM;
use App\Models\Flowering;
use App\Models\PonderadoCruzamiento;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Exception;
use App\Services\CrossingService;
use App\Http\Requests\StoreCruzamientoRequest;

class CrossingController extends Controller
{
    protected $crossingService;

    public function __construct(CrossingService $crossingService)
    {
        $this->crossingService = $crossingService;
    }

    public function generateMatrix(Request $request, $proyectos, $proyecto, $testigo, $ambiente = 'Semiseco')
    {
        return $this->crossingService->generateMatrix($proyectos, $proyecto, $testigo, $ambiente);
    }

    public function suggestionCrossings(Request $request, $proyectos, $proyecto, $testigo, $ambiente)
    {
        return $this->crossingService->suggestionCrossings($proyectos, $proyecto, $testigo, $ambiente);
    }

    public function sugerenciasCruzamientosBolsaComun(Request $request, $proyectos, $proyecto, $testigo, $ambiente)
    {
        return $this->crossingService->sugerenciasCruzamientosBolsaComun($proyectos, $proyecto, $testigo, $ambiente);
    }

    public function suggestionCrossingsPerProject(Request $request, $proyectos, $proyecto, $testigo, $ambiente)
    {
        return $this->crossingService->suggestionCrossingsPerProject($proyectos, $proyecto, $testigo, $ambiente);
    }

    public function crossingList(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 10);
            $search = $request->input('search');
            $filtersJson = $request->input('filters');

            $model = $this->crossingService->crossingList($perPage, $search, $filtersJson);

            if ($model->isNotEmpty()) {
                return response()->json($model);
            }

            return response("No hay registros", 400);
        } catch (\Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    public function crossingInitialData(Request $request)
    {
        $proyectosConFlores = $this->crossingService->crossingInitialData();
        return response()->json($proyectosConFlores);
    }
    public function listarFlores(Request $request, $proyectos, $fechai, $fechaf)
    {
        $flores = $this->crossingService->listarFlores($proyectos, $fechai, $fechaf);
        return response()->json($flores);
    }

    public function parametizeWeightedCrossing(Request $request, $proyecto, $ambiente)
    {
        $response = $this->crossingService->parametizeWeightedCrossing($proyecto, $ambiente);
        return response()->json($response);
    }

    public function modifyFeatures(Request $request, $car, $proyecto, $nivel, $ponderado, $ambiente, $nuevo)
    {
        $this->crossingService->modifyFeatures($car, $proyecto, $nivel, $ponderado, $ambiente, $nuevo);
        return response()->json(['message' => 'Se modificó correctamente la caracteristica.']);
    }



    public function enviarABolsaComun(Request $request, $variedad)
    {
        $result = $this->crossingService->enviarABolsaComun($variedad);
        
        if ($result['status']) {
            return response()->json(['message' => $result['message']]);
        } else {
            return response()->json(['message' => $result['message']], 404);
        }
    }

    public function enviarFlorAProyecto(Request $request, $variedad, $proyecto, $bolsa)
    {
        $result = $this->crossingService->enviarFlorAProyecto($variedad, $proyecto, $bolsa);

        if ($result['status']) {
            return response()->json(['message' => $result['message']]);
        } else {
            return response()->json(['message' => $result['message']], 404);
        }
    }
    public function criteriosBancoGermoplasma(Request $request)
    {
        $bg = "";

        return response()->json($bg);
    }
    public function criteriosBancoGermoplasmaPorVariedad(Request $request, $variedad)
    {
        $criterios = $this->crossingService->criteriosBancoGermoplasmaPorVariedad($variedad);
        return response()->json(['criterios' => $criterios]);
    }

    public function proyectosConFlores(Request $request)
    {
        $term = trim($request->q);
        $modelName = trim($request->model);
        $id = $request->id;
        $text = $request->text;

        $formattedTags = $this->crossingService->proyectosConFlores($modelName, $term, $id, $text);

        if ($formattedTags === null) {
            return response()->json(['error' => 'Model not found.'], 404);
        }

        return response()->json($formattedTags);
    }


    public function guardarCruzamiento(Request $request, $madre = null, $padres = null, $observaciones = null, $idPonderado = null, $proyectos = null, $autofecundado = null)
    {
        $crossings = $request->input('crossings');

        $usuario = auth('api')->user();
        if (!$usuario) {
            $usuario = \App\Models\User::first();
        }

        if (is_array($crossings)) {
            try {
                DB::connection('sivar')->beginTransaction();

                $crossingsToInsert = [];
                $usedIdsInBatch = [];

                foreach ($crossings as $cData) {
                    $madreVal = $cData['madre'] ?? '';
                    $padresVal = $cData['padres'] ?? '';
                    $obsVal = $cData['observaciones'] ?? 'Programacion de Cruzamientos';
                    $idPondVal = $cData['id_ponderados'] ?? $request->input('id_ponderados') ?? $request->input('id_ponderado');
                    $autoVal = $cData['autofecundado'] ?? 0;
                    $cantMadre = isset($cData['flores_madre']) ? max(1, (int)$cData['flores_madre']) : 1;
                    $cantPadre = isset($cData['flores_padre']) ? max(1, (int)$cData['flores_padre']) : 1;

                    $florMadre = explode("_", $madreVal);
                    $varMadre = $florMadre[0] ?? '';
                    $projMadreRaw = isset($florMadre[1]) ? str_replace("9999", "", $florMadre[1]) : null;
                    $caracterMadre = isset($florMadre[2]) && $florMadre[2] !== '' ? $florMadre[2] : null;
                    $idPrMadre = (isset($projMadreRaw) && is_numeric($projMadreRaw) && trim((string)$projMadreRaw) !== '') ? (int)$projMadreRaw : null;

                    $madreFlowerIds = $this->obtenerYDesactivarFlores($varMadre, $projMadreRaw, $caracterMadre, $cantMadre, $usedIdsInBatch);
                    $idFlrMadre = !empty($madreFlowerIds) ? $madreFlowerIds[0] : null;

                    $crossingRecord = [
                        "pias de procedencia" => "Colombia",
                        "Sitio de cruzamiento" => "CNC",
                        "Estacion_Experimental" => "EESA",
                        "vrdad_mdre" => $varMadre,
                        "id_pr_mdre" => $idPrMadre,
                        "usuario_creacion" => $usuario ? $usuario->id_usrio : null,
                        "obsrvcnes" => $obsVal,
                        "fcha_crzmnto" => now(),
                        "proyecto" => $projMadreRaw,
                        "id_ponderados" => $idPondVal,
                        "grpo_crzmnto_mdre" => $caracterMadre,
                        "id_flrcion_mdre" => $idFlrMadre,
                    ];

                    $padre = explode(",", $padresVal);
                    $caracter_padre = "";
                    for ($i = 1; $i <= count($padre); $i++) {
                        $pItem = trim($padre[$i - 1]);
                        if ($pItem !== "") {
                            $flor_padre = explode("_", $pItem);
                            $varPadre = $flor_padre[0] ?? '';
                            $projPadreRaw = isset($flor_padre[1]) ? str_replace("9999", "", $flor_padre[1]) : null;
                            $carPadre = isset($flor_padre[2]) && $flor_padre[2] !== '' ? $flor_padre[2] : null;
                            if ($carPadre !== null) {
                                $caracter_padre = $caracter_padre . "," . $carPadre;
                            }

                            $idPrPadre = (isset($projPadreRaw) && is_numeric($projPadreRaw) && trim((string)$projPadreRaw) !== '') ? (int)$projPadreRaw : null;

                            $padreFlowerIds = $this->obtenerYDesactivarFlores($varPadre, $projPadreRaw, $carPadre, $cantPadre, $usedIdsInBatch);
                            $idFlrPadre = !empty($padreFlowerIds) ? $padreFlowerIds[0] : null;

                            $caracteristica = "vrdad_pdre" . $i;
                            $origen = "id_pr_pdre" . $i;
                            $id_flrcion_col = "id_flrcion_pdre" . $i;

                            $crossingRecord[$caracteristica] = $varPadre;
                            $crossingRecord[$origen] = $idPrPadre;
                            $crossingRecord["grpo_crzmnto_pdre"] = $caracter_padre;
                            $crossingRecord[$id_flrcion_col] = $idFlrPadre;
                        }
                    }
                    $crossingsToInsert[] = $crossingRecord;

                    if ($autoVal == 1) {
                        $padre = explode(",", $padresVal);
                        $pItemAuto = trim($padre[0] ?? '');
                        $flor_padre = explode("_", $pItemAuto);
                        $varPadreAuto = $flor_padre[0] ?? '';
                        $projPadreAutoRaw = isset($flor_padre[1]) ? str_replace("9999", "", $flor_padre[1]) : null;
                        $carPadreAuto = isset($flor_padre[2]) && $flor_padre[2] !== '' ? $flor_padre[2] : null;
                        $idPrPadreAuto = (isset($projPadreAutoRaw) && is_numeric($projPadreAutoRaw) && trim((string)$projPadreAutoRaw) !== '') ? (int)$projPadreAutoRaw : null;

                        $autoMadreIds = $this->obtenerYDesactivarFlores($varPadreAuto, $projPadreAutoRaw, $carPadreAuto, 1, $usedIdsInBatch);
                        $autoPadreIds = $this->obtenerYDesactivarFlores($varPadreAuto, $projPadreAutoRaw, $carPadreAuto, 1, $usedIdsInBatch);

                        $crossingsToInsert[] = [
                            "pias de procedencia" => "Colombia",
                            "Sitio de cruzamiento" => "CNC",
                            "Estacion_Experimental" => "EESA",
                            "vrdad_mdre" => $varPadreAuto,
                            "id_pr_mdre" => $idPrPadreAuto,
                            "vrdad_pdre1" => $varPadreAuto,
                            "grpo_crzmnto_pdre" => $carPadreAuto,
                            "grpo_crzmnto_mdre" => $carPadreAuto,
                            "id_pr_pdre1" => $idPrPadreAuto,
                            "obsrvcnes" => $obsVal,
                            "id_flrcion_mdre" => !empty($autoMadreIds) ? $autoMadreIds[0] : null,
                            "id_flrcion_pdre1" => !empty($autoPadreIds) ? $autoPadreIds[0] : null,
                            "fcha_crzmnto" => now(),
                            "usuario_creacion" => $usuario ? $usuario->id_usrio : null,
                            "proyecto" => $projPadreAutoRaw,
                            "id_ponderados" => $idPondVal,
                        ];
                    }
                }

                if (count($crossingsToInsert) > 0) {
                    DB::connection('sivar')->table('cruzamientos')->insert($crossingsToInsert);
                }

                DB::connection('sivar')->commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Todos los cruzamientos se guardaron correctamente en lote y las flores fueron descontadas.'
                ]);
            } catch (\Throwable $ex) {
                if (DB::connection('sivar')->transactionLevel() > 0) {
                    DB::connection('sivar')->rollBack();
                }
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        }

        $madre = $madre ?? $request->input('madre');
        $padres = $padres ?? $request->input('padres');
        $observaciones = $observaciones ?? $request->input('observaciones');
        $idPonderado = $idPonderado ?? $request->input('id_ponderados') ?? $request->input('id_ponderado');
        $proyectos = $proyectos ?? $request->input('proyectos');
        $autofecundado = $autofecundado ?? $request->input('autofecundado');

        $florMadre = explode("_", (string)$madre);
        $varMadre = $florMadre[0] ?? '';
        $projMadreRaw = isset($florMadre[1]) ? str_replace("9999", "", $florMadre[1]) : null;
        $caracterMadre = isset($florMadre[2]) && $florMadre[2] !== '' ? $florMadre[2] : null;
        $idPrMadre = (isset($projMadreRaw) && is_numeric($projMadreRaw) && trim((string)$projMadreRaw) !== '') ? (int)$projMadreRaw : null;

        // Crea un nuevo objeto Crossing
        $cruzamiento = new Crossing;
        $cruzamiento->{"pias de procedencia"} = "Colombia";
        $cruzamiento->{"Sitio de cruzamiento"} = "CNC";
        $cruzamiento->{"Estacion_Experimental"} = "EESA";
        $cruzamiento->vrdad_mdre = $varMadre;
        $cruzamiento->id_pr_mdre = $idPrMadre;
        $cruzamiento->usuario_creacion = $usuario ? $usuario->id_usrio : null;
        $cruzamiento->obsrvcnes = $observaciones;
        $cruzamiento->fcha_crzmnto = now();
        $cruzamiento->proyecto = $projMadreRaw;
        $cruzamiento->id_ponderados = $idPonderado;
        $cruzamiento->grpo_crzmnto_mdre = $caracterMadre;

        // Realiza otras operaciones relacionadas con la obtención de ID
        $id_flr_mdre = $this->obtenerIdFlorCruzamiento($projMadreRaw, $varMadre, $caracterMadre);
        $cruzamiento->id_flrcion_mdre = $id_flr_mdre;

        $padre = explode(",", (string)$padres);
        $caracter_padre = "";
        for ($i = 1; $i <= count($padre); $i++) {
            $pItem = trim($padre[$i - 1]);
            if ($pItem !== "") {
                $flor_padre = explode("_", $pItem);
                $varPadre = $flor_padre[0] ?? '';
                $projPadreRaw = isset($flor_padre[1]) ? str_replace("9999", "", $flor_padre[1]) : null;
                $carPadre = isset($flor_padre[2]) && $flor_padre[2] !== '' ? $flor_padre[2] : null;
                if ($carPadre !== null) {
                    $caracter_padre = $caracter_padre . "," . $carPadre;
                }

                $idPrPadre = (isset($projPadreRaw) && is_numeric($projPadreRaw) && trim((string)$projPadreRaw) !== '') ? (int)$projPadreRaw : null;

                $caracteristica = "vrdad_pdre" . $i;
                $origen = "id_pr_pdre" . $i;
                $col_id_flr = "id_flrcion_pdre" . $i;
                $cruzamiento->$caracteristica = $varPadre;
                $cruzamiento->$origen = $idPrPadre;
                $cruzamiento->grpo_crzmnto_pdre = $caracter_padre;
                $id_flr_pdre = $this->obtenerIdFlorCruzamiento($projPadreRaw, $varPadre, $carPadre);
                $cruzamiento->$col_id_flr = $id_flr_pdre;
            }
        }
        $cruzamiento->save();

        if ($autofecundado == 1) {
            $padre = explode(",", (string)$padres);
            $pItemAuto = trim($padre[0] ?? '');
            $flor_padre = explode("_", $pItemAuto);
            $varPadreAuto = $flor_padre[0] ?? '';
            $projPadreAutoRaw = isset($flor_padre[1]) ? str_replace("9999", "", $flor_padre[1]) : null;
            $carPadreAuto = isset($flor_padre[2]) && $flor_padre[2] !== '' ? $flor_padre[2] : null;
            $idPrPadreAuto = (isset($projPadreAutoRaw) && is_numeric($projPadreAutoRaw) && trim((string)$projPadreAutoRaw) !== '') ? (int)$projPadreAutoRaw : null;

            $cruzamiento_auto = new Crossing;
            $cruzamiento_auto->vrdad_mdre = $varPadreAuto;
            $cruzamiento_auto->id_pr_mdre = $idPrPadreAuto;
            $cruzamiento_auto->vrdad_pdre1 = $varPadreAuto;
            $cruzamiento_auto->grpo_crzmnto_pdre = $carPadreAuto;
            $cruzamiento_auto->grpo_crzmnto_mdre = $carPadreAuto;
            $cruzamiento_auto->id_pr_pdre1 = $idPrPadreAuto;
            $cruzamiento_auto->obsrvcnes = $observaciones;
            $cruzamiento_auto->fcha_crzmnto = DB::raw('now()');
            $cruzamiento_auto->usuario_creacion = $usuario ? $usuario->id_usrio : null;
            $cruzamiento_auto->proyecto = $projPadreAutoRaw;
            $cruzamiento_auto->id_ponderados = $idPonderado;
            
            $id_flr_auto_mdre = $this->obtenerIdFlorCruzamiento($projPadreAutoRaw, $varPadreAuto, $carPadreAuto);
            $id_flr_auto_pdre = $this->obtenerIdFlorCruzamiento($projPadreAutoRaw, $varPadreAuto, $carPadreAuto);
            $cruzamiento_auto->id_flrcion_mdre = $id_flr_auto_mdre;
            $cruzamiento_auto->id_flrcion_pdre1 = $id_flr_auto_pdre;

            $cruzamiento_auto->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cruzamiento guardado con éxito'
        ]);
    }

    private function obtenerYDesactivarFlores($vrdad, $proyecto = null, $caracter = null, $cantidad = 1, &$usedIdsInBatch = [])
    {
        if ($cantidad <= 0) {
            return [];
        }

        $idPrycto = null;
        if (!empty($proyecto)) {
            if (is_numeric($proyecto) && (int)$proyecto < 1000) {
                $idPrycto = (int)$proyecto;
            } else {
                $projDb = DB::connection('sivar')
                    ->table('remote_pg_sipro')
                    ->where('cd_cntble', (string)$proyecto)
                    ->first();
                if ($projDb) {
                    $idPrycto = $projDb->id_prycto;
                }
            }
        }

        $query = DB::connection('sivar')
            ->table('floracion')
            ->where('vrdad', $vrdad)
            ->where(function ($q) {
                $q->where('estado', 0)->orWhere('estado', '0');
            });

        if (!empty($usedIdsInBatch)) {
            $query->whereNotIn('id_flrcion', $usedIdsInBatch);
        }

        $candQuery = clone $query;
        if ($idPrycto) {
            $candQuery->where(function ($q) use ($idPrycto) {
                $q->where('id_pr', $idPrycto)->orWhere('bolsa_comun', 1);
            });
        }
        if (!empty($caracter)) {
            $candQuery->where('id_crcter', $caracter);
        }

        $floresEncontradas = $candQuery->limit($cantidad)->get();

        if ($floresEncontradas->count() < $cantidad && !empty($caracter)) {
            $candQuery2 = clone $query;
            if ($idPrycto) {
                $candQuery2->where(function ($q) use ($idPrycto) {
                    $q->where('id_pr', $idPrycto)->orWhere('bolsa_comun', 1);
                });
            }
            $existingFound = $floresEncontradas->pluck('id_flrcion')->toArray();
            if (!empty($existingFound)) {
                $candQuery2->whereNotIn('id_flrcion', array_merge($usedIdsInBatch, $existingFound));
            }
            $faltantes = $cantidad - $floresEncontradas->count();
            $moreFlores = $candQuery2->limit($faltantes)->get();
            $floresEncontradas = $floresEncontradas->merge($moreFlores);
        }

        if ($floresEncontradas->count() < $cantidad) {
            $existingFound = $floresEncontradas->pluck('id_flrcion')->toArray();
            $candQuery3 = clone $query;
            if (!empty($existingFound)) {
                $candQuery3->whereNotIn('id_flrcion', array_merge($usedIdsInBatch, $existingFound));
            }
            $faltantes = $cantidad - $floresEncontradas->count();
            $moreFlores = $candQuery3->limit($faltantes)->get();
            $floresEncontradas = $floresEncontradas->merge($moreFlores);
        }

        $selectedIds = $floresEncontradas->pluck('id_flrcion')->toArray();

        if (!empty($selectedIds)) {
            DB::connection('sivar')
                ->table('floracion')
                ->whereIn('id_flrcion', $selectedIds)
                ->update(['estado' => 1]);

            foreach ($selectedIds as $id) {
                $usedIdsInBatch[] = $id;
            }
        }

        return $selectedIds;
    }

    public function obtenerIdFlorCruzamiento($proyecto, $vrdad, $caracter)
    {
        $usedIds = [];
        $ids = $this->obtenerYDesactivarFlores($vrdad, $proyecto, $caracter, 1, $usedIds);
        return !empty($ids) ? $ids[0] : null;
    }
    public function guardarPonderados(Request $request, $proyecto = null)
    {
        $proyecto = ($proyecto && trim($proyecto) !== '') ? trim($proyecto) : '010105';
        $idPonderado = Carbon::now()->toDateTimeString() . "++" . $proyecto;

        $ponderados = DB::connection('sivar')
            ->table('caracteristicas_valor_merito')
            ->leftJoin('ponderados_valor_merito', function ($join) use ($proyecto) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', $proyecto);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.nombre', 'caracteristicas_valor_merito.id_caracteristica')
            ->get();

        $procesados = [];
        foreach ($ponderados as $ponderado) {
            if (in_array($ponderado->id_caracteristica, $procesados)) {
                continue;
            }
            $procesados[] = $ponderado->id_caracteristica;

            $nuevoPonderado = new PonderadoCruzamiento();
            $nuevoPonderado->id_ponderado = $idPonderado;
            $nuevoPonderado->id_caracteristica = $ponderado->id_caracteristica;
            $nuevoPonderado->nivel = $ponderado->nivel;
            $nuevoPonderado->ponderado = $ponderado->ponderado;
            $nuevoPonderado->save();
        }

        return $idPonderado;
    }
    public function obtenerCruzamientosPorPonderado($idPonderado)
    {
        $cruzamientos = DB::connection('sivar')
            ->table('cruzamientos')
            ->leftJoin('floracion as f_mdre', 'cruzamientos.id_flrcion_mdre', '=', 'f_mdre.id_flrcion')
            ->leftJoin('floracion as f_pdre', 'cruzamientos.id_flrcion_pdre1', '=', 'f_pdre.id_flrcion')
            ->where('cruzamientos.id_ponderados', $idPonderado)
            ->select(
                'cruzamientos.*',
                'f_mdre.vivero as mdre_vivero', 'f_mdre.lte as mdre_lte', 'f_mdre.prcla as mdre_prcla', 'f_mdre.polen as mdre_polen',
                'f_pdre.vivero as pdre_vivero', 'f_pdre.lte as pdre_lte', 'f_pdre.prcla as pdre_prcla', 'f_pdre.polen as pdre_polen'
            )
            ->get();
        return response()->json($cruzamientos);
    }
    
    public function consolidado(Request $request)
    {
        $primerDiaDelAno = Carbon::parse('first day of January');

        $cruzamientos = Crossing::where('fcha_crzmnto', '>', $primerDiaDelAno)
            ->orderBy('vrdad_mdre')
            ->orderBy('vrdad_pdre1')
            ->orderByDesc('vrdad_pdre2')
            ->get();

        $consolidadoData = [];

        foreach ($cruzamientos as $cruzamiento) {
            $usuario = User::where('id_usrio', $cruzamiento->usuario_creacion)->first();
            $origen = $this->crearOrigenCruzamiento($cruzamiento->id_crzmnto);

            $consolidadoData[] = [
                'id_crzmnto' => $cruzamiento->id_crzmnto,
                'vrdad_mdre' => $cruzamiento->vrdad_mdre,
                'usuario' => $usuario ? $usuario->nmbre : 'N/A',
                'origen' => $origen,
                // Agrega otros campos según tus necesidades
            ];
        }

        return response()->json(['consolidado' => $consolidadoData]);
    }
    // public function crearOrigenCruzamiento($id_cruzamiento)
    // {
    //     $cruzamiento = Crossing::where('id_crzmnto', $id_cruzamiento)->first();
    //     $origen = '';

    //     if ($cruzamiento) {
    //         $florMadre = Flowering::where('id_flrcion', $cruzamiento->id_pr_mdre)->first();
    //         if ($florMadre) {
    //             $origen = "CN" . $florMadre->vivero . "-" . $florMadre->prcla . " x ";
    //             for ($i = 1; $i <= 15; $i++) {
    //                 $origenPadre = "id_pr_pdre" . $i;
    //                 if ($cruzamiento->$origenPadre != null && $cruzamiento->$origenPadre != "") {
    //                     $florPadre = Flowering::where('id_flrcion', $cruzamiento->$origenPadre)->first();
    //                     $origen .= "CN" . $florPadre->vivero . "-" . $florPadre->prcla . " x ";
    //                 }
    //             }
    //             $origen = substr($origen, 0, -3);
    //         }
    //     }

    //     return $origen;
    // }
    public function crearOrigenCruzamiento($id_cruzamiento)
    {
        // Obtener el registro de cruzamiento por ID
        $cruzamiento = Crossing::where('id_crzmnto', $id_cruzamiento)->first();

        if (!$cruzamiento) {
            return "--";
        }

        // Obtener el registro de la flor madre
        $florMadre = Flowering::where('id_flrcion', $cruzamiento->id_pr_mdre)->first();
        if (!$florMadre) {
            return "--";
        }

        // Inicializar el origen con la información de la madre
        $origen = "CN" . $florMadre->vivero . "-" . $florMadre->prcla;

        // Agregar la información de los padres en un loop
        $padresExistentes = false;
        for ($i = 1; $i <= 15; $i++) {
            $origenPadre = "id_pr_pdre" . $i;

            // Verificar si el padre actual tiene un valor asignado
            if (!empty($cruzamiento->$origenPadre)) {
                $florPadre = Flowering::where('id_flrcion', $cruzamiento->$origenPadre)->first();

                // Añadir la información del padre si existe en la base de datos
                if ($florPadre) {
                    $origen .= " x CN" . $florPadre->vivero . "-" . $florPadre->prcla;
                    $padresExistentes = true;
                }
            }
        }

        // Si no existen padres registrados, retorna "--"
        return $padresExistentes ? $origen : "--";
    }


    public function consultarHistoricoCruzamiento(Request $request, $madre, $padres)
    {
        $padre = explode(",", $padres);
        $anoActual = date('2019-01-01');
        $result = [];

        // Cruzamiento múltiple === muchos padres
        if (sizeof($padre) > 3) {
            $cruzamientos = DB::connection('sivar')->table('cruzamientos')
                ->where('vrdad_mdre', "=", $madre)
                ->whereNotNull('vrdad_pdre1')
                ->whereNotNull('vrdad_pdre2')
                ->whereIn('vrdad_pdre1', $padre)
                ->whereIn('vrdad_pdre2', $padre)
                ->where('fcha_crzmnto', '>=', $anoActual)
                ->count();

            $result['numero'] = $cruzamientos;
        }
        // Cruzamiento simple === un solo padre
        else {
            $cruzamientos = DB::connection('sivar')->table('cruzamientos')
                ->where('vrdad_mdre', "=", $madre)
                ->where('vrdad_pdre1', "=", $padre[0])
                ->where('fcha_crzmnto', '>=', $anoActual)
                ->count();

            $result['numero'] = $cruzamientos;
        }

        return response()->json($result);
    }
    public function enviarCorreoPracticos(Request $request, $stringMadrePadre)
    {
        $usuario = $request->user();
        $cruzamientos = explode("$$$", $stringMadrePadre);
        $nuevosCruzamientos = [];

        foreach ($cruzamientos as $key => $cruzamiento) {
            if ($cruzamiento != "") {
                $c = explode("+++", $cruzamiento);
                $madre = explode("_", $c[0]);
                $variedadMadre = $madre[0];
                $proyectoMadre = str_replace("9999", "", $madre[1]);
                $variedadMadreProyecto = DB::connection('sivar')->table('remote_pg_sipro')
                    ->where('id_prycto', "=", $proyectoMadre)->select('nm_prycto')->first();
                $variedadMadreCaracter = DB::connection('sivar')->table('caracteres')
                    ->where('id_crcter', "=", $madre[2])->select('nmbre_crcter')->first();

                $nuevoCruzamiento = $variedadMadre . "_" . $variedadMadreProyecto->nm_prycto . "_" . $variedadMadreCaracter->nmbre_crcter . "+++";

                $padres = explode(",", $c[1]);
                $nuevosPadres = [];
                foreach ($padres as $k => $p) {
                    if ($p != "") {
                        $padre = explode("_", $p);
                        $variedadPadre = $padre[0];
                        $proyectoPadre = str_replace("9999", "", $padre[1]);
                        $variedadPadreProyecto = DB::connection('sivar')->table('remote_pg_sipro')
                            ->where('id_prycto', "=", $proyectoPadre)->select('nm_prycto')->first();
                        $variedadPadreCaracter = DB::connection('sivar')->table('caracteres')
                            ->where('id_crcter', "=", $padre[2])->select('nmbre_crcter')->first();

                        $nuevoCruzamiento .= "" . $variedadPadre . "_" . $variedadPadreProyecto->nm_prycto . "_" . $variedadPadreCaracter->nmbre_crcter . ",";
                    }
                }

                $nuevoCruzamiento .= "+++1+++" . $c[3];
                $nuevosCruzamientos[] = $nuevoCruzamiento;
            }
        }
        Mail::to('lfbedoya@cenicana.org')->send(new ProgramacionCruzamientos($correoData));

        return response()->json(['message' => 'Datos de cruzamientos enviados correctamente', 'cruzamientos' => $nuevosCruzamientos]);
    }

    public function consolidadoDatatable(Request $request, $tipo)
    {
        $anoactual = date('Y');
        $contador = 0;
        $contador_id = 0;
        $pais = "pias de procedencia";
        $sitio = "Sitio de cruzamiento";
        $estacion = "Estacion_Experimental";
        $cruzamientos = [];

        if ($tipo == 1) {
            $cruzamientos = Crossing::where('fcha_crzmnto', '>', '2018-01-01')
                ->whereNull('vrdad_pdre2')
                ->whereRaw('vrdad_mdre != vrdad_pdre1')
                ->get();
        } elseif ($tipo == 2) {
            $cruzamientos = Crossing::where('fcha_crzmnto', '>', '2018-01-01')
                ->whereNotNull('vrdad_pdre2')
                ->get();
        } elseif ($tipo == 3) {
            $cruzamientos = Crossing::where('fcha_crzmnto', '>', '2018-01-01')
                ->whereRaw('vrdad_mdre <> vrdad_pdre1')
                ->get();
        }

        $result = [];

        foreach ($cruzamientos as $key => $cruzamiento) {
            // $usuario = 'App\Models\User'::where('id_usuario', '=', $cruzamiento->usuario_creacion)->first();
            // $cruzamiento->usuario = $usuario->nombre_usuario;
            $cruzamiento->origen = $this->crearOrigenCruzamiento($cruzamiento->id_crzmnto);

            $contador++;
            $contador_id++;

            if ($tipo == 1) {
                $cruzamiento->id_cruzamiento = $contador . '' . $cruzamiento->$sitio . '' . $anoactual;
                $cruzamiento->pedigree = "(" . $cruzamiento->vrdad_mdre . " x " . $cruzamiento->vrdad_pdre1 . ") HC" . $contador_id;
            } elseif ($tipo == 2) {
                $cruzamiento->id_cruzamiento = $contador . '' . $cruzamiento->$sitio . '' . $anoactual;
                $cruzamiento->pedigree = "(" . $cruzamiento->vrdad_mdre . " x ?) HM" . $contador_id;
            } elseif ($tipo == 3) {
                $cruzamiento->id_cruzamiento = $contador . '' . $cruzamiento->$sitio . '' . $anoactual;
                $cruzamiento->pedigree = "(" . $cruzamiento->vrdad_mdre . " x " . $cruzamiento->vrdad_pdre1 . ") AUTO" . $contador_id;
            }

            $result[] = $cruzamiento;
        }

        return response()->json($result);
    }
    public function cargarCruzamientos(Request $request)
    {
        // Aquí manejas la carga de datos de cruzamientos externos
        // Puedes procesar los datos y almacenarlos en tu base de datos o realizar cualquier acción necesaria
        // Después, puedes devolver una respuesta JSON para indicar el resultado de la carga, por ejemplo:

        $response = [
            'success' => true,
            'message' => 'Carga de cruzamientos externos exitosa',
        ];

        return response()->json($response);
    }
    public function modificarCruzamiento(Request $request, $idCruzamiento)
    {

        $cruzamiento = Crossing::find($idCruzamiento);
        $ponderados = DB::connection('sivar')->table('ponderados_cruzamiento')
            ->where('id_ponderado', '=', $cruzamiento->id_ponderados)
            ->leftJoin('caracteristicas_valor_merito', 'caracteristicas_valor_merito.id_caracteristica', '=', 'ponderados_cruzamiento.id_caracteristica')
            ->get();
        $response = [
            'success' => true,
            'message' => 'Cruzamiento modificado con éxito',
        ];

        return response()->json($response);
    }
    public function modificarCruzamientoPost(Request $request)
    {
        try {
            $cruzamiento = Crossing::find($request->id_cruzamiento);
            $cruzamiento->cngldor = $request->nevera;
            $cruzamiento->pso_smlla_actual = $request->peso;
            if ($cruzamiento->fcha_cscha != "") {
                $cruzamiento->fcha_cscha = $request->fecha_cosecha;
            }
            if ($cruzamiento->fcha_smbra_smllro != "") {
                $cruzamiento->fcha_smbra_smllro = $request->fecha_siembra;
            }
            if ($cruzamiento->fcha_grmncion != "") {
                $cruzamiento->fcha_grmncion = $request->fecha_germinacion;
            }
            if ($cruzamiento->fcha_slccion != "") {
                $cruzamiento->fcha_slccion = $request->fecha_seleccion;
            }
            $cruzamiento->plntlas_ttles = $request->numero_plantas_sembradas;
            $cruzamiento->plntlas_ttles = $request->numero_plantas_sembradas;
            $cruzamiento->plntlas_grmndas = $request->numero_plantas_germinadas;
            $cruzamiento->plntlas_trrzas = $request->numero_plantas_terrazas;
            $cruzamiento->plantas_finales_seleccion = $request->numero_plantas_finales;
            $cruzamiento->plntas_con_rya = $request->numero_plantas_roya;
            $cruzamiento->plntas_con_msco = $request->numero_plantas_mosaico;
            $cruzamiento->plntas_con_crbon = $request->numero_plantas_carbon;
            $cruzamiento->plntas_con_rya_nrnja = $request->numero_plantas_roya_naranja;
            $cruzamiento->obsrvcnes = $request->observaciones;
            $cruzamiento->save();

            $response = [
                'success' => true,
                'message' => 'Cruzamiento modificado con éxito',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Ha ocurrido un error, por favor contacte un administrador.',
            ];

            return response()->json($response, 500); // Puedes devolver un código de estado 500 en caso de error
        }
    }

    public function cargarCruzamientosPost(Request $request)
    {
        $EXTENSIONES_VALIDAS = ['xls', 'xlsx'];
        $tipo = 'error';
        $mensaje = '';
        $ext = strtolower($request->excel->getClientOriginalExtension());

        if (in_array($ext, $EXTENSIONES_VALIDAS)) {
            try {
                $user = auth('api')->user();
                $usuario = $user ? $user->id_usrio : \App\Models\User::first()?->id_usrio;
                \Excel::load($request->excel, function ($reader) use ($usuario) {
                    $excel = $reader->get();
                    $objExcel = $reader->getExcel();
                    $sheet = $objExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                        $cruzamiento_id = $rowData[0][0];
                        $madre = $rowData[0][1];
                        $padre = $rowData[0][3];
                        $porcentaje_germinacion = $rowData[0][4];
                        $gramos = $rowData[0][5];
                        $plantulas_estimadas = $rowData[0][6];
                        $ambiente_seco = $rowData[0][7];
                        $ambiente_humedo = $rowData[0][8];
                        #010108 humedo #010106 seco semiseco #010110 pie de monte
                        if ($cruzamiento_id) {
                            if ($ambiente_seco != "" && $ambiente_humedo != "") {
                                $this->cargarCruzamientoMexico('010106', $usuario, $cruzamiento_id, $madre, $padre, $porcentaje_germinacion / 2, $gramos / 2, $plantulas_estimadas / 2);
                                $this->cargarCruzamientoMexico('010108', $usuario, $cruzamiento_id, $madre, $padre, $porcentaje_germinacion / 2, $gramos / 2, $plantulas_estimadas / 2);
                            } else {
                                if ($ambiente_seco != "") {
                                    $this->cargarCruzamientoMexico('010106', $usuario, $cruzamiento_id, $madre, $padre, $porcentaje_germinacion, $gramos, $plantulas_estimadas);
                                }
                                if ($ambiente_humedo != "") {
                                    $this->cargarCruzamientoMexico('010108', $usuario, $cruzamiento_id, $madre, $padre, $porcentaje_germinacion, $gramos, $plantulas_estimadas);
                                }
                            }
                        }
                    }
                });
                $tipo = 'success';
                $mensaje = 'Se cargó satisfactoriamente el archivo';
            } catch (\Exception $e) {
                //echo $e;exit;
                $mensaje = 'Ha ocurrido un error en la carga, por favor contacte un administrador.';
            }
        } else {
            $mensaje = 'Ha ocurrido un error en la carga, por favor revisar el formato del archivo';
        }

        return response()->json(['tipo' => $tipo, 'mensaje' => $mensaje]);
    }
    public function cargarCruzamientoMexico(Request $request)
    {
        $proyecto = $request->input('proyecto');
        $usuario = $request->input('usuario');
        $cruzamiento_id = $request->input('cruzamiento_id');
        $madre = $request->input('madre');
        $padre = $request->input('padre');
        $porcentaje_germinacion = $request->input('porcentaje_germinacion');
        $gramos = $request->input('gramos');
        $plantulas_estimadas = $request->input('plantulas_estimadas');

        $anoactual = date('Y');

        $cruzamiento = new Crossing;
        $cruzamiento->vrdad_mdre = $madre;
        $cruzamiento->vrdad_pdre1 = $padre;
        $cruzamiento->orgen = "CIDCA" . $anoactual . $cruzamiento_id;
        $cruzamiento->usuario_creacion = $usuario;
        $cruzamiento->fcha_crzmnto = now();
        $cruzamiento->ano = $anoactual;
        $cruzamiento->pais = "Mexico";
        $cruzamiento->sitio = "TAM";
        $cruzamiento->estacion = "CIDCA";
        $cruzamiento->no_crzmnto1 = $cruzamiento_id;
        $cruzamiento->nm_fmlias = $cruzamiento_id . "TAM" . date('y');
        $cruzamiento->prcntje_grmncion = $porcentaje_germinacion;
        $cruzamiento->pso_smlla_actual = $gramos;
        $cruzamiento->plntlas_ttles_estmdas = round($plantulas_estimadas);
        $cruzamiento->proyecto = $proyecto;
        $cruzamiento->save();

        return response()->json(['message' => 'Cruzamiento cargado con éxito']);
    }
}
