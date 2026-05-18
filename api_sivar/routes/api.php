<?php
// Rutas de todos los controladores para ser consumidos en la interfaz de usuario y esta cumpla con los procesos esperados

use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return response()->json(['result' => 'ok'], 200);
})->name('test');


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', [\App\Http\Controllers\JWTAuthController::class, 'login'])->name('login');
    Route::post('logout', [\App\Http\Controllers\JWTAuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [\App\Http\Controllers\JWTAuthController::class, 'refresh'])->name('refresh');
    Route::post('me', [\App\Http\Controllers\JWTAuthController::class, 'me'])->name('me');
});

Route::group([
    'middleware' => 'api',

], function ($router) {
    Route::get('links', [\App\Http\Controllers\LinksController::class, 'links']);
    Route::post('links', [\App\Http\Controllers\LinksController::class, 'links']);
    Route::get('processes', [\App\Http\Controllers\ProcessesController::class, 'processes']);
    Route::post('processes', [\App\Http\Controllers\ProcessesController::class, 'processes']);
    Route::get('processesList', [\App\Http\Controllers\ProcessesListController::class, 'processesList']);
    Route::post('processesList', [\App\Http\Controllers\ProcessesListController::class, 'processesList']);
    Route::get('floweringList', [\App\Http\Controllers\FloweringController::class, 'floweringList']);
    // Route::get('/flowering/list', [\App\Http\Controllers\FloweringController::class, 'floweringList']);
    Route::get('/variety/{var}', [\App\Http\Controllers\VarietyController::class, 'getVarietyById']);
    Route::get('/varietyProfile/{var}', [\App\Http\Controllers\VarietyController::class, 'getVarietyProfile'])->where('var', '.*');
    Route::get('/variety', [\App\Http\Controllers\VarietyController::class, 'getVariety']);
    Route::get('/varietysList', [\App\Http\Controllers\VarietyController::class, 'varietysList']);
    Route::get('/germoplasmBankList', [\App\Http\Controllers\VarietyController::class, 'germoplasmBankList']);
    Route::get('/historyDatatable', [\App\Http\Controllers\VarietyController::class, 'historyDatatable']);
    Route::get('historyDatatable/{var}/{estado}/{tipo}', [\App\Http\Controllers\VarietyController::class, 'historyDatatable']);
    // Route::get('getParentsRecursion/{var}', [\App\Http\Controllers\VarietyController::class, 'getParentsRecursion']);
    Route::get('getParentsRecursion/{var}/{parents}/{relationship}/{type}', [\App\Http\Controllers\VarietyController::class, 'getParentsRecursion']);
    Route::get('getParentsNivelRecursion/{var}/{parents}/{relationship}/{type}/{nivel}', [\App\Http\Controllers\VarietyController::class, 'getParentsNivelRecursion']);
    Route::get('getParentsLevel/{var}', [\App\Http\Controllers\VarietyController::class, 'getParentsLevel']);
    Route::get('getParents/{var}', [\App\Http\Controllers\VarietyController::class, 'getParents']);

    Route::get('crossingList', [\App\Http\Controllers\CrossingController::class, 'crossingList']);
    Route::get('/crossing/programming', [\App\Http\Controllers\CrossingController::class, 'list']);
    Route::get('crossingInitialData', [\App\Http\Controllers\CrossingController::class, 'crossingInitialData']);
    Route::get('listarFlores/{proyectos}/{fechai}/{fechaf}', [\App\Http\Controllers\CrossingController::class, 'listarFlores']);
    Route::get('parametizeWeightedCrossing/{proyecto}/{mega_ambiente}', [\App\Http\Controllers\CrossingController::class, 'parametizeWeightedCrossing']);
    Route::post('modifyFeatures/{caracteristica}/{proyecto}/{nivel}/{ponderado}/{nuevo}', [\App\Http\Controllers\CrossingController::class, 'modifyFeatures']);
    Route::get('calcularViabilidadCaracteristica/{caracteristica}/{florA}/{florB}/{ponderado}/{testigo}', [\App\Http\Controllers\CrossingController::class, 'calcularViabilidadCaracteristica']);
    Route::get('crearOrigenCruzamiento/{id_cruzamiento}', [\App\Http\Controllers\CrossingController::class, 'crearOrigenCruzamiento']);
    Route::get('generateMatrix/{proyectos}/{proyecto}/{testigo}', [\App\Http\Controllers\CrossingController::class, 'generateMatrix']);
    Route::get('suggestionCrossings/{proyectos}/{proyecto}/{testigo}/{ambiente}', [\App\Http\Controllers\CrossingController::class, 'suggestionCrossings']);
    Route::get('suggestionCrossingsPerProject/{proyectos}/{proyecto}/{testigo}/{ambiente}', [\App\Http\Controllers\CrossingController::class, 'suggestionCrossingsPerProject']);
    Route::get('/crossing/programming/change_proyect_flower/{variedad}/{proyecto}/{bolsa}', [\App\Http\Controllers\CrossingController::class, 'enviarFlorAProyecto']);
    Route::get('sugerenciasCruzamientosBolsaComun/{proyectos}/{proyecto}/{testigo}/{ambiente}', [\App\Http\Controllers\CrossingController::class, 'sugerenciasCruzamientosBolsaComun']);
    Route::get('/crossing/programming/send_common_bag/{variedad}', [\App\Http\Controllers\CrossingController::class, 'enviarABolsaComun']);
    Route::get('/crossing/programming/criteria/', [\App\Http\Controllers\CrossingController::class, 'criteriosBancoGermoplasma']);
    Route::get('criteriosBancoGermoplasmaPorVariedad/{variedad}', [\App\Http\Controllers\CrossingController::class, 'criteriosBancoGermoplasmaPorVariedad']);
    Route::get('proyectosConFlores', [\App\Http\Controllers\CrossingController::class, 'proyectosConFlores']);
    Route::post('/crossing/programming/save_crossing', [\App\Http\Controllers\CrossingController::class, 'guardarCruzamiento']);
    Route::get('consultarHistoricoCruzamiento/{madre}/{padres}', [\App\Http\Controllers\CrossingController::class, 'consultarHistoricoCruzamiento']);
    Route::get('/crossing/programming/save_weight/{proyecto}', [\App\Http\Controllers\CrossingController::class, 'guardarPonderados']);
    Route::get('/crossing/consolidated', [\App\Http\Controllers\CrossingController::class, 'consolidado']);
    Route::get('/crossing/programming/send_mail/{string}', [\App\Http\Controllers\CrossingController::class, 'enviarCorreoPracticos']);
    Route::get('/consolidadoDatatable/{tipo}', [\App\Http\Controllers\CrossingController::class, 'consolidadoDatatable']);
    Route::get('/crossing/upload/', [\App\Http\Controllers\CrossingController::class, 'cargarCruzamientos']);
    Route::get('/crossing/upload', [\App\Http\Controllers\CrossingController::class, 'cargarCruzamientosPost']);
    Route::get('/crossing/upload1/{proyecto}/{usuario}/{cruzamiento_id}/{madre}/{padre}/{porcentaje_germinacion}/{gramos}/{plantulas_estimadas}', [\App\Http\Controllers\CrossingController::class, 'cargarCruzamientoMexico']);
    Route::get('/crossing/modify/{id}', [\App\Http\Controllers\CrossingController::class, 'modificarCruzamiento']);
    Route::get('/crossing/modify', [\App\Http\Controllers\CrossingController::class, 'modificarCruzamientoPost']);
    Route::get('/obtenerIdFlorCruzamiento/{a}/{b}/{c}', [\App\Http\Controllers\CrossingController::class, 'obtenerIdFlorCruzamiento']);

    //Experimentos
    Route::get('getSearchParameters', [\App\Http\Controllers\ExperimentosController::class, 'getSearchParameters']);
    Route::get('getAreasProgram/{id_area}', [\App\Http\Controllers\ExperimentosController::class, 'getAreasProgram']);
    Route::get('getProjectsArea/{id_area_trbjo}', [\App\Http\Controllers\ExperimentosController::class, 'getProjectsArea']);
    Route::get('getExperiment/{id_pr}/{srie}/{estdo}', [\App\Http\Controllers\ExperimentosController::class, 'getExperiment']);  
    Route::get('getCriteriosSeleccion', [\App\Http\Controllers\ExperimentosController::class, 'getCriteriosSeleccion']);   
    Route::post('grabarEncabezado', [\App\Http\Controllers\ExperimentosController::class, 'grabarEncabezado']);  
    Route::get('getTreatmentsSeason/{ano}/{id_dsno_enc}/{min_plantulas}/{plantulas_ttles}', [\App\Http\Controllers\ExperimentosController::class, 'getTreatmentsSeason']);
    Route::get('getTreatmentsExperiments/{id_dsno_enc_f}/{id_dsno_enc_i}', [\App\Http\Controllers\ExperimentosController::class, 'getTreatmentsExperiments']);
    Route::post('addDisenoDetalle/{id_dsno_enc}/{cTestigo}/{nTipoParcela}/{nTotalPlantas}/{arrIds}', [\App\Http\Controllers\ExperimentosController::class, 'addDisenoDetalle']);  
    Route::post('addDisenoDetalles/{id_dsno_enc}/{nTipoParcela}/{cTestigo}/{nTotalPlantas}/{arrIds}', [\App\Http\Controllers\ExperimentosController::class, 'addDisenoDetalles']);  
    Route::post('removeDetalle/{id_dsno_enc}/{arrIds}', [\App\Http\Controllers\ExperimentosController::class, 'removeDetalle']);  
    Route::get('getRegistros/{tipo}/{tipo_registro}/{search}/{id_dsno_enc}', [\App\Http\Controllers\ExperimentosController::class, 'getRegistros']);
    Route::post('grabarDiseno/{id_dsno_enc}/{arrIds}', [\App\Http\Controllers\ExperimentosController::class, 'grabarDiseno']);  
    Route::post('saveGenericDesign/{id_dsno_enc}', [\App\Http\Controllers\ExperimentosController::class, 'saveGenericDesign']);
    Route::post('generateDesign/{id_dsno_enc}', [\App\Http\Controllers\ExperimentosController::class, 'generateDesign']);

    //Libro de Campo
    Route::get('getLibroCampo/{id_pr}/{srie}/{estdo}', [\App\Http\Controllers\LibroCampoController::class, 'getLibroCampo']);
    Route::post('crearLibroCampo', [\App\Http\Controllers\ExperimentosController::class, 'crearLibroCampo']);
});
