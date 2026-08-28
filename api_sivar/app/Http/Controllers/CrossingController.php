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
                $fechaFin = now()->format('Y-m-d');
                $fechaInicio = now()->subDay()->format('Y-m-d');

                // 1. Collect all unique variety names to load active flowers in bulk
                $varNames = [];
                foreach ($crossings as $cData) {
                    $florMadre = explode("_", $cData['madre']);
                    $varNames[] = $florMadre[0];
                    if (!empty($cData['padres'])) {
                        $padres = explode(",", $cData['padres']);
                        foreach ($padres as $p) {
                            if ($p !== "") {
                                $florPadre = explode("_", $p);
                                $varNames[] = $florPadre[0];
                            }
                        }
                    }
                }
                $varNames = array_values(array_unique($varNames));

                // 2. Load all matching active flowers in a single query
                $activeFlowers = DB::connection('sivar')
                    ->table('floracion')
                    ->whereBetween('fcha', [$fechaInicio, $fechaFin])
                    ->where('estado', 0)
                    ->whereIn('vrdad', $varNames)
                    ->get();

                // 3. Map active flowers by variety, project, and character for O(1) lookup
                $flowersMap = [];
                foreach ($activeFlowers as $f) {
                    $key = "{$f->vrdad}_{$f->id_pr}_{$f->id_crcter}";
                    if (!isset($flowersMap[$key])) {
                        $flowersMap[$key] = $f->id_flrcion;
                    }
                }

                $crossingsToInsert = [];
                $flowerIdsToDeactivate = [];

                // 4. Build records for bulk insert and deactivation list
                foreach ($crossings as $cData) {
                    $madreVal = $cData['madre'];
                    $padresVal = $cData['padres'];
                    $obsVal = $cData['observaciones'] ?? 'Programacion de Cruzamientos';
                    $idPondVal = $cData['id_ponderados'] ?? $request->input('id_ponderados') ?? $request->input('id_ponderado');
                    $autoVal = $cData['autofecundado'] ?? 0;

                    $florMadre = explode("_", $madreVal);
                    $proyectoMadre = str_replace("9999", "", $florMadre[1]);
                    $caracterMadre = $florMadre[2];

                    $mKey = "{$florMadre[0]}_{$proyectoMadre}_{$caracterMadre}";
                    if (isset($flowersMap[$mKey])) {
                        $flowerIdsToDeactivate[] = $flowersMap[$mKey];
                    }

                    $crossingRecord = [
                        "pias de procedencia" => "Colombia",
                        "Sitio de cruzamiento" => "CNC",
                        "Estacion_Experimental" => "EESA",
                        "vrdad_mdre" => $florMadre[0],
                        "id_pr_mdre" => $proyectoMadre,
                        "usuario_creacion" => $usuario ? $usuario->id_usrio : null,
                        "obsrvcnes" => $obsVal,
                        "fcha_crzmnto" => now(),
                        "proyecto" => $proyectoMadre,
                        "id_ponderados" => $idPondVal,
                        "grpo_crzmnto_mdre" => $caracterMadre,
                    ];

                    $padre = explode(",", $padresVal);
                    $caracter_padre = "";
                    for ($i = 1; $i <= sizeof($padre); $i++) {
                        if ($padre[$i - 1] != "") {
                            $flor_padre = explode("_", $padre[$i - 1]);
                            $proyecto_padre = str_replace("9999", "", $flor_padre[1]);
                            $caracter_padre = $caracter_padre . "," . $flor_padre[2];
                            $caracteristica = "vrdad_pdre" . $i;
                            $origen = "id_pr_pdre" . $i;
                            $crossingRecord[$caracteristica] = $flor_padre[0];
                            $crossingRecord[$origen] = $proyecto_padre;
                            $crossingRecord["grpo_crzmnto_pdre"] = $caracter_padre;

                            $pKey = "{$flor_padre[0]}_{$proyecto_padre}_{$flor_padre[2]}";
                            if (isset($flowersMap[$pKey])) {
                                $flowerIdsToDeactivate[] = $flowersMap[$pKey];
                            }
                        }
                    }
                    $crossingsToInsert[] = $crossingRecord;

                    if ($autoVal == 1) {
                        $padre = explode(",", $padresVal);
                        $flor_padre = explode("_", $padre[0]);
                        $proyecto_padre = str_replace("9999", "", $flor_padre[1]);

                        $crossingsToInsert[] = [
                            "pias de procedencia" => "Colombia",
                            "Sitio de cruzamiento" => "CNC",
                            "Estacion_Experimental" => "EESA",
                            "vrdad_mdre" => $flor_padre[0],
                            "id_pr_mdre" => $proyecto_padre,
                            "vrdad_pdre1" => $flor_padre[0],
                            "grpo_crzmnto_pdre" => $flor_padre[2],
                            "grpo_crzmnto_mdre" => $flor_padre[2],
                            "id_pr_pdre1" => $proyecto_padre,
                            "obsrvcnes" => $obsVal,
                            "fcha_crzmnto" => now(),
                            "usuario_creacion" => $usuario ? $usuario->id_usrio : null,
                            "proyecto" => $proyecto_padre,
                            "id_ponderados" => $idPondVal,
                        ];
                    }
                }

                // 5. Save everything in a single transaction with bulk insert and update queries
                DB::connection('sivar')->beginTransaction();
                
                if (count($crossingsToInsert) > 0) {
                    DB::connection('sivar')->table('cruzamientos')->insert($crossingsToInsert);
                }

                if (count($flowerIdsToDeactivate) > 0) {
                    $flowerIdsToDeactivate = array_values(array_unique($flowerIdsToDeactivate));
                    DB::connection('sivar')->table('floracion')
                        ->whereIn('id_flrcion', $flowerIdsToDeactivate)
                        ->update(['estado' => 1]);
                }

                DB::connection('sivar')->commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Todos los cruzamientos se guardaron correctamente en lote.'
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

        $florMadre = explode("_", $madre);

        $proyectoMadre = str_replace("9999", "", $florMadre[1]);
        $caracterMadre = $florMadre[2];
        $proyecto = explode(",", $proyectos);
        $idPrPadreAuto = "";

        // Crea un nuevo objeto Crossing
        $cruzamiento = new Crossing;
        $cruzamiento->{"pias de procedencia"} = "Colombia";
        $cruzamiento->{"Sitio de cruzamiento"} = "CNC";
        $cruzamiento->{"Estacion_Experimental"} = "EESA";
        $cruzamiento->vrdad_mdre = $florMadre[0];
        $cruzamiento->id_pr_mdre = $proyectoMadre;
        $cruzamiento->usuario_creacion = $usuario ? $usuario->id_usrio : null;
        $cruzamiento->obsrvcnes = $observaciones;
        $cruzamiento->fcha_crzmnto = now();
        $cruzamiento->proyecto = $proyectoMadre;
        $cruzamiento->id_ponderados = $idPonderado;
        $cruzamiento->grpo_crzmnto_mdre = $caracterMadre;

        // Realiza otras operaciones relacionadas con la obtención de ID
        $this->obtenerIdFlorCruzamiento($proyectoMadre, $florMadre[0], $caracterMadre);

        $padre = explode(",", $padres);
        $caracter_padre = "";
        for ($i = 1; $i <= sizeof($padre); $i++) {
            if ($padre[$i - 1] != "") {
                $flor_padre = explode("_", $padre[$i - 1]);
                $proyecto_padre = str_replace("9999", "", $flor_padre[1]);
                $caracter_padre = $caracter_padre . "," . $flor_padre[2];
                $caracteristica = "vrdad_pdre" . $i;
                $origen = "id_pr_pdre" . $i;
                $cruzamiento->$caracteristica = $flor_padre[0]; //$padre[$i-1];
                $id_pr_padre_auto = $proyecto_padre; //$this->obtener_id_flor_cruzamiento($proyecto_padre,$padre[$i-1],$flor_padre[2]);
                $cruzamiento->$origen = $id_pr_padre_auto;
                $cruzamiento->grpo_crzmnto_pdre = $caracter_padre;
                $this->obtenerIdFlorCruzamiento($proyecto_padre, $flor_padre[0], $flor_padre[2]);
            }
        }
        $cruzamiento->save();

        if ($autofecundado == 1) {
            $padre = explode(",", $padres);
            $flor_padre = explode("_", $padre[0]);
            $proyecto_padre = str_replace("9999", "", $flor_padre[1]);
            $cruzamiento_auto = new Crossing;
            $cruzamiento_auto->vrdad_mdre = $flor_padre[0];
            $cruzamiento_auto->id_pr_mdre = $proyecto_padre;
            $cruzamiento_auto->vrdad_pdre1 = $flor_padre[0];
            $cruzamiento_auto->grpo_crzmnto_pdre = $flor_padre[2];
            $cruzamiento_auto->grpo_crzmnto_mdre = $flor_padre[2];
            $cruzamiento_auto->id_pr_pdre1 = $proyecto_padre;
            $cruzamiento_auto->obsrvcnes = $observaciones;
            $cruzamiento_auto->fcha_crzmnto = DB::raw('now()');
            $cruzamiento_auto->usuario_creacion = $usuario ? $usuario->id_usrio : null;
            $cruzamiento_auto->proyecto = $proyecto_padre;
            $cruzamiento_auto->id_ponderados = $idPonderado;
            $cruzamiento_auto->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cruzamiento guardado con éxito'
        ]);
    }
    public function obtenerIdFlorCruzamiento($proyecto, $vrdad, $caracter)
    {
        $fechaFin = now()->format('Y-m-d');
        $fechaInicio = now()->subDay()->format('Y-m-d');

        $flores = DB::connection('sivar')
            ->table('floracion')
            ->whereBetween('floracion.fcha', [$fechaInicio, $fechaFin])
            ->where('floracion.id_pr', $proyecto)
            ->where('floracion.id_crcter', $caracter)
            ->where('floracion.estado', 0)
            ->where('floracion.vrdad', $vrdad)
            ->first();
        if ($flores) {
            $idFlor = $flores->id_flrcion;
            DB::connection('sivar')
                ->table('floracion')
                ->where('id_flrcion', $idFlor)
                ->update(['estado' => 1]);
            return $idFlor;
        }

        return null;
    }
    public function guardarPonderados(Request $request, $proyecto)
    {
        $idPonderado = Carbon::now()->toDateTimeString() . "++" . $proyecto;

        $ponderados = DB::connection('sivar')
            ->table('caracteristicas_valor_merito')
            ->leftJoin('ponderados_valor_merito', function ($join) use ($proyecto) {
                $join->on('ponderados_valor_merito.id_caracteristica', '=', 'caracteristicas_valor_merito.id_caracteristica')
                    ->where('ponderados_valor_merito.id_proyecto', $proyecto);
            })
            ->select('ponderados_valor_merito.*', 'caracteristicas_valor_merito.nombre', 'caracteristicas_valor_merito.id_caracteristica')
            ->get();

        foreach ($ponderados as $ponderado) {
            $nuevoPonderado = new PonderadoCruzamiento();
            $nuevoPonderado->id_ponderado = $idPonderado;
            $nuevoPonderado->id_caracteristica = $ponderado->id_caracteristica;
            $nuevoPonderado->nivel = $ponderado->nivel;
            $nuevoPonderado->ponderado = $ponderado->ponderado;
            $nuevoPonderado->save();
        }

        return $idPonderado;
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
