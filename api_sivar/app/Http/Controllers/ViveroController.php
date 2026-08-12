<?php

namespace App\Http\Controllers;

use App\Models\Vivero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\ViveroService;
use App\Http\Requests\StoreViveroRequest;
use App\Http\Requests\UpdateViveroRequest;
use App\Http\Requests\RegistrarCosechaRequest;

class ViveroController extends Controller
{
    protected $viveroService;

    public function __construct(ViveroService $viveroService)
    {
        $this->viveroService = $viveroService;
    }
    public function index(Request $request)
    {
        if ($request->query('slim') === 'true') {
            $viveros = Vivero::with(['parcelas:id,vivero_id,numero_parcela,numero_parcela_origen,id_plot_origen'])
                ->whereNotNull('proyecto_id')
                ->orderBy('created_at', 'desc')
                ->get(['id', 'identificador_unico', 'nombre', 'lote_id', 'ingenio', 'hacienda', 'suerte', 'fecha_siembra', 'proyecto_id', 'numero_corte', 'consecutivo_vivero_ingenio']);

            $viveros->each(function($v) {
                $v->makeHidden(['nombre_proyecto', 'nombre_responsable', 'nombre_ambiente', 'consecutivo_corte']);
            });

            return response()->json($viveros);
        }

        $viveros = Vivero::with(['proyecto', 'responsable', 'caracter', 'parcelas.variedad', 'parcelas.caracter', 'lote', 'origenLote', 'origenVivero'])
            ->whereNotNull('proyecto_id')
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($viveros as $vivero) {
            $vivero->id_vivero_origen_formateado = $this->formatIdViveroOrigen($vivero);
        }
        return response()->json($viveros);
    }

    public function store(StoreViveroRequest $request)
    {

        // Validate lot capacity (counting only fully active nurseries other than the one being activated)
        if ($request->has('lote_id') && $request->lote_id) {
            $lote = \App\Models\Lote::findOrFail($request->lote_id);
            
            $preCreatedId = null;
            if ($request->consecutivo_vivero_ingenio) {
                $preCreated = Vivero::where('lote_id', $lote->id)
                    ->where('consecutivo_vivero_ingenio', $request->consecutivo_vivero_ingenio)
                    ->first();
                if ($preCreated) {
                    $preCreatedId = $preCreated->id;
                }
            }

            $activeCount = Vivero::where('lote_id', $lote->id)
                ->whereNotNull('proyecto_id')
                ->where('id', '!=', $preCreatedId)
                ->count();

            if ($activeCount >= $lote->capacidad_maxima) {
                return response()->json([
                    'message' => "El lote {$lote->nombre_lote} ha superado su capacidad máxima de {$lote->capacidad_maxima} viveros activos."
                ], 400);
            }
        }

        $consecutivoViveroIngenio = $request->consecutivo_vivero_ingenio;
        $esCorte = $request->es_corte || $request->query('es_corte') === 'true' || $request->query('es_corte') === 1;

        // Ya no se genera el identificador concatenando el número de corte, 
        // siempre se generará por el consecutivo normal del slot.

        if (!isset($identificador)) {
            $identificador = $this->viveroService->generarIdentificadorUnico(
                $request->ingenio,
                $request->hacienda,
                $request->suerte,
                $request->fecha_siembra,
                $consecutivoViveroIngenio
            );
        }

        $suerteVal = $request->suerte;
        if (!$suerteVal && $request->lote_id) {
            $lote = \App\Models\Lote::find($request->lote_id);
            if ($lote) {
                $suerteVal = $lote->nombre_lote;
            }
        }

        $vivero = Vivero::withTrashed()
            ->where('lote_id', $request->lote_id)
            ->where('consecutivo_vivero_ingenio', $consecutivoViveroIngenio)
            ->first();

        if ($vivero) {
            if ($vivero->trashed()) {
                $vivero->restore();
            }
            $vivero->update([
                'identificador_unico' => $identificador,
                'nombre' => $request->nombre ?: $identificador,
                'ingenio' => $request->ingenio,
                'hacienda' => $request->hacienda,
                'suerte' => $suerteVal,
                'proyecto_id' => $request->proyecto_id,
                'ambiente' => $request->ambiente,
                'responsable_id' => $request->responsable_id,
                'fecha_siembra' => $request->fecha_siembra,
                'numero_corte' => $request->numero_corte ?? 1,
                'temporada_floracion' => $request->temporada_floracion,
                'condicion' => $request->condicion,
                'caracter_id' => $request->caracter_id,
                'origen_ingenio' => $request->origen_ingenio,
                'origen_hacienda' => $request->origen_hacienda,
                'origen_suerte' => $request->origen_suerte,
                'origen_anio' => $request->origen_anio,
                'origen_parcela' => $request->origen_parcela,
                'origen_lote_id' => $request->origen_lote_id,
                'origen_vivero_id' => $request->origen_vivero_id,
            ]);
        } else {
            $vivero = Vivero::create([
                'identificador_unico' => $identificador,
                'nombre' => $request->nombre ?: $identificador,
                'ingenio' => $request->ingenio,
                'hacienda' => $request->hacienda,
                'suerte' => $suerteVal,
                'proyecto_id' => $request->proyecto_id,
                'ambiente' => $request->ambiente,
                'responsable_id' => $request->responsable_id,
                'fecha_siembra' => $request->fecha_siembra,
                'numero_corte' => $request->numero_corte ?? 1,
                'temporada_floracion' => $request->temporada_floracion,
                'condicion' => $request->condicion,
                'caracter_id' => $request->caracter_id,
                'origen_ingenio' => $request->origen_ingenio,
                'origen_hacienda' => $request->origen_hacienda,
                'origen_suerte' => $request->origen_suerte,
                'origen_anio' => $request->origen_anio,
                'origen_parcela' => $request->origen_parcela,
                'origen_lote_id' => $request->origen_lote_id,
                'origen_vivero_id' => $request->origen_vivero_id,
                'lote_id' => $request->lote_id,
                'consecutivo_vivero_ingenio' => $consecutivoViveroIngenio,
            ]);
        }

        $hasHistory = \App\Models\ViveroLoteHistorial::where('vivero_id', $vivero->id)
            ->where('lote_id', $vivero->lote_id)
            ->exists();

        if ($vivero->lote_id && !$hasHistory) {
            $lote = \App\Models\Lote::find($vivero->lote_id);
            $loteName = $lote ? $lote->nombre_lote : 'N/A';
            \App\Models\ViveroLoteHistorial::create([
                'vivero_id' => $vivero->id,
                'lote_id' => $vivero->lote_id,
                'fecha_inicio' => now(),
                'activo' => true,
                'accion' => "Registro Inicial en {$loteName} (Vivero {$vivero->consecutivo_vivero_ingenio})"
            ]);
        }

        if ($esCorte && $request->origen_vivero_id) {
            // Delete any existing parcelas for this nursery
            \DB::connection('sivar')
                ->table('vivero_parcelas')
                ->where('vivero_id', $vivero->id)
                ->delete();

            // Fetch parcelas from the parent nursery
            $parentParcelas = \DB::connection('sivar')
                ->table('vivero_parcelas')
                ->where('vivero_id', $request->origen_vivero_id)
                ->orderBy('numero_parcela')
                ->get();

            // Recalculate ID Plot for the new cut
            $parts = explode('-', $vivero->identificador_unico);
            $baseId = array_slice($parts, 0, 4);
            $baseIdStr = implode('-', $baseId);

            foreach ($parentParcelas as $p) {
                $newIdPlot = null;
                if ($p->numero_parcela) {
                    $newIdPlot = $baseIdStr . '-' . $p->numero_parcela;
                }

                \DB::connection('sivar')
                    ->table('vivero_parcelas')
                    ->insert([
                        'vivero_id' => $vivero->id,
                        'numero_parcela' => $p->numero_parcela,
                        'variedad_id' => $p->variedad_id,
                        'numero_parcela_origen' => $p->numero_parcela,
                        'id_plot_origen' => $newIdPlot,
                        'caracter_id' => $p->caracter_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
            
            // Sync count of total_parcelas
            $vivero->total_parcelas = count($parentParcelas);
            $vivero->save();
        }

        return response()->json($vivero, 201);
    }

    public function show($id)
    {
        $vivero = Vivero::with(['proyecto', 'responsable', 'caracter', 'lote', 'historialLotes.lote', 'origenLote', 'origenVivero'])->findOrFail($id);
        $vivero->id_vivero_origen_formateado = $this->formatIdViveroOrigen($vivero);
        return response()->json($vivero);
    }

    public function update(UpdateViveroRequest $request, $id)
    {
        $vivero = Vivero::findOrFail($id);

        if ($request->has('lote_id') && $request->lote_id !== $vivero->lote_id) {
            $oldLote = \App\Models\Lote::find($vivero->lote_id);
            $oldLoteName = $oldLote ? $oldLote->nombre_lote : 'N/A';

            if ($request->lote_id) {
                $lote = \App\Models\Lote::findOrFail($request->lote_id);
                $activeCount = Vivero::where('lote_id', $lote->id)->where('id', '!=', $vivero->id)->count();
                if ($activeCount >= $lote->capacidad_maxima) {
                    return response()->json([
                        'message' => "El lote {$lote->nombre_lote} ha superado su capacidad máxima de {$lote->capacidad_maxima} viveros."
                    ], 400);
                }
            }

            \App\Models\ViveroLoteHistorial::where('vivero_id', $vivero->id)
                ->where('activo', true)
                ->update([
                    'activo' => false,
                    'fecha_fin' => now()
                ]);

            if ($request->lote_id) {
                \App\Models\ViveroLoteHistorial::create([
                    'vivero_id' => $vivero->id,
                    'lote_id' => $request->lote_id,
                    'fecha_inicio' => now(),
                    'activo' => true,
                    'accion' => "Cambio de Lote del {$oldLoteName} al {$lote->nombre_lote}"
                ]);
            }
        }

        $vivero->fill($request->except('identificador_unico'));
        
        if (!$vivero->suerte && $vivero->lote_id) {
            $lote = \App\Models\Lote::find($vivero->lote_id);
            if ($lote) {
                $vivero->suerte = $lote->nombre_lote;
            }
        }
        
        // Siempre generar el identificador_unico usando el método helper en el update
        // (ya no se concatena el número de corte)
        $parts = explode('-', $vivero->identificador_unico);
        $consecutivo = end($parts);
        if (!is_numeric($consecutivo) || intval($consecutivo) <= 0) {
            $consecutivo = $vivero->id;
        }
        $vivero->identificador_unico = $this->generarIdentificadorUnico(
            $vivero->ingenio,
            $vivero->hacienda,
            $vivero->suerte,
            $vivero->fecha_siembra,
            $consecutivo
        );
        
        $vivero->save();

        if (!$vivero->nombre) {
            $vivero->nombre = $vivero->identificador_unico;
            $vivero->save();
        }

        return response()->json($vivero);
    }    public function trasladarLote(Request $request, $id)
    {
        $viveroA = Vivero::findOrFail($id);
        
        $request->validate([
            'lote_id' => 'required|integer|exists:lotes,id',
            'consecutivo' => 'required|integer'
        ]);

        $newLoteId = $request->lote_id;
        $newConsecutivo = $request->consecutivo;

        // Find the destination Vivero B (the slot placeholder)
        $viveroB = Vivero::where('lote_id', $newLoteId)
            ->where('consecutivo_vivero_ingenio', $newConsecutivo)
            ->first();

        if (!$viveroB) {
            // If it doesn't exist, create it as an empty placeholder first
            $lote = \App\Models\Lote::findOrFail($newLoteId);
            $ingenio = $lote->ingenio_codigo ?: '00';
            $hacienda = $lote->hacienda_codigo ?: '00';
            $haciendaCleaned = ltrim($hacienda, '0');
            $suerte = $lote->nombre_lote ?: '00';
            $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
            $anio = date('Y', strtotime($viveroA->fecha_siembra));
            $identificadorB = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $haciendaCleaned, $suerteCleaned, $newConsecutivo);

            $viveroB = Vivero::create([
                'identificador_unico' => $identificadorB,
                'nombre' => $identificadorB,
                'ingenio' => $lote->ingenio_codigo,
                'hacienda' => $lote->hacienda_codigo,
                'suerte' => $lote->nombre_lote,
                'lote_id' => $lote->id,
                'fecha_siembra' => $viveroA->fecha_siembra,
                'consecutivo_vivero_ingenio' => $newConsecutivo,
                'total_parcelas' => 10
            ]);
        }

        // If Vivero B is not empty and it's NOT the same nursery
        if ($viveroB->id !== $viveroA->id && $viveroB->proyecto_id !== null) {
            return response()->json([
                'message' => 'El puesto de vivero seleccionado ya está sembrado/ocupado.'
            ], 400);
        }

        // If they chose the exact same lot and same slot, it's a no-op
        if ($viveroA->lote_id == $newLoteId && $viveroA->consecutivo_vivero_ingenio == $newConsecutivo) {
            return response()->json([
                'message' => 'El vivero ya se encuentra en este lote y puesto.'
            ], 400);
        }
        
        // Save old Vivero A location properties
        $oldLoteId = $viveroA->lote_id;
        $oldConsecutivo = $viveroA->consecutivo_vivero_ingenio;
        $oldIdentificador = $viveroA->identificador_unico;
        $oldNombre = $viveroA->nombre;
        $oldIngenio = $viveroA->ingenio;
        $oldHacienda = $viveroA->hacienda;
        $oldSuerte = $viveroA->suerte;

        // Save target Vivero B location properties
        $targetLoteId = $viveroB->lote_id;
        $targetConsecutivo = $viveroB->consecutivo_vivero_ingenio;
        $targetIdentificador = $viveroB->identificador_unico;
        $targetNombre = $viveroB->nombre;
        $targetIngenio = $viveroB->ingenio;
        $targetHacienda = $viveroB->hacienda;
        $targetSuerte = $viveroB->suerte;

        if ($viveroB->id !== $viveroA->id) {
            // Temporarily rename B to avoid unique constraint violation during swap
            $viveroB->identificador_unico = 'temp-swap-' . $viveroB->id . '-' . uniqid();
            $viveroB->save();
        }

        // Perform the swap of location fields on A
        $viveroA->lote_id = $targetLoteId;
        $viveroA->consecutivo_vivero_ingenio = $targetConsecutivo;
        $viveroA->identificador_unico = $targetIdentificador;
        $viveroA->nombre = $targetNombre;
        $viveroA->ingenio = $targetIngenio;
        $viveroA->hacienda = $targetHacienda;
        $viveroA->suerte = $targetSuerte;
        $viveroA->save();

        if ($viveroB->id !== $viveroA->id) {
            $viveroB->lote_id = $oldLoteId;
            $viveroB->consecutivo_vivero_ingenio = $oldConsecutivo;
            $viveroB->identificador_unico = $oldIdentificador;
            $viveroB->nombre = $oldNombre;
            $viveroB->ingenio = $oldIngenio;
            $viveroB->hacienda = $oldHacienda;
            $viveroB->suerte = $oldSuerte;
            $viveroB->save();
        }

        // Recalculate ID Plot for all parcelas of Vivero A
        $parts = explode('-', $viveroA->identificador_unico);
        $baseId = array_slice($parts, 0, 4);
        $baseIdStr = implode('-', $baseId);
        
        $parcelas = \DB::connection('sivar')
            ->table('vivero_parcelas')
            ->where('vivero_id', $viveroA->id)
            ->get();

        foreach ($parcelas as $p) {
            if ($p->numero_parcela_origen) {
                $newIdPlot = $baseIdStr . '-' . $p->numero_parcela_origen;
                \DB::connection('sivar')
                    ->table('vivero_parcelas')
                    ->where('id', $p->id)
                    ->update(['id_plot_origen' => $newIdPlot]);
            }
        }

        // Manage Lote History (either Lote or slot changed!)
        if ($oldLoteId != $newLoteId || $oldConsecutivo != $newConsecutivo) {
            $oldLote = \App\Models\Lote::find($oldLoteId);
            $oldLoteName = $oldLote ? $oldLote->nombre_lote : 'N/A';
            $newLote = \App\Models\Lote::find($newLoteId);
            $newLoteName = $newLote ? $newLote->nombre_lote : 'N/A';

            // Determine action type and text
            if ($oldLoteId != $newLoteId && $oldConsecutivo != $newConsecutivo) {
                $accionText = "Traslado del {$oldLoteName} (Vivero {$oldConsecutivo}) al {$newLoteName} (Vivero {$newConsecutivo})";
            } else if ($oldLoteId != $newLoteId) {
                $accionText = "Traslado del {$oldLoteName} al {$newLoteName}";
            } else {
                $accionText = "Cambio de Puesto del Vivero {$oldConsecutivo} al Vivero {$newConsecutivo}";
            }

            \App\Models\ViveroLoteHistorial::where('vivero_id', $viveroA->id)
                ->where('activo', true)
                ->update([
                    'activo' => false,
                    'fecha_fin' => now()
                ]);

            \App\Models\ViveroLoteHistorial::create([
                'vivero_id' => $viveroA->id,
                'lote_id' => $newLoteId,
                'fecha_inicio' => now(),
                'activo' => true,
                'accion' => $accionText
            ]);
        }

        $viveroA->load(['lote', 'historialLotes.lote']);

    }

    public function destroy($id)
    {
        $vivero = Vivero::findOrFail($id);
        
        // Check if there are active nurseries/cuts that depend on this nursery's plots/cuts
        $hasChildren = Vivero::where('origen_parcela', 'like', $vivero->identificador_unico . '%')->exists();
        if ($hasChildren) {
            return response()->json([
                'error' => 'dependency_exists',
                'message' => 'No se puede eliminar este vivero porque existen otros viveros/cortes que dependen de su semilla.'
            ], 400);
        }
        
        if ($vivero->lote_id) {
            // It is a slot within a lote, so we just clear its sowing fields to reset it to an empty slot!
            $lote = $vivero->lote;
            $ingenio = $lote->ingenio_codigo ?: '00';
            $hacienda = $lote->hacienda_codigo ?: '00';
            $haciendaCleaned = ltrim($hacienda, '0');
            $suerte = $lote->nombre_lote ?: '00';
            $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
            $anio = date('Y');
            $identificadorDefault = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $haciendaCleaned, $suerteCleaned, $vivero->consecutivo_vivero_ingenio);

            $vivero->update([
                'identificador_unico' => $identificadorDefault,
                'nombre' => "Vivero {$vivero->consecutivo_vivero_ingenio}",
                'proyecto_id' => null,
                'ambiente' => null,
                'responsable_id' => null,
                'fecha_siembra' => now()->format('Y-m-d'),
                'numero_corte' => 1,
                'temporada_floracion' => null,
                'condicion' => null,
                'caracter_id' => null,
                'origen_ingenio' => null,
                'origen_hacienda' => null,
                'origen_suerte' => null,
                'origen_anio' => null,
                'origen_parcela' => null,
                'origen_lote_id' => null,
                'origen_vivero_id' => null,
            ]);

            // Also clear the varieties in all plots of this nursery!
            \Illuminate\Support\Facades\DB::connection('sivar')
                ->table('vivero_parcelas')
                ->where('vivero_id', $vivero->id)
                ->update([
                    'variedad_id' => null,
                    'updated_at' => now()
                ]);

            return response()->json([
                'message' => 'El vivero ha sido vaciado/desactivado y el slot físico queda disponible.',
                'vivero' => $vivero
            ], 200);
        } else {
            // If it is a nursery that does NOT belong to any lote, delete it
            $vivero->delete();
            return response()->json(null, 204);
        }
    }

    public function registrarCosecha(RegistrarCosechaRequest $request, $id)
    {
        $vivero = Vivero::findOrFail($id);

        $fechaCorte = $request->fecha_cosecha;
        $ambiente = $request->ambiente;

        // Guardar el registro histórico de la cosecha
        $vivero->cosechas()->create([
            'fecha_cosecha' => $fechaCorte,
            'nueva_fecha_siembra' => $fechaCorte,
            'numero_corte_anterior' => $vivero->numero_corte,
            'ambiente' => $ambiente,
        ]);

        // Actualizar el vivero para el nuevo ciclo
        $vivero->numero_corte += 1;
        $vivero->fecha_siembra = $fechaCorte;
        if ($ambiente) {
            $vivero->ambiente = $ambiente;
        }

        // Ya no se actualiza el identificador_unico con el sufijo de corte según solicitud del usuario

        $vivero->save();

        return response()->json([
            'message' => 'Corte registrado y vivero actualizado correctamente',
            'vivero' => $vivero
        ]);
    }

    public function getHistorialCosechas($id)
    {
        $vivero = Vivero::findOrFail($id);
        $historial = $vivero->cosechas()->orderBy('created_at', 'desc')->get();
        return response()->json($historial);
    }

    public function getIngenios()
    {
        $ingenios = DB::connection('sivar')->table('remote_pg_ingenios')->get();
        return response()->json($ingenios);
    }

    public function getHaciendas($ingenio)
    {
        $haciendas = DB::connection('sivar')->table('remote_pg_hacienda')
            ->where('cd_ingnio', $ingenio)
            ->get();

        $haciendas->each(function($hda) {
            $hda->cd_hcnda = ltrim($hda->cd_hcnda, '0');
            $hda->nm_hcnda = str_replace('_00', '_', $hda->nm_hcnda);
        });

        return response()->json($haciendas);
    }

    public function getSuertes($hacienda)
    {
        $haciendaPadded = str_pad($hacienda, 6, '0', STR_PAD_LEFT);

        $suertes = DB::connection('sivar')->table('remote_pg_suerte')
            ->where('cd_hcnda', $haciendaPadded)
            ->get();
        return response()->json($suertes);
    }

    public function getProyectos()
    {
        $proyectos = cache()->remember('vivero_proyectos_array', 3600, function() {
            return \App\Models\Proyecto::with('area')->where('id_area_trbjo', 5)->get()->toArray();
        });
        return response()->json($proyectos);
    }

    public function getResponsables()
    {
        // 1 = VARIEDADES, 5 = Mejoramiento Genético
        $usuarios = cache()->remember('vivero_responsables_array', 3600, function() {
            $users = \App\Models\User::where('id_area', 1)
                                      ->where('id_area_trbjo', 5)
                                      ->where('estdo', '0') // Asumiendo que 0 es activo, u omitir si todos valen
                                      ->get();
            if ($users->isEmpty()) {
                 $users = \App\Models\User::whereIn('id_area', [1, 4])->get();
            }
            return $users->toArray();
        });

        return response()->json($usuarios);
    }

    public function getAmbientes()
    {
        $ambientes = cache()->remember('vivero_ambientes_array', 3600, function() {
            return DB::connection('sivar')->table('mega_ambiente')->get()->toArray();
        });
        return response()->json($ambientes);
    }

    public function getCaracteresPorProyecto($id)
    {
        $caracteres = DB::table('proyecto_caracteres')->where('proyecto_id', $id)->get();
        return response()->json($caracteres);
    }

    public function storeCaracter(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $exists = DB::table('proyecto_caracteres')
            ->where('proyecto_id', $id)
            ->whereRaw('LOWER(nombre) = ?', [strtolower($request->nombre)])
            ->first();

        if ($exists) {
            return response()->json($exists, 200);
        }

        $newId = DB::table('proyecto_caracteres')->insertGetId([
            'proyecto_id' => $id,
            'nombre' => $request->nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $caracter = DB::table('proyecto_caracteres')->where('id', $newId)->first();
        return response()->json($caracter, 201);
    }

    public function getNextCorteConsecutivo(Request $request)
    {
        $origenViveroId = $request->query('origen_vivero_id');
        $origenParcela = $request->query('origen_parcela');
        
        if ($origenViveroId) {
            $count = Vivero::where('origen_vivero_id', $origenViveroId)->count();
            return response()->json(['consecutivo' => $count + 1]);
        }
        
        if ($origenParcela) {
            $count = Vivero::where('origen_parcela', $origenParcela)->count();
            return response()->json(['consecutivo' => $count + 1]);
        }

        return response()->json(['consecutivo' => 1]);
    }

    public function getEstructura($id)
    {
        // Tracing upwards to find the absolute root parent nursery of this lineage
        $vivero = Vivero::findOrFail($id);
        $rootVivero = $vivero;
        $visited = [$rootVivero->id];
        
        while (true) {
            $parent = null;
            if ($rootVivero->origen_vivero_id) {
                $parent = Vivero::find($rootVivero->origen_vivero_id);
            }
            
            if (!$parent && $rootVivero->origen_parcela) {
                $parts = explode('-', $rootVivero->origen_parcela);
                if (count($parts) >= 4) {
                    $baseId = implode('-', array_slice($parts, 0, 4));
                    $parent = Vivero::where('identificador_unico', $baseId)->first();
                }
            }
            
            if ($parent && !in_array($parent->id, $visited)) {
                $rootVivero = $parent;
                $visited[] = $parent->id;
            } else {
                break;
            }
        }

        $estructura = $this->viveroService->getEstructura($rootVivero->id);
        return response()->json($estructura);
    }
}
