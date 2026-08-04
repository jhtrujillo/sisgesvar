<?php

namespace App\Http\Controllers;

use App\Models\Vivero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ViveroController extends Controller
{
    public function index()
    {
        $viveros = Vivero::with(['proyecto', 'responsable', 'caracter', 'parcelas.variedad', 'parcelas.caracter', 'lote', 'origenLote', 'origenVivero'])
            ->whereNotNull('proyecto_id')
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($viveros as $vivero) {
            $vivero->id_vivero_origen_formateado = $this->formatIdViveroOrigen($vivero);
        }
        return response()->json($viveros);
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'proyecto_id' => 'required|integer|exists:sivar.remote_pg_sipro,id_prycto',
            'ingenio' => 'required|string',
            'hacienda' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'fecha_siembra' => 'required|date',
            'origen_ingenio' => 'nullable|string',
            'origen_hacienda' => 'nullable|string',
            'origen_suerte' => 'nullable|string',
            'origen_anio' => 'nullable|integer',
            'origen_parcela' => 'nullable|string',
            'origen_lote_id' => 'nullable|integer|exists:lotes,id',
            'origen_vivero_id' => 'nullable|integer|exists:viveros,id',
            'lote_id' => 'nullable|integer|exists:lotes,id',
            'consecutivo_vivero_ingenio' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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

        if ($request->origen_parcela && $esCorte) {
            $cutNumber = Vivero::where('origen_parcela', $request->origen_parcela)->count() + 1;
            $identificador = $request->origen_parcela . '-' . $cutNumber;
        } else {
            $identificador = $this->generarIdentificadorUnico(
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
            \App\Models\ViveroLoteHistorial::create([
                'vivero_id' => $vivero->id,
                'lote_id' => $vivero->lote_id,
                'fecha_inicio' => now(),
                'activo' => true
            ]);
        }

        return response()->json($vivero, 201);
    }

    public function show($id)
    {
        $vivero = Vivero::with(['proyecto', 'responsable', 'caracter', 'lote', 'historialLotes.lote', 'origenLote', 'origenVivero'])->findOrFail($id);
        $vivero->id_vivero_origen_formateado = $this->formatIdViveroOrigen($vivero);
        return response()->json($vivero);
    }

    public function update(Request $request, $id)
    {
        $vivero = Vivero::findOrFail($id);

        if ($request->has('lote_id') && $request->lote_id !== $vivero->lote_id) {
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
                    'activo' => true
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
        
        $esCorte = $request->es_corte 
            || ($vivero->origen_vivero_id && $vivero->origen_vivero_id != $vivero->id)
            || (count(explode('-', $vivero->identificador_unico)) >= 5);
        
        if ($vivero->origen_parcela && $esCorte) {
            $cutNumber = Vivero::where('origen_parcela', $vivero->origen_parcela)
                ->where('id', '<=', $vivero->id)
                ->count();
            if ($cutNumber === 0) {
                $cutNumber = 1;
            }
            $vivero->identificador_unico = $vivero->origen_parcela . '-' . $cutNumber;
        } else {
            // Always regenerate the identificador_unico using the helper method on update
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
        }
        
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
            $suerte = $lote->nombre_lote ?: '00';
            $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
            $anio = date('Y', strtotime($viveroA->fecha_siembra));
            $identificadorB = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $hacienda, $suerteCleaned, $newConsecutivo);

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

        // Perform the swap of location fields
        $viveroA->lote_id = $viveroB->lote_id;
        $viveroA->consecutivo_vivero_ingenio = $viveroB->consecutivo_vivero_ingenio;
        $viveroA->identificador_unico = $viveroB->identificador_unico;
        $viveroA->nombre = $viveroB->nombre;
        $viveroA->ingenio = $viveroB->ingenio;
        $viveroA->hacienda = $viveroB->hacienda;
        $viveroA->suerte = $viveroB->suerte;
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

        // Manage Lote History if Lote changed
        if ($oldLoteId != $newLoteId) {
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
                'activo' => true
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
            $suerte = $lote->nombre_lote ?: '00';
            $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
            $anio = date('Y');
            $identificadorDefault = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $hacienda, $suerteCleaned, $vivero->consecutivo_vivero_ingenio);

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

    public function registrarCosecha(Request $request, $id)
    {
        $request->validate([
            'fecha_cosecha' => 'required|date',
            'nueva_fecha_siembra' => 'required|date',
        ]);

        $vivero = Vivero::findOrFail($id);

        // Guardar el registro histórico de la cosecha
        $vivero->cosechas()->create([
            'fecha_cosecha' => $request->fecha_cosecha,
            'nueva_fecha_siembra' => $request->nueva_fecha_siembra,
            'numero_corte_anterior' => $vivero->numero_corte,
        ]);

        // Actualizar el vivero para el nuevo ciclo
        $vivero->numero_corte += 1;
        $vivero->fecha_siembra = $request->nueva_fecha_siembra;

        // HU-003: Actualizar identificador_unico con el sufijo de corte
        $identificadorBase = preg_replace('/-C?\d+$/', '', $vivero->identificador_unico);
        $vivero->identificador_unico = $identificadorBase . '-' . $vivero->numero_corte;

        $vivero->save();

        return response()->json([
            'message' => 'Cosecha registrada y vivero actualizado correctamente',
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
        return response()->json($haciendas);
    }

    public function getSuertes($hacienda)
    {
        $suertes = DB::connection('sivar')->table('remote_pg_suerte')
            ->where('cd_hcnda', $hacienda)
            ->get();
        return response()->json($suertes);
    }

    public function getProyectos()
    {
        $proyectos = \App\Models\Proyecto::where('id_area_trbjo', 5)->get();
        return response()->json($proyectos);
    }

    public function getResponsables()
    {
        // 1 = VARIEDADES, 5 = Mejoramiento Genético
        $usuarios = \App\Models\User::where('id_area', 1)
                                      ->where('id_area_trbjo', 5)
                                      ->where('estdo', '0') // Asumiendo que 0 es activo, u omitir si todos valen
                                      ->get();

        // Si la tabla usuario no tiene id_area_trbjo poblado correctamente en todos, 
        // y quieres asegurar traerlos:
        if ($usuarios->isEmpty()) {
             $usuarios = \App\Models\User::where('id_area', 1)->get();
        }

        return response()->json($usuarios);
    }

    public function getAmbientes()
    {
        $ambientes = DB::connection('sivar')->table('mega_ambiente')->get();
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
        $origen = $request->query('origen_parcela');
        if (!$origen) {
            return response()->json(['consecutivo' => 1]);
        }
        $count = Vivero::where('origen_parcela', $origen)->count();
        return response()->json(['consecutivo' => $count + 1]);
    }

    public function getEstructura($id)
    {
        $vivero = Vivero::with(['proyecto', 'responsable', 'caracter', 'parcelas.variedad', 'parcelas.caracter'])->findOrFail($id);
        $this->loadEstructuraRecursiva($vivero);
        return response()->json($vivero);
    }

    private function loadEstructuraRecursiva($vivero)
    {
        // 1. Load cuts for each real parcel (exact match on plot ID)
        foreach ($vivero->parcelas as $parcela) {
            $parcelLabel = $parcela->numero_parcela_origen ?: $parcela->numero_parcela;
            $plotId = $vivero->identificador_unico . '-' . $parcelLabel;
            $cortes = Vivero::with(['proyecto', 'responsable', 'caracter', 'parcelas.variedad', 'parcelas.caracter'])
                ->where('origen_parcela', $plotId)
                ->get();
            foreach ($cortes as $corte) {
                $this->loadEstructuraRecursiva($corte);
            }
            $parcela->cortes_recursivos = $cortes;
        }

        // 2. Load cuts that directly reference this nursery (e.g. legacy/direct cuts without parcel segment)
        $directCortes = Vivero::with(['proyecto', 'responsable', 'caracter', 'parcelas.variedad', 'parcelas.caracter'])
            ->where('origen_parcela', $vivero->identificador_unico)
            ->get();
        foreach ($directCortes as $corte) {
            $this->loadEstructuraRecursiva($corte);
        }

        if ($directCortes->isNotEmpty()) {
            $virtualParcela = new \stdClass();
            $virtualParcela->id = 'virtual_' . $vivero->id;
            $virtualParcela->numero_parcela = 'General';
            $virtualParcela->numero_parcela_origen = 'General';
            $virtualParcela->id_plot_origen = $vivero->identificador_unico;
            $virtualParcela->variedad = null;
            $virtualParcela->caracter = null;
            $virtualParcela->cortes_recursivos = $directCortes;

            if (is_array($vivero->parcelas)) {
                $parcelas = $vivero->parcelas;
                $parcelas[] = $virtualParcela;
                $vivero->parcelas = $parcelas;
            } else {
                $vivero->parcelas->push($virtualParcela);
            }
        }
    }

    private function generarIdentificadorUnico($ingenioCd, $haciendaCd, $suerteCd, $fechaSiembra, $consecutivo)
    {
        $ingenio = $ingenioCd ?: '00';
        $hacienda = $haciendaCd ?: '00';
        $suerte = $suerteCd ?: '00';
        $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
        $anioSiembra = $fechaSiembra ? date('Y', strtotime($fechaSiembra)) : date('Y');

        return sprintf('%s%s-%s-%s-%d', $ingenio, $anioSiembra, $hacienda, $suerteCleaned, $consecutivo);
    }

    private function formatIdViveroOrigen($vivero)
    {
        if ($vivero->origenVivero) {
            return $vivero->origenVivero->identificador_unico;
        }

        // Si tiene origen_parcela con formato de ID de parcela de Vivero (ej: CN2026-00EESA-00023D-2-4)
        if ($vivero->origen_parcela && count(explode('-', $vivero->origen_parcela)) > 3) {
            $parts = explode('-', $vivero->origen_parcela);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                array_pop($parts); // Remover número de parcela
                $cleanedParts = array_map(function($p) {
                    return trim(preg_replace('/\b(lote|vivero)\b/i', '', $p));
                }, $parts);
                return implode('-', $cleanedParts); // Retorna el Vivero ID padre
            }
        }

        // Si no (origen manual/externo), construimos el ID usando los códigos de la base de datos
        $info = [];
        $ingenioAnio = '';
        if ($vivero->origen_ingenio) {
            $ingenioAnio .= $vivero->origen_ingenio;
        }
        if ($vivero->origen_anio) {
            $ingenioAnio .= $vivero->origen_anio;
        }
        if ($ingenioAnio !== '') {
            $info[] = $ingenioAnio;
        }
        if ($vivero->origen_hacienda) $info[] = $vivero->origen_hacienda;
        
        $loteNombre = '';
        if ($vivero->origenLote) {
            $loteNombre = $vivero->origenLote->nombre_lote;
        } else {
            $loteNombre = $vivero->origen_suerte;
        }
        if ($loteNombre) {
            $info[] = trim(preg_replace('/\b(lote|vivero)\b/i', '', $loteNombre));
        }
        
        if ($vivero->origen_parcela) {
            $info[] = trim(preg_replace('/\b(lote|vivero)\b/i', '', $vivero->origen_parcela));
        }

        // Clean up empty elements
        $info = array_filter(array_map('trim', $info));

        return count($info) > 0 ? implode('-', $info) : 'N/A';
    }
}
