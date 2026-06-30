<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use App\Models\Variety;
use App\Models\Crossing;
use Exception;

class VarietyController extends Controller
{
    public function varietysList(Request $request)
    {
        try {

            $model = Variety::all();
            if ($model) {
                return response()->json($model);
            }

            return response("No hay registros", 400);
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    public function getVariety(Request $request)
    {

        try {

            $variety = Variety::select('id_nm_vrdad','nm_vrdad')->get();
            if ($variety) {
                return response()->json($variety);
            }


            return response("No hay registros", 400);
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }
    public function getVarietyById(Request $request, $var)
    {

        try {
            if ($var != "") {
                $variety = Variety::where("nm_vrdad", $var)->first();
                if ($variety) {
                    return response()->json($variety);
                }
            }

            return response("No hay registros", 400);
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }



    /**
     * Retrieves the parents of a given variety recursively.
     *
     * @param Request $request The HTTP request object containing the input variables.
     *                          Required parameters: 'var', 'relationship', 'type'.
     * @throws None
     * @return Response The response object containing the JSON data of the parents.
     */
// public function getParentsRecursion($var, $relationship, $type)
// {
//     try {
//         if (empty($var) ) {
//             return response("Var, Relationship, and Type are mandatory variables", 400);
//         }

//         $parents = $this->getVarietyParents($var, $relationship, $type);
//         return response()->json($parents);
//     } catch (Exception $ex) {
//         return response($ex->getMessage(), 500);
//     }
// }

//     private function getVarietyParents($var, $relationship, $type)
//     {
//         try {if ($var === '') {
//             return [];
//         }

//         $variety = Variety::where('nm_vrdad', $var)->first();

//         if (empty($variety)) {
//             return [
//                 'name' => $relationship . ' ' . $var . $type
//             ];
//         }

//         $parents = [];

//         if ($variety->nm_vrdad == '?') {
//             $parents[] = [
//                 'name' => $relationship . ' ' . $var,
//                 'type' => 'padre'
//             ];
//         } else {
//             $mother = $this->getVarietyParents($variety->vrdad_madre, $relationship, 'madre');
//             $father = $this->getVarietyParents($variety->vrdad_pdre, $relationship, 'padre');

//             $parents = [
//                 'name' => $relationship . ' ' . $var . $type,
//                 'children' => array_merge($mother, $father)
//             ];
//         }

//         return $parents;} catch (Exception $ex) {
//             return response($ex->getMessage(), 500);
//         }
//     }

// public function getParentsRecursion($var,$parents,$relationship,$type){
//     $variety = Variety::where('nm_vrdad','=', $var)->get()->first();  
//     if($var==""){ 
//         return $parents;
//     }
//     if(empty($variety)){
//         $parents .= '{ "name": "'.$relationship.' '.$var.'"'.$type.'}';  
//         return  $this->getParentsRecursion("",$parents,"","");
//     }
//     else{
//         if($variety->nm_vrdad=='?'){
//             $parents .= '{ "name": "'.$relationship.' '.$var.'","type":"padre"}';  
//             return  $this->getParentsRecursion("",$parents,"","");
//         }
//         if($variety->vrdad_madre=='' || $variety->vrdad_madre=='null' ){
//             $parents .= '{ "name": "'.$relationship.' '.$var.'" '.$type.'}';
//             return  $this->getParentsRecursion("",$parents,'',' ,"type":"madre" ');
//         }
//         else{
//             $mother=$this->getParentsRecursion($variety->vrdad_madre,$parents,'',' ,"type":"madre" ');
//         }
        
//         if($variety->vrdad_pdre=='' || $variety->vrdad_pdre=='null' ){
//             $parents .= '{ "name": "'.$relationship.' '.$var.'" '.$type.'}';
//             return  $this->getParentsRecursion("",$parents,'',' ,"type":"padre" ');
//         }
//         else{
//             $father=$this->getParentsRecursion($variety->vrdad_pdre,$parents,'',' ,"type":"padre" ');
//         }                         
            
//         return $parents .= '{ "name": "'.$relationship.' '.$var.'" '.$type.',"children":[ '.  $mother.' , '.$father.']}';
//     }             
// }
public function getParentsRecursion($var, $relationship, $type)
{
    // Inicializamos la variable $parents como un array vacío
    $parents = [];

    // Llamamos a la función recursiva para obtener los padres recursivamente
    $result = $this->getParentsRecursionHelper($var, $parents, $relationship, $type);

    // Devolvemos el resultado como respuesta JSON
    return response()->json($result);
}

private function getParentsRecursionHelper($var, &$parents, $relationship, $type)
{
    $variety = Variety::where('nm_vrdad', '=', $var)->get()->first();  

    if ($var == "") {
        return $parents;
    }

    if (empty($variety)) {
        $parents[] = [
            "name" => "$relationship $var",
            "type" => $type
        ];
        return $this->getParentsRecursionHelper("", $parents, "", "");
    } else {
        if ($variety->nm_vrdad == '?') {
            $parents[] = [
                "name" => "$relationship $var",
                "type" => "padre"
            ];
            return $this->getParentsRecursionHelper("", $parents, "", "");
        }

        $mother = [];
        $father = [];

        if ($variety->vrdad_madre == '' || $variety->vrdad_madre == 'null') {
            $parents[] = [
                "name" => "$relationship $var",
                "type" => $type
            ];
            return $this->getParentsRecursionHelper("", $parents, '', "madre");
        } else {
            $mother = $this->getParentsRecursionHelper($variety->vrdad_madre, $parents, '', "madre");
        }

        if ($variety->vrdad_pdre == '' || $variety->vrdad_pdre == 'null') {
            $parents[] = [
                "name" => "$relationship $var",
                "type" => $type
            ];
            return $this->getParentsRecursionHelper("", $parents, '', "padre");
        } else {
            $father = $this->getParentsRecursionHelper($variety->vrdad_pdre, $parents, '',"padre");
        }                         

        $parents  = [
            "name" => "$relationship $var",
            "type" => $type,
            "children" => array_merge($mother, $father)
        ];

        return $parents;
    }             
}

    // public function getParentsNivelRecursion($var, $parents, $relationship, $type, $nivel)
    // {
    //     $variety = Variety::where('nm_vrdad','=', $var)->get()->first();  
    //     if($var==""){ 
    //         return $nivel;
    //     }         
    //     if(empty($variety)){
    //         $parents .= '{ "name": "'.$relationship.' '.$var.'"'.$type.',"'.$nivel.'"}';  
    //         return  $this->getParentsNivelRecursion("",$parents,"","",$nivel+1);
    //     }
    //     else{
    //         if($variety->nm_vrdad=='?'){
    //             $parents .= '{ "name": "'.$relationship.' '.$var.'","type":"padre","'.$nivel.'"}';  
    //             return  $this->getParentsNivelRecursion("",$parents,"","",$nivel+1);
    //         }
    //         if($variety->vrdad_madre=='' || $variety->vrdad_madre=='null' ){
    //             $parents .= '{ "name": "'.$relationship.' '.$var.'" '.$type.',"$nivel"}';
    //             return  $this->getParentsNivelRecursion("",$parents,'',' ,"type":"madre" ',$nivel+1);
    //         }
    //         else{
    //             $mother=$this->getParentsNivelRecursion($variety->vrdad_madre,$parents,'',' ,"type":"madre" ',$nivel+1);
    //         }
            
    //         if($variety->vrdad_pdre=='' || $variety->vrdad_pdre=='null' ){
    //             $parents .= '{ "name": "'.$relationship.' '.$var.'" '.$type.',"$nivel"}';
    //             return  $this->getParentsNivelRecursion("",$parents,'',' ,"type":"padre" ',$nivel+1);
    //         }
    //         else{
    //             $father=$this->getParentsNivelRecursion($variety->vrdad_pdre,$parents,'',' ,"type":"padre" ',$nivel+1);
    //         }                             
    //         return $father > $mother ? $father : $mother;
    //     }  
    // }
    public function getParentsNivelRecursion($var, $relationship, $type, $nivel)
    {
        $parents = [];
        $nivel = intval($nivel);  // Asegurar que $nivel sea un entero
        $this->getParentsNivelRecursionHelper($var, $parents, $relationship, $type, $nivel);
        return response()->json($parents);
    }
    
    private function getParentsNivelRecursionHelper($var, &$parents, $relationship, $type, $nivel)
    {
        $nivel = intval($nivel);  // Asegurar que $nivel sea un entero
        $variety = Variety::where('nm_vrdad', '=', $var)->get()->first();  
    
        if ($var == "") {
            return $nivel;
        }
    
        if (empty($variety)) {
            $parents[] = [
                "name" => "$relationship $var",
                "type" => $type,
                "nivel" => $nivel
            ];
            return $this->getParentsNivelRecursionHelper("", $parents, "", "", $nivel + 1);
        } else {
            if ($variety->nm_vrdad == '?') {
                $parents[] = [
                    "name" => "$relationship $var",
                    "type" => "(Padre)",
                    "nivel" => $nivel
                ];
                return $this->getParentsNivelRecursionHelper("", $parents, "", "", $nivel + 1);
            }
    
            $mother = [];
            $father = [];
    
            if ($variety->vrdad_madre == '' || $variety->vrdad_madre == 'null') {
                $parents[] = [
                    "name" => "$relationship $var",
                    "type" => $type,
                    "nivel" => $nivel
                ];
                return $this->getParentsNivelRecursionHelper("", $parents, '', '(Madre)', $nivel + 1);
            } else {
                $mother_nivel = $this->getParentsNivelRecursionHelper($variety->vrdad_madre, $mother, '', '(Madre)', $nivel + 1);
            }
    
            if ($variety->vrdad_pdre == '' || $variety->vrdad_pdre == 'null') {
                $parents[] = [
                    "name" => "$relationship $var",
                    "type" => $type,
                    "nivel" => $nivel
                ];
                return $this->getParentsNivelRecursionHelper("", $parents, '', '(Padre)', $nivel + 1);
            } else {
                $father_nivel = $this->getParentsNivelRecursionHelper($variety->vrdad_pdre, $father, '', '(Padre)', $nivel + 1);
            }
    
            $parents[] = [
                "name" => "$relationship $var",
                "type" => $type,
                "nivel" => $nivel,
                "children" => array_merge($mother, $father)
            ];
    
            return max($mother_nivel ?? $nivel, $father_nivel ?? $nivel);
        }
    }
    
    
    public function getParentsLevel(Request $request, $var)
    {
        $nivel = $this->getParentsNivelRecursion($var,'','(Raiz)',0,[]);    
        return response()->json([$nivel]);
    }
    public function getParents(Request $request, $var)
    {

        // Llama a getParentsRecursion con un objeto Request válido
        $parents = $this->getParentsRecursion($var,'','raiz'); 
        $nivel = $this->getParentsNivelRecursion($var,'','(Raiz)',0,[]); 
        try {
        $arr = [
            'parents' => $parents,
            'nivel' => $nivel
        ];

        return response()->json($arr);} catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    public function germoplasmBankList(Request $request)
    {
        try {
            // Retrieve all records from the 'caracterizacion_banco_germoplasma' table
            $model = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')->paginate(10);
    
            // Check if records are found
            if ($model->isNotEmpty()) {
                return response()->json($model);
            }
    
            // Return response if no records are found
            return response("No hay registros", 400);
        } catch (Exception $ex) {
            // Return response in case of an exception
            return response($ex->getMessage(), 500);
        }
    }
    // public function historyDatatable(Request $request)
    // {
    //     try {
    //         // Obtener los valores del request
    //         $tipo = $request->input('tipo');
    //         $var = $request->input('var');
    //         $estado = $request->input('estado');
    //         if($tipo==1){
    //             $model = DB::connection('aplicacion')->table('cruzamientos')
    //                     ->join('datos_campo_crudos', 'cruzamientos.nm_fmlias', '=', 'datos_campo_crudos.crzmnto')
    //                     -> select('cruzamientos.*')
    //                     ->where('cruzamientos.vrdad_mdre', $var)
    //                     ->where('datos_campo_crudos.estdo_slccion', $estado)
    //                     ->distinct();
    //             $dictionary = [];
    //             return $this->buildDatatable($model,$dictionary);
    //         }
    
    //         $model = Crossing::join('datos_campo_crudos', 'cruzamientos.nm_fmlias', '=', 'datos_campo_crudos.crzmnto')
    //             ->select('cruzamientos.*');
    
    //         // Aplicar filtro de estado si está presente
    //         if (!empty($estado)) {
    //             $model->where('datos_campo_crudos.estdo_slccion', $estado);
    //         }
    
    //         // Aplicar filtros según el tipo
    //         switch ($tipo) {
    //             case 1:
    //                 $model->where('cruzamientos.vrdad_mdre', $var);
    //                 break;
    //             case 2:
    //                 $model->where('cruzamientos.vrdad_pdre1', $var);
    //                 break;
    //             case 3:
    //                 $model->join('maestro_V_VIC_BG', function ($join) use ($var) {
    //                     $join->on('cruzamientos.pdgree', 'LIKE', DB::raw("CONCAT('%', \"maestro_V_VIC_BG\".\"pdgree\", '%')"))
    //                         ->where('maestro_V_VIC_BG.nm_vrdad', $var);
    //                 });
    //                 break;
    //             default:
    //                 // Enviar una respuesta de error si el tipo no es válido
    //                 // return response()->json(['error' => 'Tipo de solicitud no válido'], 400);
    //         }
    
    //         // Ejecutar la consulta y obtener los resultados
    //         $results = $model->distinct()->get();
    
    //         // Devolver los resultados como respuesta JSON
    //         return response()->json($results);
    //     } catch (Exception $ex) {
    //         return response($ex->getMessage(), 500);
    //     }
    // }
    public function historyDatatable(Request $request, $var, $estado, $tipo)
    {
        try {
            // Inicialización del modelo y la consulta base
            $model = Crossing::join('datos_campo_crudos', 'cruzamientos.nm_fmlias', '=', 'datos_campo_crudos.crzmnto')
                ->select('cruzamientos.*');
            
            // Condiciones de la consulta basadas en el tipo
            if ($tipo == 1) {
                // Consulta basada en la variedad madre
                $model->where('cruzamientos.vrdad_mdre', $var)
                    ->where('datos_campo_crudos.estdo_slccion', $estado);
            } elseif ($tipo == 2) {
                // Consulta basada en la variedad padre
                $model->where('cruzamientos.vrdad_pdre1', $var)
                    ->where('datos_campo_crudos.estdo_slccion', $estado);
            } elseif ($tipo == 3) {
                // Consulta basada en el pedigrí relacionado con la variedad
                $model->join("maestro_V_VIC_BG as mvvb", 'cruzamientos.pdgree', 'LIKE', DB::raw('CONCAT(\'%\', mvvb.pdgree, \'%\')'))
                    ->where('mvvb.nm_vrdad', '=', $var)
                    ->where('datos_campo_crudos.estdo_slccion', '=', $estado)
                    ->get();
            }
            
            // Ejecución de la consulta y obtención de los resultados
            $results = $model->distinct()->get();
    
            // Retorno de los datos en formato JSON
            return response()->json(['data' => $results]);
        } catch (\Exception $ex) {
            // Manejo de errores y retorno de mensaje de error en formato JSON
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }    

    public function getVarietyProfile(Request $request, $var)
    {
        try {
            $variety = Variety::where("nm_vrdad", $var)->first();
            
            // Construir la consulta de promedios para la variedad individual (para unir múltiples ensayos y ensayos incompletos)
            $columnsToAverage = [
                'mosaico_p' => 'mosaico_p',
                'roya_cafe_r' => 'roya_cafe_r',
                'roya_naranja_r' => 'roya_naranja_r',
                'carbon_p' => 'carbon_p',
                'sacarosa' => 'sacarosa',
                'brix' => 'brix',
                'fibra' => 'fibra',
                'pureza' => 'pureza',
                'tchm' => 'tchm',
                'altura_planta' => 'altura_planta',
                'diametro_tallo' => 'diametro_tallo',
                'aspecto_planta' => 'aspecto_planta',
                'spad' => 'spad'
            ];

            $selectParts = [];
            foreach ($columnsToAverage as $col => $alias) {
                $selectParts[] = "AVG(CASE WHEN TRIM(REPLACE($col::text, ',', '.')) ~ '^\d+(\.\d+)?$' THEN TRIM(REPLACE($col::text, ',', '.'))::double precision ELSE NULL END) as $alias";
            }
            $selectParts[] = "MAX(procedencia) as procedencia";
            $selectParts[] = "MAX(estacion) as estacion";
            
            $traitsSelectRaw = implode(",\n", $selectParts);

            // Intentar obtener la caracterización agregada por el nombre exacto de la variedad
            $traits = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
                ->where('variedad', $var)
                ->selectRaw($traitsSelectRaw)
                ->first();

            // Si es completamente nulo o no tiene datos de sacarosa/pureza/tchm, intentar buscar con LIKE
            $hasAnyData = false;
            if ($traits) {
                foreach (['sacarosa', 'pureza', 'fibra', 'tchm', 'mosaico_p', 'carbon_p'] as $key) {
                    if (isset($traits->$key) && !is_null($traits->$key)) {
                        $hasAnyData = true;
                        break;
                    }
                }
            }

            if (!$hasAnyData) {
                // Intentar búsqueda LIKE
                $traits = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
                    ->where('variedad', 'LIKE', '%' . $var . '%')
                    ->selectRaw($traitsSelectRaw)
                    ->first();
                
                // Volver a verificar si la búsqueda LIKE arrojó algún dato válido
                $hasAnyData = false;
                if ($traits) {
                    foreach (['sacarosa', 'pureza', 'fibra', 'tchm', 'mosaico_p', 'carbon_p'] as $key) {
                        if (isset($traits->$key) && !is_null($traits->$key)) {
                            $hasAnyData = true;
                            break;
                        }
                    }
                }
                if ($hasAnyData) {
                    $traits->origen_datos = 'BG';
                }
            } else {
                $traits->origen_datos = 'BG';
            }

            if (!$hasAnyData) {
                // FALLBACK 1: Pruebas Regionales (estdo_slccion = 5) en datos_campo_crudos
                $prSelectRaw = "
                    avg(CAST(REPLACE(CAST(mosaico AS TEXT), ',', '.') AS FLOAT)) as mosaico_p,
                    avg(CAST(REPLACE(CAST(roya AS TEXT), ',', '.') AS FLOAT)) as roya_cafe_r,
                    avg(CAST(REPLACE(CAST(\"179\" AS TEXT), ',', '.') AS FLOAT)) as roya_naranja_r,
                    avg(CAST(REPLACE(CAST(carbon AS TEXT), ',', '.') AS FLOAT)) as carbon_p,
                    avg(CAST(REPLACE(CAST(\"163\" AS TEXT), ',', '.') AS FLOAT)) as sacarosa,
                    avg(CAST(REPLACE(CAST(\"173\" AS TEXT), ',', '.') AS FLOAT)) as tchm,
                    avg(CAST(REPLACE(CAST(\"Tallo Altura (cm)\" AS TEXT), ',', '.') AS FLOAT)) as altura_planta,
                    avg(CAST(REPLACE(CAST(\"DiametroTallo\" AS TEXT), ',', '.') AS FLOAT)) as diametro_tallo,
                    MAX(CAST(estdo_slccion AS TEXT)) as procedencia
                ";
                
                $traitsPR = DB::connection('sivar')->table('datos_campo_crudos')
                    ->where('nm_vrdad', $var)
                    ->where('estdo_slccion', '=', 5)
                    ->selectRaw($prSelectRaw)
                    ->first();
                
                if ($traitsPR) {
                    foreach (['sacarosa', 'tchm', 'mosaico_p', 'carbon_p'] as $key) {
                        if (isset($traitsPR->$key) && !is_null($traitsPR->$key)) {
                            $hasAnyData = true;
                            break;
                        }
                    }
                }
                
                if ($hasAnyData) {
                    $traits = $traitsPR;
                    $traits->origen_datos = 'PR';
                }
            }

            if (!$hasAnyData) {
                // FALLBACK 2: Estado III (estdo_slccion = 3) en datos_campo_crudos
                $traitsEIII = DB::connection('sivar')->table('datos_campo_crudos')
                    ->where('nm_vrdad', $var)
                    ->where('estdo_slccion', '=', 3)
                    ->selectRaw($prSelectRaw)
                    ->first();
                
                if ($traitsEIII) {
                    foreach (['sacarosa', 'tchm', 'mosaico_p', 'carbon_p'] as $key) {
                        if (isset($traitsEIII->$key) && !is_null($traitsEIII->$key)) {
                            $hasAnyData = true;
                            break;
                        }
                    }
                }
                
                if ($hasAnyData) {
                    $traits = $traitsEIII;
                    $traits->origen_datos = 'ESTADO III';
                }
            }

            // Si al final no se obtuvieron datos válidos, se deja como null para que el frontend muestre la tarjeta vacía
            if (!$hasAnyData) {
                $traits = null;
            } else {
                // Convertir todos los floats a números PHP para evitar que vayan como strings en el JSON
                $traitsArray = (array)$traits;
                foreach ($traitsArray as $key => $value) {
                    if (is_numeric($value)) {
                        $traitsArray[$key] = (float)$value;
                    }
                }
                $traits = $traitsArray;
            }

            // También podemos obtener el promedio global de la población para comparar las características en gráficos de radar/barras!
            // Para evitar errores por representación no válida de texto (p. ej. "0,0") en PostgreSQL:
            $globalColumns = [
                'mosaico_p' => 'mosaico',
                'roya_cafe_r' => 'roya_cafe',
                'roya_naranja_r' => 'roya_naranja',
                'carbon_p' => 'carbon',
                'sacarosa' => 'sacarosa',
                'brix' => 'brix',
                'fibra' => 'fibra',
                'pureza' => 'pureza',
                'tchm' => 'tchm',
                'altura_planta' => 'altura_planta',
                'diametro_tallo' => 'diametro_tallo',
                'spad' => 'spad'
            ];

            $globalParts = [];
            foreach ($globalColumns as $col => $alias) {
                $globalParts[] = "AVG(CASE WHEN TRIM(REPLACE($col::text, ',', '.')) ~ '^\d+(\.\d+)?$' THEN TRIM(REPLACE($col::text, ',', '.'))::double precision ELSE NULL END) as $alias";
            }
            $globalRawStr = implode(",\n", $globalParts);

            $globalAverages = DB::connection('sivar')->table('caracterizacion_banco_germoplasma')
                ->selectRaw($globalRawStr)
                ->first();

            return response()->json([
                'success' => true,
                'variety' => $variety,
                'traits' => $traits,
                'globalAverages' => $globalAverages
            ]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'error' => $ex->getMessage()], 500);
        }
    }
}
