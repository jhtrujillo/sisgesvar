<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DisenoEncabezado;
use App\Models\DisenoDetalle;
use App\Models\Cruzamiento;
use App\Models\Proyecto;
use App\Models\Area;
use App\Models\ConfCampo;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Throwable;

class ExperimentosController extends Controller
{

    // public $modelClass = DisenoEncabezado::class;
    // public $moduleView = 'modules.experimentos.main';
    // public $paramView = array(
    //     // "title" => "Sivar",
    //     // "subtitle" => "Módulo de experimentos",
    //     "breadcrum" => array(
    //         array(
    //             "label" => "Home",
    //             "route" => "admin"
    //         ),
    //         array(
    //             "label" => "Experimentos",
    //         ),
    //     )
    // );

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // public function index()
    // {
    //     return view($this->moduleView, $this->paramView);
    // }

    public function getSearchParameters()
    {
        try {
            // Obtener programas
            $programas = DB::connection('sivar')->table('remote_pg_areas_cc')
                ->select('id_area AS id', 'nmbre AS text')
                ->distinct()
                ->whereNotNull('id_area')
                ->orderBy('nmbre', 'desc')
                ->get();

            // Obtener áreas
            $areas = DB::connection('sivar')->table('remote_pg_areas_cc')
                ->select('id_area_trbjo AS id', 'nm_area_trbjo AS text')
                ->distinct()
                ->orderBy('nm_area_trbjo', 'asc')
                ->get();

            // Obtener proyectos con join para enriquecer datos
            $proyectos = DB::connection('sivar')->table('remote_pg_sipro')
                ->select(
                    'remote_pg_sipro.id_prycto AS id',
                    'remote_pg_sipro.nm_prycto AS text',
                    'remote_pg_areas_cc.id_area_trbjo',
                    'remote_pg_areas_cc.nm_area_trbjo'
                )
                ->join('remote_pg_areas_cc', function ($join) {
                    $join->on('remote_pg_sipro.id_area_trbjo', '=', 'remote_pg_areas_cc.id_area_trbjo')
                        ->on('remote_pg_sipro.id_area', '=', 'remote_pg_areas_cc.id_area');
                })
                ->orderBy('remote_pg_sipro.nm_prycto', 'asc')
                ->get();

            // Obtener temporadas de cruzamiento
            $temporadasCruzamiento = DB::connection('sivar')->table('cruzamientos')
                ->select('ano AS id', 'ano AS text')
                ->distinct()
                ->whereNotNull('ano')
                ->orderBy('ano', 'desc')
                ->get();

            // Obtener diseños experimentales
            $disenoExperimental = DB::connection('sivar')->table('diseno_experimental')
                ->select('id_dsno_exprmntal AS id', 'nm_dsno_exprmntal AS text')
                ->whereNotIn('id_dsno_exprmntal', [2, 3])
                ->orderBy('id_dsno_exprmntal', 'asc')
                ->get();

            // Obtener áreas de campo y variables asociadas
            $areasCampo = DB::connection('sivar')->table('conf_campos')
                ->select('area')
                ->distinct()
                ->whereNotNull('area')
                ->orderBy('area', 'asc')
                ->get();

            $variables = [];
            foreach ($areasCampo as $key => $value) {
                $variables[$key]['area'] = $value->area;
                $variables[$key]['variables'] = DB::connection('sivar')->table('conf_campos')
                    ->select('nmro_cmpo', 'nmbre_cmpo')
                    ->where([
                        ['area', '=', $value->area],
                        ['nmbre_cmpo', '<>', '""']
                    ])
                    ->whereNotNull('nmbre_cmpo')
                    ->orderBy('nmbre_cmpo', 'asc')
                    ->get();
            }

            // Construir respuesta
            $response = [
                "listProgramas" => $programas,
                "listAreas" => $areas,
                "listProyectos" => $proyectos,
                "listSeries" => [],
                "listEstados" => [
                    ["id" => 1, "text" => "E1 en prueba"],
                ],
                "listTemporadas" => $temporadasCruzamiento,
                "listCruzamientoMadre" => [
                    ["id" => 1, "text" => "EZH"],
                    ["id" => 2, "text" => "EZS"],
                    ["id" => 3, "text" => "EZP"],
                ],
                "listCruzamientoPadre" => [
                    ["id" => 1, "text" => "EZH"],
                    ["id" => 2, "text" => "EZS"],
                    ["id" => 3, "text" => "EZS-EZH-EZP"],
                ],
                "listTipoEnsayo" => [
                    ["id" => 'F', "text" => "Familias"],
                    ["id" => 'I', "text" => "Individual"],
                ],
                "listTipoParcela" => [
                    ["id" => 'pp', "text" => "Principal"],
                    ["id" => 'sp', "text" => "Subparcelas"],
                ],
                "listDisenoExp" => $disenoExperimental,
                "listVariables" => $variables,
            ];

            // Agregar series de años
            for ($i = (date("Y") + 4); $i >= 1996; $i--) {
                $response['listSeries'][] = [
                    "id" => $i,
                    "text" => $i
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar los parámetros de búsqueda.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAreasProgram($id_area)
    {
        try {

            // Consultar áreas relacionadas al programa
            $areas = Area::select('id_area_trbjo AS id', 'nm_area_trbjo AS text')
                ->where('id_area', $id_area)
                ->distinct()
                ->orderBy('nm_area_trbjo', 'asc')
                ->get();

            // Construir respuesta
            return response()->json([
                'success' => true,
                'listAreas' => $areas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las áreas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProjectsArea($id_area_trbjo)
    {
        try {
            // Consultar los proyectos relacionados al área
            $proyectos = Proyecto::select(
                'remote_pg_sipro.id_prycto AS id',
                'remote_pg_sipro.nm_prycto AS text',
                'remote_pg_areas_cc.id_area_trbjo',
                'remote_pg_areas_cc.nm_area_trbjo'
            )
                ->join('remote_pg_areas_cc', function ($join) {
                    $join->on('remote_pg_sipro.id_area_trbjo', '=', 'remote_pg_areas_cc.id_area_trbjo')
                        ->on('remote_pg_sipro.id_area', '=', 'remote_pg_areas_cc.id_area');
                })
                ->where('remote_pg_sipro.id_area_trbjo', $id_area_trbjo)
                ->orderBy('remote_pg_sipro.nm_prycto', 'asc')
                ->get();

            // Formatear respuesta
            return response()->json([
                'success' => true,
                'listProyectos' => $proyectos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los proyectos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getExperiment($id_pr, $srie, $estdo)
    {

        try {

            // Consulta para obtener los experimentos
            $experimento = DisenoEncabezado::select('*')
                ->where([
                    ['id_pr', '=', $id_pr],
                    ['srie', '=', $srie],
                    ['estdo', '=', $estdo],
                ])
                ->orderBy('tpo_ensyo', 'asc')
                ->get();

            // Preparar y devolver la respuesta
            return response()->json([
                'success' => true,
                'experimento' => $experimento,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los experimentos.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getCriteriosSeleccion()
    {
        try {
            // Obtener las temporadas de cruzamiento
            $temporadaCrzmnto = DB::connection('sivar')->table('cruzamientos')
                ->select('ano AS id', 'ano AS text')
                ->distinct()
                ->orderBy('ano', 'desc')
                ->get();

            // Preparar respuesta
            return response()->json([
                'success' => true,
                'listTemporada' => $temporadaCrzmnto,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los criterios de selección.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function grabarEncabezado($estdo, $srie, $id_pr, $id_ambnte)
    {

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Crear el primer encabezado (tpo_ensyo 'F')
            $newDisenoEncabezadoF = new DisenoEncabezado();
            $newDisenoEncabezadoF->estdo = $estdo;
            $newDisenoEncabezadoF->srie = $srie;
            $newDisenoEncabezadoF->id_pr = $id_pr;
            $newDisenoEncabezadoF->id_ambnte = $id_ambnte;
            $newDisenoEncabezadoF->tpo_ensyo = 'F';
            $newDisenoEncabezadoF->prgrso = 1;
            $newDisenoEncabezadoF->save();

            // Verificar si se guardó correctamente el primer encabezado
            if (!$newDisenoEncabezadoF->id_dsno_enc) {
                DB::rollBack();
                return response()->json([
                    'code' => 400,
                    'message' => 'Error guardando el primer encabezado del experimento'
                ], 400);
            }

            // Crear el segundo encabezado (tpo_ensyo 'I')
            $newDisenoEncabezadoI = new DisenoEncabezado();
            $newDisenoEncabezadoI->estdo = $estdo;
            $newDisenoEncabezadoI->srie = $srie;
            $newDisenoEncabezadoI->id_pr = $id_pr;
            $newDisenoEncabezadoI->id_ambnte = $id_ambnte;
            $newDisenoEncabezadoI->tpo_ensyo = 'I';
            $newDisenoEncabezadoI->prgrso = 1;
            $newDisenoEncabezadoI->save();

            // Verificar si se guardó correctamente el segundo encabezado
            if (!$newDisenoEncabezadoI->id_dsno_enc) {
                // Si falla, revertimos el primer registro
                $newDisenoEncabezadoF->delete();
                DB::rollBack();
                return response()->json([
                    'code' => 400,
                    'message' => 'Error guardando el segundo encabezado del experimento'
                ], 400);
            }

            // Commit de la transacción si ambos registros se guardaron correctamente
            DB::commit();

            // Respuesta de éxito con los ids de los diseños creados
            return response()->json([
                'code' => 200,
                'message' => 'Se guarda el experimento con éxito',
                'IdsDisenosCreados' => [
                    'idDisenoF' => $newDisenoEncabezadoF->id_dsno_enc,
                    'idDisenoI' => $newDisenoEncabezadoI->id_dsno_enc
                ]
            ], 200);
        } catch (Throwable $th) {
            // Si ocurre una excepción, revertir transacción
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => 'Error guardando el experimento',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getTreatmentsSeason($ano, $id_dsno_enc, $min_plantulas, $plantulas_ttles)
    {
        try {
            $min_plantulas   = ($min_plantulas   == NULL || $min_plantulas   == '') ? 0 : $min_plantulas;
            $plantulas_ttles = ($plantulas_ttles == NULL || $plantulas_ttles == '') ? 0 : $plantulas_ttles;

            // Realizar la consulta a la base de datos
            $tratamientosDisponibles = DB::connection('sivar')->table('cruzamientos')
                ->select(
                    'id_crzmnto',
                    'no_crzmnto',
                    'pdgree',
                    'orgen',
                    DB::raw('(COALESCE(plntlas_ttles, 0) - COALESCE(plntlas_dscrtdas, 0)) AS plntlas_ttles'),
                    'grpo_crzmnto',
                    'grpo_crzmnto_mdre',
                    'grpo_crzmnto_pdre'
                )
                ->where('ano', $ano)
                ->where(DB::raw('(COALESCE(plntlas_ttles, 0) - COALESCE(plntlas_dscrtdas, 0))'), '>', 0)
                ->where(function ($query) use ($min_plantulas, $plantulas_ttles) {
                    $query->where(DB::raw('(COALESCE(plntlas_ttles, 0) - COALESCE(plntlas_dscrtdas, 0))'), '>=', $min_plantulas)
                        ->where(DB::raw('(COALESCE(plntlas_ttles, 0) - COALESCE(plntlas_dscrtdas, 0))'), '>=', $plantulas_ttles);
                })
                ->whereNotIn(DB::raw('CAST(id_crzmnto AS varchar)'), function ($subQuery) use ($id_dsno_enc) {
                    $subQuery->select('trtmnto')
                        ->from('diseno_det')
                        ->where('id_dsno_enc', $id_dsno_enc);
                })
                ->distinct()
                ->orderBy('pdgree')
                ->get();

            // Preparar respuesta
            return response()->json([
                'success' => true,
                'tratamientos' => $tratamientosDisponibles,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tratamientos disponibles.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addDisenoDetalle($id_dsno_enc, $cTestigo, $nTipoParcela, $nTotalPlantas, $arrIds)
    {
        try {

            // Verificar que el número total de plantas no sea nulo ni vacío
            $plntlas_ttles = $nTotalPlantas ?? 0;

            // Obtener el valor máximo de 'entrda' para el diseño actual
            $maxEntrada = DisenoDetalle::select(DB::raw('MAX(entrda) as maxentrada'))
                ->where('id_dsno_enc', $id_dsno_enc)
                ->first()->maxentrada;

            // Si no existe ninguna entrada, iniciar en 1
            if (!$maxEntrada) {
                $maxEntrada = 1;
            } else {
                $maxEntrada++;
            }

            // Crear un nuevo detalle de diseño
            $newDisenoDetalle = new DisenoDetalle();
            $newDisenoDetalle->id_dsno_enc = $id_dsno_enc;
            $newDisenoDetalle->entrda = $maxEntrada;
            $newDisenoDetalle->trtmnto = $arrIds;
            $newDisenoDetalle->nmro_clnes = $plntlas_ttles;
            $newDisenoDetalle->tstgo = $cTestigo;
            $newDisenoDetalle->tpo_prcla = $nTipoParcela;
            $newDisenoDetalle->save();

            // Contar las entradas y testigos
            $entradas = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'No']])->count();
            $testigos = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'Si']])->count();
            $testigos_moviles = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'Movil']])->count();
            $parcela_ppal = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'No'], ['tpo_prcla', 'pp']])->count();
            $sub_parcela = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'No'], ['tpo_prcla', 'sp']])->count();

            // Actualizar el encabezado de diseño con los nuevos valores
            $disenoEnc = DisenoEncabezado::find($id_dsno_enc);
            $disenoEnc->entrdas = $entradas;
            $disenoEnc->tstgos = $testigos;
            $disenoEnc->tstgos_mvil = $testigos_moviles;
            $disenoEnc->prcla_prncpal = $parcela_ppal;
            $disenoEnc->sub_prclas = $sub_parcela;
            $disenoEnc->save();

            // Preparar la respuesta con el nuevo detalle de diseño
            return response()->json([
                'success' => true,
                'newDisenoDetalle' => $newDisenoDetalle,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el diseño de detalle.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addDisenosDetalles($id_dsno_enc, $nTipoParcela, $cTestigo, $nTotalPlantas, $arrIds)
    {

        try {

            // Verificar si se van a usar todas las plantas
            $allPlantulas = false;
            if (($nTotalPlantas == NULL) || ($nTotalPlantas == '')) {
                $allPlantulas = true;
            } else {
                $plntlas_ttles = $nTotalPlantas;
            }

            // Obtener el valor máximo de 'entrda' para el diseño actual
            $maxEntrada = DisenoDetalle::select(DB::raw('MAX(entrda) as maxentrada'))
                ->where('id_dsno_enc', $id_dsno_enc)
                ->first()->maxentrada;

            // Si no existe ninguna entrada, iniciar en 1
            if (!$maxEntrada) {
                $maxEntrada = 1;
            } else {
                $maxEntrada++;
            }

            $idDetalCreados = array();

            // Procesar cada tratamiento del array
            foreach ($arrIds as $key => $value) {
                if ($cTestigo == 'No') {
                    // Actualizar la cantidad de plantas en el tratamiento
                    $tratamiento = Cruzamiento::find($value['id_crzmnto']);
                    $tratamiento->plntlas_ttles = $tratamiento->plntlas_ttles - ($allPlantulas ? $value['plntlas_ttles'] : $plntlas_ttles);
                    $saveTratamiento = $tratamiento->save();
                } else {
                    $saveTratamiento = true;
                }

                if ($saveTratamiento) {
                    // Crear un nuevo detalle de diseño
                    $newDisenoDetalle = new DisenoDetalle();
                    $newDisenoDetalle->id_dsno_enc = $id_dsno_enc;
                    $newDisenoDetalle->entrda = $maxEntrada;
                    $newDisenoDetalle->trtmnto = $value['id_crzmnto'];
                    $newDisenoDetalle->nmro_clnes = $allPlantulas ? $value['plntlas_ttles'] : $plntlas_ttles;
                    $newDisenoDetalle->tstgo = $cTestigo;
                    $newDisenoDetalle->tpo_prcla = $nTipoParcela;
                    $idDetalCreados[] = $newDisenoDetalle->save();

                    $maxEntrada++;
                }
            }

            // Actualizar las entradas del experimento
            $experimento = $this->updateEntradasExperimento($id_dsno_enc);

            // Preparar la respuesta con los detalles creados
            return response()->json([
                'success' => true,
                'idDetalCreados' => $idDetalCreados,
                'experimento' => $experimento,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar los detalles de diseño.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateEntradasExperimento($id_dsno_enc)
    {
        try {
            // Contar las entradas y testigos
            $entradas = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'No']])->count();
            $testigos = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'Si']])->count();
            $testigos_moviles = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'Movil']])->count();
            $parcela_ppal = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'No'], ['tpo_prcla', 'pp']])->count();
            $sub_parcela = DisenoDetalle::where([['id_dsno_enc', $id_dsno_enc], ['tstgo', 'No'], ['tpo_prcla', 'sp']])->count();

            // Actualizar el encabezado de diseño con los nuevos valores
            $disenoEnc = DisenoEncabezado::find($id_dsno_enc);
            $disenoEnc->entrdas = $entradas;
            $disenoEnc->tstgos = $testigos;
            $disenoEnc->tstgos_mvil = $testigos_moviles;
            $disenoEnc->prcla_prncpal = $parcela_ppal;
            $disenoEnc->sub_prclas = $sub_parcela;
            $disenoEnc->save();

            return $disenoEnc;
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar las entradas del experimento.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTreatmentsExperiments($id_dsno_enc_f, $id_dsno_enc_i)
    {
        try {

            // Obtener tratamientos de los experimentos
            $tratamientoF = DisenoDetalle::select(
                'diseno_det.id_dsno_det',
                'diseno_det.id_dsno_enc',
                'diseno_det.trtmnto',
                'cruzamientos.no_crzmnto', // Familia
                'cruzamientos.pdgree', // Pedegree
                'cruzamientos.orgen', // Origen
                'diseno_det.nmro_clnes', // No. Plantas
                DB::raw('(COALESCE(cruzamientos.plntlas_ttles, 0) - COALESCE(cruzamientos.plntlas_dscrtdas,0)) AS plntlas_ttles')
            )
                ->join('cruzamientos', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'), '=', 'diseno_det.trtmnto')
                ->where([
                    ['diseno_det.id_dsno_enc', $id_dsno_enc_f],
                    ['tstgo', 'No'],
                    ['tpo_prcla', 'pp']
                ])
                ->get();

            $tratamientoI = DisenoDetalle::select(
                'diseno_det.id_dsno_det',
                'diseno_det.id_dsno_enc',
                'diseno_det.trtmnto',
                'cruzamientos.no_crzmnto', // Familia
                'cruzamientos.pdgree', // Pedegree
                'cruzamientos.orgen', // Origen
                'diseno_det.nmro_clnes', // No. Plantas
                DB::raw('(COALESCE(cruzamientos.plntlas_ttles, 0) - COALESCE(cruzamientos.plntlas_dscrtdas,0)) AS plntlas_ttles')
            )
                ->join('cruzamientos', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'), '=', 'diseno_det.trtmnto')
                ->where([
                    ['diseno_det.id_dsno_enc', $id_dsno_enc_i],
                    ['tstgo', 'No'],
                    ['tpo_prcla', 'pp']
                ])
                ->get();

            // Obtener los testigos fijos y móviles para ambos experimentos
            $testigosFijosF = DB::connection('sivar')->table('maestro_V_VIC_BG')
                ->select('diseno_det.id_dsno_det', 'nm_vrdad', 'pdgree', 'orgen')
                ->join('diseno_det', function ($join) {
                    $join->on('maestro_V_VIC_BG.nm_vrdad', '=', 'diseno_det.trtmnto');
                })
                ->where([
                    ['diseno_det.id_dsno_enc', $id_dsno_enc_f],
                    ['tstgo', 'Si']
                ])
                ->orderBy('maestro_V_VIC_BG.nm_vrdad')
                ->get();

            $testigosFijosI = DB::connection('sivar')->table('maestro_V_VIC_BG')
                ->select('diseno_det.id_dsno_det', 'nm_vrdad', 'pdgree', 'orgen')
                ->join('diseno_det', function ($join) {
                    $join->on('maestro_V_VIC_BG.nm_vrdad', '=', 'diseno_det.trtmnto');
                })
                ->where([
                    ['diseno_det.id_dsno_enc', $id_dsno_enc_i],
                    ['tstgo', 'Si']
                ])
                ->orderBy('maestro_V_VIC_BG.nm_vrdad')
                ->get();

            $testigosMovilesF = DB::connection('sivar')->table('maestro_V_VIC_BG')
                ->select('diseno_det.id_dsno_det', 'nm_vrdad', 'pdgree', 'orgen')
                ->join('diseno_det', function ($join) {
                    $join->on('maestro_V_VIC_BG.nm_vrdad', '=', 'diseno_det.trtmnto');
                })
                ->where([
                    ['diseno_det.id_dsno_enc', $id_dsno_enc_f],
                    ['tstgo', 'Movil']
                ])
                ->orderBy('maestro_V_VIC_BG.nm_vrdad')
                ->get();

            $testigosMovilesI = DB::connection('sivar')->table('maestro_V_VIC_BG')
                ->select('diseno_det.id_dsno_det', 'nm_vrdad', 'pdgree', 'orgen')
                ->join('diseno_det', function ($join) {
                    $join->on('maestro_V_VIC_BG.nm_vrdad', '=', 'diseno_det.trtmnto');
                })
                ->where([
                    ['diseno_det.id_dsno_enc', $id_dsno_enc_i],
                    ['tstgo', 'Movil']
                ])
                ->orderBy('maestro_V_VIC_BG.nm_vrdad')
                ->get();

            // Obtener detalles de los experimentos
            $experimentoF = DisenoEncabezado::find($id_dsno_enc_f);
            $experimentoI = DisenoEncabezado::find($id_dsno_enc_i);

            // Obtener los tratamientos de salida
            $distTratF = DB::connection('sivar')->table('dissalida_det')
                ->select(
                    'dissalida_det.id_dsno_enc',
                    'dissalida_det.id_dissalida_det',
                    'dissalida_det.lcldad',
                    'dissalida_det.rptcion',
                    'dissalida_det.entrda',
                    'dissalida_det.block',
                    'maestro_V_VIC_BG.nm_vrdad as trtmnto',
                    DB::raw('\'\' as pdgree'),
                    'dissalida_det.prcla',
                    'maestro_V_VIC_BG.nm_vrdad as orgen',
                    'dissalida_det.tstgo',
                    DB::raw('0 as nmro_clnes')
                )
                ->join('maestro_V_VIC_BG', function ($join) {
                    $join->on('dissalida_det.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
                })
                ->where('id_dsno_enc', $id_dsno_enc_f)
                ->union(
                    DB::connection('sivar')->table('dissalida_det')
                        ->select(
                            'dissalida_det.id_dsno_enc',
                            'dissalida_det.id_dissalida_det',
                            'dissalida_det.lcldad',
                            'dissalida_det.rptcion',
                            'dissalida_det.entrda',
                            'dissalida_det.block',
                            'cruzamientos.nm_fmlias as trtmnto',
                            'cruzamientos.pdgree',
                            'dissalida_det.prcla',
                            'cruzamientos.orgen',
                            'dissalida_det.tstgo',
                            'dissalida_det.nmro_clnes'
                        )
                        ->join('cruzamientos', function ($join) {
                            $join->on('dissalida_det.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                        })
                        ->where('id_dsno_enc', $id_dsno_enc_f)
                )
                ->orderBy('id_dissalida_det', 'asc')
                ->get();

            $distTratI = DB::connection('sivar')->table('dissalida_det')
                ->select(
                    'dissalida_det.id_dsno_enc',
                    'dissalida_det.id_dissalida_det',
                    'dissalida_det.lcldad',
                    'dissalida_det.rptcion',
                    'dissalida_det.entrda',
                    'dissalida_det.block',
                    'maestro_V_VIC_BG.nm_vrdad as trtmnto',
                    DB::raw('\'\' as pdgree'),
                    'dissalida_det.prcla',
                    'maestro_V_VIC_BG.nm_vrdad as orgen',
                    'dissalida_det.tstgo',
                    DB::raw('0 as nmro_clnes')
                )
                ->join('maestro_V_VIC_BG', function ($join) {
                    $join->on('dissalida_det.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
                })
                ->where('id_dsno_enc', $id_dsno_enc_i)
                ->union(
                    DB::connection('sivar')->table('dissalida_det')
                        ->select(
                            'dissalida_det.id_dsno_enc',
                            'dissalida_det.id_dissalida_det',
                            'dissalida_det.lcldad',
                            'dissalida_det.rptcion',
                            'dissalida_det.entrda',
                            'dissalida_det.block',
                            'cruzamientos.nm_fmlias as trtmnto',
                            'cruzamientos.pdgree',
                            'dissalida_det.prcla',
                            'cruzamientos.orgen',
                            'dissalida_det.tstgo',
                            'dissalida_det.nmro_clnes'
                        )
                        ->join('cruzamientos', function ($join) {
                            $join->on('dissalida_det.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                        })
                        ->where('id_dsno_enc', $id_dsno_enc_i)
                )
                ->orderBy('id_dissalida_det', 'asc')
                ->get();

            // Enviar respuesta
            return response()->json([
                'tratamientosF' => $tratamientoF,
                'tratamientosI' => $tratamientoI,
                'testigosFijosF' => $testigosFijosF,
                'testigosFijosI' => $testigosFijosI,
                'testigosMovilesF' => $testigosMovilesF,
                'testigosMovilesI' => $testigosMovilesI,
                'distTratF' => $distTratF,
                'distTratI' => $distTratI,
                'experimentoF' => $experimentoF,
                'experimentoI' => $experimentoI,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los datos.'], 500);
        }
    }

    public function removeDetalle($id_dsno_enc, $arrIds)
    {
        try {
            $detallesEliminado = [];

            // Procesar cada ID del arreglo para eliminar los detalles
            foreach ($arrIds as $key => $value) {
                $detalleEliminado = false;
                $detallesEliminado[$key]['id_detalle'] = $value['id_detalle'];

                // Buscar el detalle de diseño
                $detalleExperimento = DisenoDetalle::find($value['id_detalle']);

                // Verificar si el detalle existe
                if ($detalleExperimento) {
                    // Si no es un testigo
                    if ($detalleExperimento->tstgo == 'No') {
                        // Buscar el tratamiento y actualizar la cantidad de plantas
                        $tratamiento = Cruzamiento::find($value['id_crzmnto']);
                        if ($tratamiento) {
                            $tratamiento->plntlas_ttles += $value['nro_plntlas'];
                            $saveTratamiento = $tratamiento->save();
                        }
                    } else {
                        $saveTratamiento = true; // Si es un testigo, no actualizamos el tratamiento
                    }

                    // Si el tratamiento se guardó correctamente, proceder con la eliminación
                    if ($saveTratamiento) {
                        $detalleEliminado = $detalleExperimento->delete();
                    }
                }

                // Registrar si se eliminó el detalle
                $detallesEliminado[$key]['deleted'] = $detalleEliminado;
            }

            // Actualizar las entradas del experimento
            $experimento = $this->updateEntradasExperimento($id_dsno_enc);

            // Preparar la respuesta con los detalles eliminados
            if (count($detallesEliminado)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Detalles eliminados con éxito.',
                    'detallesEliminados' => $detallesEliminado,
                    'experimento' => $experimento,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar los detalles.',
                    'detallesEliminados' => $detallesEliminado,
                ], 400);
            }
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar los detalles.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function getRegistros($tipo, $tipo_registro, $search, $id_dsno_enc)
    {
        try {
            $where = '';

            // Establecer el tipo de registro para la consulta
            if ($tipo_registro == 'tf') {
                $where = 'Si';
            } elseif ($tipo_registro == 'tm') {
                $where = 'Movil';
            }

            // Inicializar la variable para los registros
            $registros = [];

            // Procesar los registros según el tipo
            switch ($tipo) {
                case 'tratamiento':
                    $registros = Cruzamiento::select(
                        'no_crzmnto', // Familia
                        'id_crzmnto as tratamiento',
                        DB::raw('concat_ws(\' - \', CAST(no_crzmnto AS text), pdgree, orgen) as name'), // name
                        'pdgree', // Pedegree
                        'orgen', // Origen
                        DB::raw('(COALESCE(plntlas_ttles, 0) - COALESCE(plntlas_dscrtdas,0)) AS nmro_clnes') // No. Plantas
                    )
                        ->where(DB::raw('COALESCE(plntlas_ttles, 0)'), '>', '0')
                        ->whereRaw('upper(concat_ws(\' - \', CAST(no_crzmnto AS text), pdgree, orgen)) like ?', ['%' . strtoupper($search) . '%'])
                        ->whereNotIn(DB::raw('CAST(id_crzmnto AS varchar)'), function ($q) use ($id_dsno_enc) {
                            $q->select('trtmnto')
                                ->from('diseno_det')
                                ->where('id_dsno_enc', $id_dsno_enc);
                        })
                        ->limit(100)
                        ->orderBy('no_crzmnto')
                        ->get();
                    break;

                case 'variedad':
                    $registros = DB::connection('sivar')->table('maestro_V_VIC_BG')
                        ->select(
                            'maestro_V_VIC_BG.nm_vrdad  as tratamiento',
                            DB::raw('concat_ws(\' - \', nm_vrdad, pdgree, procedencia.nm_prcdncia) as name') // name
                        )
                        ->leftJoin('procedencia', function ($join) {
                            $join->on('maestro_V_VIC_BG.id_prcdncia', '=', 'procedencia.id_prcdncia');
                        })
                        ->whereRaw('upper(nm_vrdad) like ?', ['%' . strtoupper($search) . '%'])
                        ->whereNotIn('maestro_V_VIC_BG.nm_vrdad', function ($q) use ($id_dsno_enc, $where) {
                            $q->select('trtmnto')
                                ->from('diseno_det')
                                ->where([['id_dsno_enc', $id_dsno_enc], ['tstgo', $where]]);
                        })
                        ->limit(100)
                        ->orderBy('maestro_V_VIC_BG.nm_vrdad')
                        ->get();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipo de registro no válido.',
                    ], 400);
            }

            // Devolver la respuesta
            return response()->json([
                'success' => true,
                'registros_count' => count($registros),
                'registros' => $registros,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los registros.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function grabarDiseno($id_dsno_enc, $id_dsno_exprmntal, $lclddes, $rptcnes, $blques, $entrdas, $prcla_prncpal, $sub_prclas, $tstgos, $tstgos_mvil, $dscrpcion)
    {
        try {

            // Iniciar transacción
            DB::beginTransaction();

            // Buscar el encabezado del diseño
            $disenoEnc = DisenoEncabezado::find($id_dsno_enc);

            if (!$disenoEnc) {
                // Si no se encuentra el encabezado, devolver error
                return response()->json([
                    'success' => false,
                    'message' => 'Encabezado de diseño no encontrado.',
                ], 404);
            }

            // Actualizar los valores del encabezado
            $disenoEnc->id_dsno_exprmntal = $id_dsno_exprmntal;
            $disenoEnc->lclddes = $lclddes;
            $disenoEnc->rptcnes = $rptcnes;
            $disenoEnc->blques = $blques;
            $disenoEnc->entrdas = $entrdas;
            $disenoEnc->prcla_prncpal = $prcla_prncpal;
            $disenoEnc->sub_prclas = $sub_prclas;
            $disenoEnc->tstgos = $tstgos;
            $disenoEnc->tstgos_mvil = $tstgos_mvil;
            $disenoEnc->dscrpcion = $dscrpcion;

            // Guardar los cambios en el encabezado
            if ($disenoEnc->save()) {
                // Si la operación fue exitosa, confirmar la transacción
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Se graba con éxito',
                    'tipo' => $disenoEnc->tpo_ensyo,
                    'actualizado' => true
                ], 200);
            } else {
                // Si falla la operación, revertir la transacción
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error grabando el diseño.',
                    'actualizado' => false
                ], 400);
            }
        } catch (Throwable $th) {
            // En caso de error, revertir la transacción y devolver un mensaje de error
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al grabar el diseño.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function saveGenericDesign($id_dsno_enc)
    {
        $saved = true;
        $errorSaving = '';
        $responseArray = [];

        try {
            // Cargar el diseño experimental
            $disenoExperimental = DB::connection('sivar')->table('diseno_enc as d')
                ->select('e.nm_fncion_php', 'd.*')
                ->join('diseno_experimental as e', function ($join) {
                    $join->on('e.id_dsno_exprmntal', '=', 'd.id_dsno_exprmntal');
                })
                ->where('id_dsno_enc', $id_dsno_enc)->get();

            if ($disenoExperimental->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el diseño experimental.',
                ], 404);
            }

            DB::beginTransaction();
            $this->iniciarParcela();

            // Crear las localidades según el diseño experimental
            for ($i = 1; $i <= $disenoExperimental[0]->lclddes; $i++) {
                switch ($disenoExperimental[0]->nm_fncion_php) {
                    case "bloques_completamente_azar":
                        $saved = $this->bloquesCompletamenteAzar($id_dsno_enc, $i, $disenoExperimental[0]->rptcnes);
                        break;

                    case "parcelas_divididas_t_aleatorio":
                        $saved = $this->parcelasDivididasTestigoAleatorio($id_dsno_enc, $i, $disenoExperimental[0]->rptcnes);
                        break;

                    case "lattice_simple":
                        $parametros = [
                            'modelo' => 'alpha_lattice',
                            'tratamientos' => 15,
                            'bloques' => 3,
                            'repeticiones' => 2,
                            'semilla' => 55,
                            'localidades' => 5
                        ];
                        $this->getModeloEstadistico($parametros);
                        $saved = $this->latticeSimple($id_dsno_enc, $i, $disenoExperimental[0]->rptcnes);
                        break;

                    default:
                        break;
                }

                if (!$saved) {
                    $errorSaving = 'No se pudo crear la localidad ' . $i;
                    break;
                }

                // Insertar localidad al libro
                $saved = DB::connection('sivar')->table('libro_det')->insert([
                    'id_dsno_enc' => $id_dsno_enc,
                    'lcldad' => $i
                ]);

                if (!$saved) {
                    $errorSaving = 'No se pudo agregar la localidad al libro';
                    break;
                }
            }

            // Si todo ha salido bien, hacer commit y actualizar el diseño
            if ($saved) {
                DB::commit();

                // Actualizar el encabezado del diseño
                $disenoEnc = DisenoEncabezado::find($id_dsno_enc);
                if ($disenoEnc) {
                    $disenoEnc->exste_dsno = 'Si';
                    $disenoEnc->save();
                }

                $responseArray = [
                    'success' => true,
                    'message' => 'El diseño se grabó con éxito.',
                    'tipo' => $disenoEnc ? $disenoEnc->tpo_ensyo : 'Desconocido',
                ];
            } else {
                DB::rollBack();
                $responseArray = [
                    'success' => false,
                    'message' => 'Error al grabar el diseño.',
                    'errorSaving' => $errorSaving,
                ];
            }

            return response()->json($responseArray, $saved ? 200 : 500);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al grabar el diseño.',
                'errorSaving' => 'Otro',
                'errorDetails' => $th->getMessage(),
            ], 500);
        }
    }


    public function generateDesign($id_dsno_enc)
    {
        $ejecucion = [];
        $errorSaving = '';
        $responseArray = [];

        try {
            // Cargar el diseño experimental
            $disenoExperimental = DB::connection('sivar')->table('diseno_enc as d')
                ->select('e.nm_fncion_php', 'd.*')
                ->join('diseno_experimental as e', function ($join) {
                    $join->on('e.id_dsno_exprmntal', '=', 'd.id_dsno_exprmntal');
                })
                ->where('id_dsno_enc', $id_dsno_enc)->first();

            if (!$disenoExperimental) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el diseño experimental.',
                ], 404);
            }

            $disenio = $disenoExperimental->nm_fncion_php;
            $localidades = $disenoExperimental->lclddes;
            $repeticiones = $disenoExperimental->rptcnes;
            $bloques = $disenoExperimental->blques;
            $entradas = $disenoExperimental->entrdas;
            $testigos = $disenoExperimental->tstgos;

            DB::beginTransaction();
            $this->iniciarParcela();

            switch ($disenio) {
                case "bloques_completamente_azar":
                    $ejecucion = $this->bloquesCompletamenteAzar($id_dsno_enc, $localidades, $repeticiones);
                    break;
                case "lattice_simple":
                case "lattice_triple":
                    $modelorandom = [
                        'Wichmann-Hill',
                        'Marsaglia-Multicarry',
                        'Super-Duper',
                        'Mersenne-Twister',
                        'Knuth-TAOCP',
                        'Knuth-TAOCP-2002'
                    ];

                    $tratamientos = $entradas + $testigos;
                    $tipo = ($disenio == 'lattice_simple') ? 2 : 3;
                    $semilla = random_int(0, 1000);
                    $nombrerandom = $modelorandom[random_int(0, 5)];

                    $parametros = [
                        'modelo' => 'lattice',
                        'tratamientos' => $tratamientos,
                        'tipo' => $tipo,
                        'semilla' => $semilla,
                        'nombrerandom' => $nombrerandom,
                        'localidades' => $localidades
                    ];

                    $ejecucion = $this->lattice($id_dsno_enc, $parametros);
                    break;
                case "alpha_lattice":
                    $tratamientos = $entradas + $testigos;
                    $semilla = random_int(0, 1000);

                    $parametros = [
                        'modelo' => 'alpha_lattice',
                        'tratamientos' => $tratamientos,
                        'bloques' => $bloques,
                        'repeticiones' => $repeticiones,
                        'semilla' => $semilla
                    ];

                    $ejecucion = $this->alpha($id_dsno_enc, $parametros);
                    break;
                case "bloques_aumentados":
                    $tratamientos = DB::connection('sivar')->table('diseno_det')
                        ->select('entrda')
                        ->where('id_dsno_enc', $id_dsno_enc)
                        ->where('tstgo', '=', 'No')
                        ->get();

                    $idsTratamientos = '"' . $tratamientos->pluck('entrda')->implode('}{') . '"';

                    $testigos = DB::connection('sivar')->table('diseno_det')
                        ->select('entrda')
                        ->where('id_dsno_enc', $id_dsno_enc)
                        ->where('tstgo', '=', 'Si')
                        ->get();

                    $idsTestigos = '"' . $testigos->pluck('entrda')->implode('}{') . '"';

                    $modelorandom = [
                        'Wichmann-Hill',
                        'Marsaglia-Multicarry',
                        'Super-Duper',
                        'Mersenne-Twister',
                        'Knuth-TAOCP',
                        'Knuth-TAOCP-2002'
                    ];

                    $semilla = random_int(0, 1000);
                    $nombrerandom = $modelorandom[random_int(0, 5)];

                    $parametros = [
                        'modelo' => 'bloques_aumentados',
                        'tratamientos' => $idsTratamientos,
                        'testigos' => $idsTestigos,
                        'repeticiones' => $repeticiones,
                        'semilla' => $semilla,
                        'nombrerandom' => $nombrerandom,
                        'localidades' => $localidades
                    ];

                    $ejecucion = $this->bloquesAumentados($id_dsno_enc, $parametros);
                    break;
                case "completamente_azar":
                    $ejecucion = $this->completamenteAzar($id_dsno_enc, $localidades, $repeticiones);
                    break;
                default:
                    $ejecucion = [
                        'saved' => false,
                        'message' => 'Experimento sin diseño válido.'
                    ];
                    break;
            }

            if ($ejecucion['saved']) {
                $ejecucion = $this->agregarLocalidad($id_dsno_enc, $localidades);
            }

            if ($ejecucion['saved']) {
                DB::commit();

                // Actualizar el diseño
                $disenoEnc = DisenoEncabezado::find($id_dsno_enc);
                if ($disenoEnc) {
                    $disenoEnc->exste_dsno = 'Si';
                    $disenoEnc->save();
                }

                $responseArray = [
                    'success' => true,
                    'message' => 'El diseño se generó con éxito.',
                    'tipo' => $disenoEnc ? $disenoEnc->tpo_ensyo : 'Desconocido',
                ];
            } else {
                DB::rollBack();
                $responseArray = [
                    'success' => false,
                    'message' => 'Error al generar el diseño.',
                    'errorSaving' => $ejecucion['message']
                ];
            }

            return response()->json($responseArray, $ejecucion['saved'] ? 200 : 500);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al generar el diseño.',
                'errorSaving' => 'Otro',
                'errorDetails' => $th->getMessage(),
            ], 500);
        }
    }


    public function iniciarParcela()
    {
        DB::connection('sivar')->select(DB::raw("SELECT setval('incremental', 1)"));
    }

    public function guardarRegistros($table, $modelo, $registros)
    {
        $saveSalida = DB::connection('sivar')->table($table)->insert($registros);
        if ($saveSalida) {
            return array(
                'saved' => true,
                'message' => 'Se guarda ' . $modelo
            );
        } else {
            return array(
                'saved' => false,
                'message' => 'Error guardando ' . $modelo
            );
        }
    }

    public function agregarLocalidad($id_dsno_enc, $localidades)
    {
        $infoLocalidad = array();
        $index = 0;

        for ($localidad = 1; $localidad <= $localidades; $localidad++) {
            $infoLocalidad[$index]['id_dsno_enc'] = $id_dsno_enc;
            $infoLocalidad[$index]['lcldad'] = $localidad;

            $index++;
        }

        return $this->guardarRegistros('libro_det', 'localidad', $infoLocalidad);
    }

    public function getModeloEstadistico($parametros)
    {
        $status = '';
        $message = '';
        $models = array();

        $client = new Client(['base_uri' => 'http://192.168.153.119']);
        $request = $client->request('GET', '/rwebService.php', ['query' => $parametros]);
        $status = $request->getStatusCode();

        if ($status == '200') {
            $models = json_decode($request->getBody()->getContents());

            if (!empty($models)) {
                $message = 'Se genera el modelo con éxito';
            } else {
                $message = 'Error generando el modelo';
            }
        } else {
            $message = 'No fue posible generar el modelo';
        }

        return array(
            'status' => $status,
            'message' => $message,
            'models' => $models
        );
    }

    public function bloquesCompletamenteAzar($id_dsno_enc, $localidades, $repeticiones)
    {
        $infoentrada = array();
        $index = 0;

        for ($localidad = 1; $localidad <= $localidades; $localidad++) {
            for ($repeticion = 1; $repeticion <= $repeticiones; $repeticion++) {
                $bloque = DB::connection('sivar')->table('diseno_det')
                    ->select('id_dsno_enc', 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes')
                    ->where('id_dsno_enc', $id_dsno_enc)
                    ->where(function ($q) {
                        $q->where('tstgo', '=', 'No')
                            ->orWhere('tstgo', '=', 'Si');
                    })
                    ->inRandomOrder()
                    ->get();

                foreach ($bloque as $registro) {
                    $infoentrada[$index]['id_dsno_enc'] = $registro->id_dsno_enc;
                    $infoentrada[$index]['lcldad'] = $localidad;
                    $infoentrada[$index]['rptcion'] = $repeticion;
                    $infoentrada[$index]['block'] = $repeticion;
                    $infoentrada[$index]['entrda'] = $registro->entrda;
                    $infoentrada[$index]['trtmnto'] = $registro->trtmnto;
                    $infoentrada[$index]['tstgo'] = $registro->tstgo;
                    $infoentrada[$index]['nmro_clnes'] = $registro->nmro_clnes;

                    $index++;
                }
            }
        }

        return $this->guardarRegistros('dissalida_det', 'diseño', $infoentrada);
    }

    public function completamenteAzar($id_dsno_enc, $localidades, $repeticiones)
    {
        $infoentrada = array();
        $index = 0;

        for ($localidad = 1; $localidad <= $localidades; $localidad++) {

            // $bloques = new \stdClass();
            $repArray = array();
            for ($repeticion = 1; $repeticion <= $repeticiones; $repeticion++) {
                // Repeticiones
                $repArray[] = "SELECT id_dsno_enc, $repeticion AS rptcion, $repeticion AS block, entrda, trtmnto, tstgo, nmro_clnes FROM diseno_det WHERE (id_dsno_enc = $id_dsno_enc) AND ((tstgo = 'No') OR (tstgo = 'Si')) ";

                // $bloque = DB::connection('sivar')->table('diseno_det')
                // ->select(DB::raw("$repeticion as rptcion, $repeticion as block"), 'id_dsno_enc', 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes')
                // ->where('id_dsno_enc', $id_dsno_enc)
                // ->where(function($q) {
                //     $q->where('tstgo', '=', 'No')
                //     ->orWhere('tstgo', '=', 'Si');
                // });

                // if ($repeticion == 1) {
                //     $bloques = $bloque;
                // } else {
                //     $bloques = $bloque->unionAll($bloques);
                // }
            }
            $sqlRepeticiones = implode(" UNION ALL ", $repArray);
            $sqlRepeticiones = "SELECT d.id_dsno_enc, rptcion, block, d.entrda, d.trtmnto, d.tstgo, d.nmro_clnes FROM (" . $sqlRepeticiones . ") AS d ORDER BY random()";

            // $registros = DB::connection('sivar')->table(DB::raw("({$bloques->toSql()}) AS s"))->select('*'); //$bloques->inRandomOrder()->get();
            // $registros = $bloques->get();

            $registros = DB::connection('sivar')->select($sqlRepeticiones);
            // dd($registros);

            foreach ($registros as $registro) {
                $infoentrada[$index]['id_dsno_enc'] = $registro->id_dsno_enc;
                $infoentrada[$index]['lcldad'] = $localidad;
                $infoentrada[$index]['rptcion'] = $registro->rptcion;
                $infoentrada[$index]['block'] = $registro->block;
                $infoentrada[$index]['entrda'] = $registro->entrda;
                $infoentrada[$index]['trtmnto'] = $registro->trtmnto;
                $infoentrada[$index]['tstgo'] = $registro->tstgo;
                $infoentrada[$index]['nmro_clnes'] = $registro->nmro_clnes;

                $index++;
            }
        }

        return $this->guardarRegistros('dissalida_det', 'diseño', $infoentrada);
    }

    public function lattice($id_dsno_enc, $parametros)
    {
        $resp = $this->getModeloEstadistico($parametros);
        $models = $resp['models'];

        if ($resp['status'] != '200') {
            dd('si entra es porque rweb falla');
        }

        $registros = DB::connection('sivar')->table('diseno_det')
            ->select('id_dsno_enc', 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes', 'tpo_prcla')
            ->where('id_dsno_enc', $id_dsno_enc)
            ->where(function ($q) {
                $q->where('tstgo', '=', 'No')
                    ->orWhere('tstgo', '=', 'Si');
            })
            ->orderBy('entrda', 'asc')
            ->get();

        $infoentrada = array();
        $index = 0;

        foreach ($models as $localidad => $book) {
            foreach ($book as $key => $plot) {
                $numTrt = $plot[4] - 1;

                $infoentrada[$index]['id_dsno_enc'] = $registros[$numTrt]->id_dsno_enc;
                $infoentrada[$index]['lcldad']      = $localidad;
                $infoentrada[$index]['rptcion']     = $plot[2];
                $infoentrada[$index]['block']       = $plot[3];
                $infoentrada[$index]['entrda']      = $plot[4];
                $infoentrada[$index]['trtmnto']     = $registros[$numTrt]->trtmnto;
                $infoentrada[$index]['tstgo']       = $registros[$numTrt]->tstgo;
                $infoentrada[$index]['nmro_clnes']  = $registros[$numTrt]->nmro_clnes;
                $infoentrada[$index]['row']         = null;
                $infoentrada[$index]['cols']        = null;
                $infoentrada[$index]['checks']      = null;
                // $infoentrada[$numTrt]['tpo_prcla'] = ($registros[$numTrt]->tpo_prcla == '') ? null : $registros[$numTrt]->tpo_prcla;

                $index++;
            }
        }

        return $this->guardarRegistros('dissalida_det', 'diseño', $infoentrada);
    }

    public function bloquesAumentados($id_dsno_enc, $parametros)
    {
        $resp = $this->getModeloEstadistico($parametros);
        $models = $resp['models'];

        if ($resp['status'] != '200') {
            dd('si entra es porque rweb falla');
        }

        $registros = DB::connection('sivar')->table('diseno_det')
            ->select('id_dsno_enc', 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes', 'tpo_prcla')
            ->where('id_dsno_enc', $id_dsno_enc)
            ->where(function ($q) {
                $q->where('tstgo', '=', 'No')
                    ->orWhere('tstgo', '=', 'Si');
            })
            ->orderBy('entrda', 'asc')
            ->get();

        $tratamientos = array();
        foreach ($registros as $registro) {
            $tratamientos[$registro->entrda] = $registro;
        }

        $infoentrada = array();
        $index = 0;

        foreach ($models as $localidad => $book) {
            $i = 0;
            foreach ($book as $key => $plot) {
                $numTrt = $plot[3];

                $infoentrada[$index]['id_dsno_enc'] = $tratamientos[$numTrt]->id_dsno_enc; //
                $infoentrada[$index]['lcldad']      = $localidad; //
                $infoentrada[$index]['rptcion']     = $plot[2]; //
                $infoentrada[$index]['block']       = $plot[2]; //
                $infoentrada[$index]['entrda']      = $plot[3]; //
                $infoentrada[$index]['trtmnto']     = $tratamientos[$numTrt]->trtmnto; //
                $infoentrada[$index]['tstgo']       = $tratamientos[$numTrt]->tstgo; //
                $infoentrada[$index]['nmro_clnes']  = $tratamientos[$numTrt]->nmro_clnes; //
                $infoentrada[$index]['row']         = null; //
                $infoentrada[$index]['cols']        = null; //
                $infoentrada[$index]['checks']      = ($tratamientos[$numTrt]->tstgo == 'No') ? "0"  : $i; //
                // $infoentrada[$numTrt]['tpo_prcla'] = ($tratamientos[$numTrt]->tpo_prcla == '') ? null : $tratamientos[$numTrt]->tpo_prcla;

                $index++;
                $i++;
            }
        }

        return $this->guardarRegistros('dissalida_det', 'diseño', $infoentrada);
    }

    public function alpha($id_dsno_enc, $parametros)
    {
        $resp = $this->getModeloEstadistico($parametros);
        $models = $resp['models'];

        if ($resp['status'] != '200') {
            dd('si entra es porque rweb falla');
        }

        $registros = DB::connection('sivar')->table('diseno_det')
            ->select('id_dsno_enc', 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes', 'tpo_prcla')
            ->where('id_dsno_enc', $id_dsno_enc)
            ->where(function ($q) {
                $q->where('tstgo', '=', 'No')
                    ->orWhere('tstgo', '=', 'Si');
            })
            ->orderBy('entrda', 'asc')
            ->get();

        $infoentrada = array();
        $index = 0;

        foreach ($models as $localidad => $book) {
            foreach ($book as $key => $plot) {
                $numTrt = $plot[4] - 1;

                $infoentrada[$index]['id_dsno_enc'] = $registros[$numTrt]->id_dsno_enc;
                $infoentrada[$index]['lcldad']      = $localidad;
                $infoentrada[$index]['rptcion']     = $plot[5];
                $infoentrada[$index]['block']       = $plot[3];
                $infoentrada[$index]['entrda']      = $plot[4];
                $infoentrada[$index]['trtmnto']     = $registros[$numTrt]->trtmnto; //
                $infoentrada[$index]['tstgo']       = $registros[$numTrt]->tstgo; //
                $infoentrada[$index]['nmro_clnes']  = $registros[$numTrt]->nmro_clnes; //
                $infoentrada[$index]['row']         = null;
                $infoentrada[$index]['cols']        = $plot[2]; // es null en v1
                $infoentrada[$index]['checks']      = null;
                // $infoentrada[$numTrt]['tpo_prcla'] = ($registros[$numTrt]->tpo_prcla == '') ? null : $registros[$numTrt]->tpo_prcla;

                $index++;
            }
        }

        return $this->guardarRegistros('dissalida_det', 'diseño', $infoentrada);
    }

    public function bloquesCompletamenteAzar2($id_dsno_enc, $lcldad, $repeticiones)
    {
        $bloques = array();
        for ($repeticion = 1; $repeticion <= $repeticiones; $repeticion++) {
            $bloque = DB::connection('sivar')->table('diseno_det')
                ->select('nmro_clnes')
                // ->select(DB::raw("$id_dsno_enc as id_dsno_enc, $lcldad as lcldad, $i as rptcion, $i as block"), 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes')
                ->where('id_dsno_enc', $id_dsno_enc)
                ->where(function ($q) {
                    $q->where('tstgo', '=', 'No')
                        ->orWhere('tstgo', '=', 'Si');
                })
                ->inRandomOrder()
                ->get();

            foreach ($bloque as $registro) {
                $saveSalida = DB::connection('sivar')->table('dissalida_det')
                    ->insert([
                        'id_dsno_enc' => $registro->id_dsno_enc,
                        'lcldad' => $registro->lcldad,
                        'rptcion' => $registro->rptcion,
                        'block' => $registro->block,
                        'entrda' => $registro->entrda,
                        'trtmnto' => $registro->trtmnto,
                        'tstgo' => $registro->tstgo,
                        'nmro_clnes' => $registro->nmro_clnes,
                    ]);

                if (!$saveSalida) {
                    return false;
                }
            }
        }

        return true;
    }

    public function parcelasDivididasTestigoAleatorio($id_dsno_enc, $lcldad, $repeticiones)
    {
        $this->iniciarParcela();
        $bloques = array();
        for ($i = 1; $i <= $repeticiones; $i++) {
            $bloque = DB::connection('sivar')->table('diseno_det')
                ->select(DB::raw("$id_dsno_enc as id_dsno_enc, $lcldad as lcldad, $i as rptcion, $i as block"), 'entrda', 'trtmnto', 'tstgo', 'nmro_clnes')
                ->where('id_dsno_enc', $id_dsno_enc)
                ->where(function ($q) {
                    $q->where('tstgo', '=', 'No')
                        ->orWhere('tstgo', '=', 'Si');
                })
                ->inRandomOrder()
                ->get();

            foreach ($bloque as $registro) {
                $saveSalida = DB::connection('sivar')->table('dissalida_det')
                    ->insert([
                        'id_dsno_enc' => $registro->id_dsno_enc,
                        'lcldad' => $registro->lcldad,
                        'rptcion' => $registro->rptcion,
                        'block' => $registro->block,
                        'entrda' => $registro->entrda,
                        'trtmnto' => $registro->trtmnto,
                        'tstgo' => $registro->tstgo,
                        'nmro_clnes' => $registro->nmro_clnes,
                    ]);

                if (!$saveSalida) {
                    return false;
                }
            }
        }

        return true;
    }
}
