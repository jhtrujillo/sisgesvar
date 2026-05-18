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
}
