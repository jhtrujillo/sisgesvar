<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DisenoEncabezado;
use App\Models\DisenoDetalle;
use App\Models\Cruzamiento;
use App\Models\Proyecto;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Throwable;

class LibroCampoController extends Controller
{

    // public $modelClass = DisenoEncabezado::class;
    // public $moduleView = 'modules.libro_campo.main';
    // public $paramView = array(
    //     // "title" => "Sivar",
    //     // "subtitle" => "Módulo de experimentos",
    //     "breadcrum" => array(
    //         array(
    //             "label" => "Home",
    //             "route" => "admin"
    //         ),
    //         array(
    //             "label" => "Libro de Campo",
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

    public function getLibroCampo($id_pr, $srie, $estdo)
    {
        try {
            // Obtener los registros de diseño de encabezado
            $diseno_enc = DisenoEncabezado::select()
                ->where([['id_pr', $id_pr], ['srie', $srie], ['estdo', $estdo]])
                ->orderBy('tpo_ensyo', 'asc')
                ->get();

            // Obtener las áreas de campo
            $areasCampo = DB::connection('sivar')->table('conf_campos')
                ->select('area')
                ->whereNotNull('area')
                ->distinct()
                ->orderBy('area', 'asc')
                ->get();

            // Obtener las variables asociadas a cada área
            $variables = [];
            foreach ($areasCampo as $key => $value) {
                $variables[$key]['area'] = $value->area;
                $variables[$key]['variables'] = DB::connection('sivar')->table('conf_campos')
                    ->select('nmro_cmpo', 'nmbre_cmpo')
                    ->where([['area', '=', $value->area], ['nmbre_cmpo', '<>', '""']])
                    ->whereNotNull('nmbre_cmpo')
                    ->orderBy('nmbre_cmpo', 'asc')
                    ->get();
            }

            // Variables para el libro de campo
            $libroF = [];
            $libroI = [];
            $listVariables = [];
            $error = 0;
            $mensajeError = [];

            // Verificar si existen los registros de diseño de encabezado
            if ($diseno_enc->isNotEmpty()) {
                $hasDatosCampo = false;

                foreach ($diseno_enc as $diseno) {
                    $idDsnoEnc = $diseno->id_dsno_enc;
                    $datosCampo = DB::connection('sivar')->table('datos_campo')
                        ->where('id_dsno_enc', $idDsnoEnc)
                        ->get();

                    if ($datosCampo->isNotEmpty()) {
                        $hasDatosCampo = true;
                        $dataLibro = $this->makeLibroCampoData($idDsnoEnc);
                        if ($diseno->tpo_ensyo == 'F') {
                            $libroF = $dataLibro;
                        } elseif ($diseno->tpo_ensyo == 'I') {
                            $libroI = $dataLibro;
                        }
                    }
                }

                if (!$hasDatosCampo) {
                    $listVariables = $variables;
                }
            } else {
                $error = 1;
                $mensajeError[] = 'El experimento no existe';
            }

            // Devolver la respuesta
            return response()->json([
                "experimento" => $diseno_enc,
                "listVariables" => $listVariables,
                "libroF" => $libroF,
                "libroI" => $libroI,
                "error" => $error,
                "mensajeError" => $mensajeError
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el libro de campo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function makeLibroCampoData($idDsnoEnc)
    {
        // Traer la distribución de tratamientos creada por el modelo estadístico en el diseño de experimentos
        $libroExperimento = DB::connection('sivar')->table('dissalida_det as dd')
            ->select(
                'dd.id_dsno_enc',
                'dd.id_dissalida_det',
                'dd.rptcion',
                'dd.entrda',
                'dd.lcldad',
                'dd.block',
                'maestro_V_VIC_BG.nm_vrdad as trtmnto',
                'dd.prcla',
                'dd.tstgo',
                DB::raw('0 as nmro_clnes')
            )
            ->join('maestro_V_VIC_BG', function ($join) {
                $join->on('dd.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
            })
            ->where('id_dsno_enc', $idDsnoEnc)
            ->union(
                DB::connection('sivar')->table('dissalida_det as dd2')
                    ->select(
                        'dd2.id_dsno_enc',
                        'dd2.id_dissalida_det',
                        'dd2.rptcion',
                        'dd2.entrda',
                        'dd2.lcldad',
                        'dd2.block',
                        'cruzamientos.nm_fmlias as trtmnto',
                        'dd2.prcla',
                        'dd2.tstgo',
                        DB::raw('0 as nmro_clnes')
                    )
                    ->join('cruzamientos', function ($join) {
                        $join->on('dd2.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                    })
                    ->where('id_dsno_enc', $idDsnoEnc)
            )
            ->orderBy('id_dissalida_det', 'asc')
            ->get();

        // Traer los campos del experimento
        $campos = DB::connection('sivar')->table('conf_campos as cc')
            ->select('cc.id_conf_campos', 'cc.nmro_cmpo', 'cc.nmbre_cmpo')
            ->whereIn(
                'cc.nmro_cmpo',
                DB::connection('sivar')->table('datos_campo as dc1')
                    ->select('dc1.nmro_cmpo')
                    ->where('dc1.id_dsno_enc', $idDsnoEnc)
                    ->distinct()
            )
            ->orderBy('cc.nmro_cmpo', 'asc')
            ->get();

        // Traer las variables del campo
        $variablesCampo = DB::connection('sivar')->table('dissalida_det')
            ->select(
                DB::raw('CONCAT(a.id_dissalida_det, \'-\', a.nmro_cmpo) as clave'),
                'dissalida_det.id_dissalida_det',
                'conf_campos.nmro_cmpo',
                'conf_campos.nmbre_cmpo',
                DB::raw('COALESCE(a.vlor, \'\') as vlor')
            )
            ->join('maestro_V_VIC_BG', function ($join) {
                $join->on('dissalida_det.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
            })
            ->join('datos_campo as a', function ($join) {
                $join->on('dissalida_det.id_dissalida_det', '=', 'a.id_dissalida_det');
            })
            ->join('conf_campos', function ($join) {
                $join->on('a.nmro_cmpo', '=', 'conf_campos.nmro_cmpo');
            })
            ->join(DB::raw('(SELECT max(COALESCE(c.id_fcha_evlcion, 0)) as id_fcha_evlcion, c.id_dissalida_det, c.nmro_cmpo, c.id_dsno_enc FROM datos_campo c GROUP BY (c.id_dissalida_det, c.nmro_cmpo, c.id_dsno_enc)) as c'), function ($join) {
                $join->on(DB::raw('COALESCE(a.id_fcha_evlcion, 0)'), '=', 'c.id_fcha_evlcion')
                    ->on('a.id_dsno_enc', '=', 'c.id_dsno_enc')
                    ->on('a.id_dissalida_det', '=', 'c.id_dissalida_det')
                    ->on('a.nmro_cmpo', '=', 'c.nmro_cmpo');
            })
            ->where('dissalida_det.id_dsno_enc', $idDsnoEnc)
            ->union(
                DB::connection('sivar')->table('dissalida_det')
                    ->select(
                        DB::raw('CONCAT(e.id_dissalida_det, \'-\', e.nmro_cmpo) as clave'),
                        'dissalida_det.id_dissalida_det',
                        'conf_campos.nmro_cmpo',
                        'conf_campos.nmbre_cmpo',
                        DB::raw('COALESCE(e.vlor, \'\') as vlor')
                    )
                    ->join('cruzamientos', function ($join) {
                        $join->on('dissalida_det.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                    })
                    ->join('datos_campo as e', function ($join) {
                        $join->on('dissalida_det.id_dissalida_det', '=', 'e.id_dissalida_det');
                    })
                    ->join('conf_campos', function ($join) {
                        $join->on('e.nmro_cmpo', '=', 'conf_campos.nmro_cmpo');
                    })
                    ->join(DB::raw('(SELECT max(COALESCE(f.id_fcha_evlcion, 0)) as id_fcha_evlcion, f.id_dissalida_det, f.nmro_cmpo, f.id_dsno_enc FROM datos_campo f GROUP BY (f.id_dissalida_det, f.nmro_cmpo, f.id_dsno_enc)) as f'), function ($join) {
                        $join->on(DB::raw('COALESCE(e.id_fcha_evlcion, 0)'), '=', 'f.id_fcha_evlcion')
                            ->on('e.id_dsno_enc', '=', 'f.id_dsno_enc')
                            ->on('e.id_dissalida_det', '=', 'f.id_dissalida_det')
                            ->on('e.nmro_cmpo', '=', 'f.nmro_cmpo');
                    })
                    ->where('dissalida_det.id_dsno_enc', $idDsnoEnc)
            )
            ->orderBy('id_dissalida_det', 'asc')
            ->orderBy('nmro_cmpo', 'asc')
            ->get()
            ->keyBy('clave');

        // Asignar los valores a las variables del libro
        foreach ($libroExperimento as $key1 => $variableExp) {
            foreach ($campos as $key2 => $campo) {
                $val = $campo->nmro_cmpo;
                $keyClave = $variableExp->id_dissalida_det . '-' . $campo->nmro_cmpo;
                $libroExperimento[$key1]->$val = isset($variablesCampo[$keyClave]) ? $variablesCampo[$keyClave]->vlor : '';
            }
        }

        return [
            'libroCampo' => $libroExperimento->values(),
            'camposLibro' => $campos
        ];
    }

    public function makeLibroCampo($idDsnoEnc)
    {
        try {
            $libro = $this->makeLibroCampoData($idDsnoEnc);
            return response()->json([
                'success' => true,
                'libro' => $libro,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el libro de campo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function crearLibroCampo(Request $request)
    {
        $libro = $request->input('libro', []);
        if (empty($libro)) {
            return response()->json([
                "code" => 400,
                "message" => 'No se recibieron datos para crear el libro de campo.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $totalInserts = 0;
            $hasSalidas = false;

            foreach ($libro as $datos) {
                if (empty($datos['id_dsno_enc']) || empty($datos['campos'])) {
                    continue;
                }

                $idDsnoEnc = $datos['id_dsno_enc'];

                // Obtener las salidas de diseño asociadas
                $salidas = DB::connection('sivar')->table('dissalida_det')
                    ->select('id_dissalida_det', 'id_dsno_enc')
                    ->where('id_dsno_enc', $idDsnoEnc)
                    ->get();

                if ($salidas->isNotEmpty()) {
                    $hasSalidas = true;

                    // Limpiar registros previos si se está re-configurando el libro
                    DB::connection('sivar')->table('datos_campo')
                        ->where('id_dsno_enc', $idDsnoEnc)
                        ->delete();

                    $batchInserts = [];
                    foreach ($salidas as $salida) {
                        foreach ($datos['campos'] as $variable) {
                            $batchInserts[] = [
                                'id_dissalida_det' => $salida->id_dissalida_det,
                                'id_dsno_enc' => $salida->id_dsno_enc,
                                'nmro_cmpo' => $variable['nmro_cmpo']
                            ];

                            if (count($batchInserts) >= 500) {
                                DB::connection('sivar')->table('datos_campo')->insert($batchInserts);
                                $totalInserts += count($batchInserts);
                                $batchInserts = [];
                            }
                        }
                    }

                    if (!empty($batchInserts)) {
                        DB::connection('sivar')->table('datos_campo')->insert($batchInserts);
                        $totalInserts += count($batchInserts);
                    }
                }
            }

            if (!$hasSalidas) {
                DB::rollBack();
                return response()->json([
                    "code" => 422,
                    "message" => 'El experimento aún no tiene parcelas de diseño (dissalida_det) generadas. Debe generar primero el diseño estadístico.'
                ], 422);
            }

            DB::commit();
            return response()->json([
                "code" => 200,
                "message" => 'Se creó el libro de campo con éxito (' . $totalInserts . ' registros inicializados).',
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "code" => 500,
                "message" => 'Error al crear el libro de campo: ' . $th->getMessage(),
            ], 500);
        }
    }
    
}


/*

                        $b = DB::connection('sivar')->table('dissalida_det')
                        ->select('dissalida_det.id_dsno_enc', 'dissalida_det.id_dissalida_det', 'dissalida_det.rptcion',
                            'dissalida_det.entrda', 'dissalida_det.lcldad', 'dissalida_det.block',
                            'maestro_V_VIC_BG.nm_vrdad as trtmnto', 'dissalida_det.prcla', 'dissalida_det.tstgo', DB::raw('0 as nmro_clnes')
                        )
                        ->join('maestro_V_VIC_BG', function($join) {
                            $join->on('dissalida_det.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
                        })
                        ->where('id_dsno_enc', $idDsnoEncF)
                        ->union(
                            DB::connection('sivar')->table('dissalida_det')
                            ->select('dissalida_det.id_dsno_enc', 'dissalida_det.id_dissalida_det', 'dissalida_det.rptcion',
                                'dissalida_det.entrda', 'dissalida_det.lcldad', 'dissalida_det.block',
                                'cruzamientos.nm_fmlias as trtmnto', 'dissalida_det.prcla', 'dissalida_det.tstgo', DB::raw('0 as nmro_clnes')
                            )
                            ->join('cruzamientos', function($join) {
                                $join->on('dissalida_det.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                            })
                            ->where('id_dsno_enc', $idDsnoEncF)
                        )
                        ->get();

*/


/*



                        $libroCampo = (DB::connection('sivar')->table('dissalida_det')
                        ->select(DB::raw('CONCAT(a.id_dissalida_det, \'-\', a.nmro_cmpo) as clave'), 'dissalida_det.id_dsno_enc', 'dissalida_det.id_dissalida_det', 'dissalida_det.rptcion',
                            'dissalida_det.entrda', 'dissalida_det.lcldad', 'dissalida_det.block',
                            'maestro_V_VIC_BG.nm_vrdad as trtmnto', 'dissalida_det.prcla', 'dissalida_det.tstgo', DB::raw('0 as nmro_clnes'),
                            'conf_campos.nmro_cmpo', 'conf_campos.nmbre_cmpo', 'a.vlor'
                        )
                        ->join('maestro_V_VIC_BG', function($join) {
                            $join->on('dissalida_det.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
                        })
                        ->join('datos_campo as a', function($join) {
                            $join->on('dissalida_det.id_dissalida_det', '=', 'a.id_dissalida_det');
                        })
                        ->join('conf_campos', function($join) {
                            $join->on('a.nmro_cmpo', '=', 'conf_campos.nmro_cmpo');
                        })
                        ->join(DB::raw('(SELECT max(COALESCE(c.id_fcha_evlcion, 0)) as id_fcha_evlcion, c.id_dissalida_det, c.nmro_cmpo, c.id_dsno_enc FROM datos_campo c GROUP BY (c.id_dissalida_det, c.nmro_cmpo, c.id_dsno_enc)) as c'), function($join) {
                            $join->on(DB::raw('COALESCE(a.id_fcha_evlcion, 0)'), '=', 'c.id_fcha_evlcion')
                                ->on('a.id_dsno_enc', '=', 'c.id_dsno_enc')
                                ->on('a.id_dissalida_det', '=', 'c.id_dissalida_det')
                                ->on('a.nmro_cmpo', '=', 'c.nmro_cmpo');
                        })
                        ->where('dissalida_det.id_dsno_enc', $idDsnoEncF)
                        //->whereRaw('datos_campo.id_dto_cmpo in (select max(id_fcha_evlcion) from datos_campo d group by (claim_company_id))')
                        ->union(
                            DB::connection('sivar')->table('dissalida_det')
                            ->select(DB::raw('CONCAT(e.id_dissalida_det, \'-\', e.nmro_cmpo) as clave'), 'dissalida_det.id_dsno_enc', 'dissalida_det.id_dissalida_det', 'dissalida_det.rptcion',
                                'dissalida_det.entrda', 'dissalida_det.lcldad', 'dissalida_det.block',
                                'cruzamientos.nm_fmlias as trtmnto', 'dissalida_det.prcla', 'dissalida_det.tstgo', DB::raw('0 as nmro_clnes'),
                                'conf_campos.nmro_cmpo', 'conf_campos.nmbre_cmpo', 'e.vlor'
                            )
                            ->join('cruzamientos', function($join) {
                                $join->on('dissalida_det.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                            })
                            ->join('datos_campo as e', function($join) {
                                $join->on('dissalida_det.id_dissalida_det', '=', 'e.id_dissalida_det');
                            })
                            ->join('conf_campos', function($join) {
                                $join->on('e.nmro_cmpo', '=', 'conf_campos.nmro_cmpo');
                            })
                            ->join(DB::raw('(SELECT max(COALESCE(f.id_fcha_evlcion, 0)) as id_fcha_evlcion, f.id_dissalida_det, f.nmro_cmpo, f.id_dsno_enc FROM datos_campo f GROUP BY (f.id_dissalida_det, f.nmro_cmpo, f.id_dsno_enc)) as f'), function($join) {
                                $join->on(DB::raw('COALESCE(e.id_fcha_evlcion, 0)'), '=', 'f.id_fcha_evlcion')
                                    ->on('e.id_dsno_enc', '=', 'f.id_dsno_enc')
                                    ->on('e.id_dissalida_det', '=', 'f.id_dissalida_det')
                                    ->on('e.nmro_cmpo', '=', 'f.nmro_cmpo');
                            })
                            ->where('dissalida_det.id_dsno_enc', $idDsnoEncF)
                        ))
                        ->orderBy('id_dissalida_det', 'asc')
                        ->orderBy('nmro_cmpo', 'asc')
                        ->get();


*/




/*



                        $libroCampo = (DB::connection('sivar')->table('dissalida_det')
                        ->select(DB::raw('CONCAT(a.id_dissalida_det, \'-\', a.nmro_cmpo) as clave'), 'dissalida_det.id_dsno_enc', 'dissalida_det.id_dissalida_det', 'dissalida_det.rptcion',
                            'dissalida_det.entrda', 'dissalida_det.lcldad', 'dissalida_det.block',
                            'maestro_V_VIC_BG.nm_vrdad as trtmnto', 'dissalida_det.prcla', 'dissalida_det.tstgo', DB::raw('0 as nmro_clnes'),
                            'conf_campos.nmro_cmpo', 'conf_campos.nmbre_cmpo', 'a.vlor'
                        )
                        ->join('maestro_V_VIC_BG', function($join) {
                            $join->on('dissalida_det.trtmnto', '=', 'maestro_V_VIC_BG.nm_vrdad');
                        })
                        ->join('datos_campo as a', function($join) {
                            $join->on('dissalida_det.id_dissalida_det', '=', 'a.id_dissalida_det');
                        })
                        ->join('conf_campos', function($join) {
                            $join->on('a.nmro_cmpo', '=', 'conf_campos.nmro_cmpo');
                        })
                        ->join(DB::raw('(SELECT max(COALESCE(c.id_fcha_evlcion, 0)) as id_fcha_evlcion, c.id_dissalida_det, c.nmro_cmpo, c.id_dsno_enc FROM datos_campo c GROUP BY (c.id_dissalida_det, c.nmro_cmpo, c.id_dsno_enc)) as c'), function($join) {
                            $join->on(DB::raw('COALESCE(a.id_fcha_evlcion, 0)'), '=', 'c.id_fcha_evlcion')
                                ->on('a.id_dsno_enc', '=', 'c.id_dsno_enc')
                                ->on('a.id_dissalida_det', '=', 'c.id_dissalida_det')
                                ->on('a.nmro_cmpo', '=', 'c.nmro_cmpo');
                        })
                        ->where('dissalida_det.id_dsno_enc', $idDsnoEncF)
                        //->whereRaw('datos_campo.id_dto_cmpo in (select max(id_fcha_evlcion) from datos_campo d group by (claim_company_id))')
                        ->union(
                            DB::connection('sivar')->table('dissalida_det')
                            ->select(DB::raw('CONCAT(e.id_dissalida_det, \'-\', e.nmro_cmpo) as clave'), 'dissalida_det.id_dsno_enc', 'dissalida_det.id_dissalida_det', 'dissalida_det.rptcion',
                                'dissalida_det.entrda', 'dissalida_det.lcldad', 'dissalida_det.block',
                                'cruzamientos.nm_fmlias as trtmnto', 'dissalida_det.prcla', 'dissalida_det.tstgo', DB::raw('0 as nmro_clnes'),
                                'conf_campos.nmro_cmpo', 'conf_campos.nmbre_cmpo', 'e.vlor'
                            )
                            ->join('cruzamientos', function($join) {
                                $join->on('dissalida_det.trtmnto', '=', DB::raw('CAST(cruzamientos.id_crzmnto AS varchar)'));
                            })
                            ->join('datos_campo as e', function($join) {
                                $join->on('dissalida_det.id_dissalida_det', '=', 'e.id_dissalida_det');
                            })
                            ->join('conf_campos', function($join) {
                                $join->on('e.nmro_cmpo', '=', 'conf_campos.nmro_cmpo');
                            })
                            ->join(DB::raw('(SELECT max(COALESCE(f.id_fcha_evlcion, 0)) as id_fcha_evlcion, f.id_dissalida_det, f.nmro_cmpo, f.id_dsno_enc FROM datos_campo f GROUP BY (f.id_dissalida_det, f.nmro_cmpo, f.id_dsno_enc)) as f'), function($join) {
                                $join->on(DB::raw('COALESCE(e.id_fcha_evlcion, 0)'), '=', 'f.id_fcha_evlcion')
                                    ->on('e.id_dsno_enc', '=', 'f.id_dsno_enc')
                                    ->on('e.id_dissalida_det', '=', 'f.id_dissalida_det')
                                    ->on('e.nmro_cmpo', '=', 'f.nmro_cmpo');
                            })
                            ->where('dissalida_det.id_dsno_enc', $idDsnoEncF)
                        ))
                        ->orderBy('id_dissalida_det', 'asc')
                        ->orderBy('nmro_cmpo', 'asc')
                        ->get();




*/
