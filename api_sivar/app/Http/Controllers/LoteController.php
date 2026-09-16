<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Vivero;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year');

        $query = Lote::query()
            ->when($year, function ($q) use ($year) {
                $q->whereHas('viveros', function ($q) use ($year) {
                    $q->whereYear('fecha_siembra', $year);
                })
                ->with(['viveros' => function ($q) use ($year) {
                    $q->whereYear('fecha_siembra', $year);
                }])
                ->withCount([
                    'viveros as viveros_activos_count' => function ($q) use ($year) {
                        $q->whereNotNull('proyecto_id')
                          ->whereYear('fecha_siembra', $year);
                    }
                ]);
            }, function ($q) {
                $q->with('viveros')
                  ->withCount([
                      'viveros as viveros_activos_count' => function ($q) {
                          $q->whereNotNull('proyecto_id');
                      }
                  ]);
            })
            ->orderBy('nombre_lote', 'asc');

        if ($request->has('ingenio_codigo') && $request->ingenio_codigo) {
            $query->where('ingenio_codigo', $request->ingenio_codigo);
        }
        if ($request->has('hacienda_codigo') && $request->hacienda_codigo) {
            $query->where('hacienda_codigo', $request->hacienda_codigo);
        }

        $lotes = $query->get();

        return response()->json($lotes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingenio_codigo' => 'required|string',
            'hacienda_codigo' => 'nullable|string',
            'nombre_lote' => [
                'required',
                'string',
                Rule::unique('lotes')->where(function ($query) use ($request) {
                    return $query->where('ingenio_codigo', $request->ingenio_codigo)
                        ->where('hacienda_codigo', $request->hacienda_codigo);
                }),
            ],
            'capacidad_maxima' => 'required|integer|min:0',
            'total_parcelas_vivero' => 'nullable|integer|min:0'
        ], [
            'nombre_lote.unique' => 'Ya existe un lote con este nombre en la hacienda seleccionada.'
        ]);

        $lote = DB::transaction(function () use ($validated) {
            $lote = Lote::create($validated);
            $this->syncViverosAndParcelas($lote);
            return $lote;
        });

        return response()->json($lote, 201);
    }

    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);

        $ingenio = $lote->ingenio_codigo;
        $hacienda = $request->input('hacienda_codigo', $lote->hacienda_codigo);

        $validated = $request->validate([
            'hacienda_codigo' => 'sometimes|nullable|string',
            'nombre_lote' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('lotes')->where(function ($query) use ($ingenio, $hacienda) {
                    return $query->where('ingenio_codigo', $ingenio)
                        ->where('hacienda_codigo', $hacienda);
                })->ignore($id),
            ],
            'capacidad_maxima' => 'sometimes|required|integer|min:0',
            'total_parcelas_vivero' => 'sometimes|nullable|integer|min:0'
        ], [
            'nombre_lote.unique' => 'Ya existe un lote con este nombre en la hacienda seleccionada.'
        ]);

        $updatedLote = DB::transaction(function () use ($lote, $validated) {
            $lote->lockForUpdate();
            $lote->update($validated);
            $this->syncViverosAndParcelas($lote);
            return $lote;
        });

        return response()->json($updatedLote);
    }

    public function destroy($id)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $lote = Lote::where('id', $id)->lockForUpdate()->findOrFail($id);

            // Find if any vivero in this lote has actual data
            $viveros = Vivero::where('lote_id', $lote->id)->get();

            $hasRealData = false;
            foreach ($viveros as $vivero) {
                if ($vivero->proyecto_id || $vivero->responsable_id || $vivero->caracter_id || $vivero->ambiente) {
                    $hasRealData = true;
                    break;
                }
                // Check if any parcela has a variety
                $hasVarieties = \Illuminate\Support\Facades\DB::connection('sivar')
                    ->table('vivero_parcelas')
                    ->where('vivero_id', $vivero->id)
                    ->whereNotNull('variedad_id')
                    ->exists();
                if ($hasVarieties) {
                    $hasRealData = true;
                    break;
                }
                // Check if any harvest exists
                $hasCosechas = \Illuminate\Support\Facades\DB::connection('sivar')
                    ->table('vivero_cosechas')
                    ->where('vivero_id', $vivero->id)
                    ->exists();
                if ($hasCosechas) {
                    $hasRealData = true;
                    break;
                }
            }

            if ($hasRealData) {
                return response()->json([
                    'message' => 'No se puede eliminar este lote porque tiene viveros con datos registrados.'
                ], 400);
            }

            // Delete all empty pre-created viveros (and cascade delete parcelas)
            foreach ($viveros as $vivero) {
                $vivero->forceDelete();
            }

            $lote->delete();
            return response()->json(null, 204);
        });
    }

    private function syncViverosAndParcelas($lote)
    {
        $parcelasPorViveroRequest = request('parcelas_por_vivero', []); // Array of [consecutivo => total_parcelas]
        $nombresPorViveroRequest = request('nombres_por_vivero', []);  // Array of [consecutivo => nombre]
        $inicioPorViveroRequest = request('inicio_por_vivero', []);  // Array of [consecutivo => inicio]
        
        $consecutivos = array_keys($parcelasPorViveroRequest);
        if (empty($consecutivos)) {
            // Fallback just in case, though the frontend should always send it
            $capacidad = $lote->capacidad_maxima;
            for ($c = 1; $c <= $capacidad; $c++) {
                $consecutivos[] = $c;
            }
        }

        // Validate duplicates in request
        if (count($consecutivos) !== count(array_unique($consecutivos))) {
            throw new \Exception("Hay identificadores (IDs) de vivero duplicados en la solicitud.");
        }

        // Validate global availability
        $existingOtherLotes = Vivero::withTrashed()
            ->whereIn('consecutivo_vivero_ingenio', $consecutivos)
            ->where('lote_id', '!=', $lote->id)
            ->get();

        if ($existingOtherLotes->isNotEmpty()) {
            $conflict = $existingOtherLotes->first();
            throw new \Exception("El ID {$conflict->consecutivo_vivero_ingenio} no está disponible. Ya está asignado en el Ingenio {$conflict->ingenio}, Hacienda {$conflict->hacienda}, Lote {$conflict->suerte}.");
        }

        // Update suerte field for all existing viveros of this lote
        Vivero::withTrashed()->where('lote_id', $lote->id)->update([
            'suerte' => $lote->nombre_lote
        ]);

        $fechaSiembraRequest = request('fecha_siembra', now()->format('Y-m-d'));
        $year = date('Y', strtotime($fechaSiembraRequest));

        $existingViveros = Vivero::withTrashed()
            ->where('lote_id', $lote->id)
            ->whereYear('fecha_siembra', $year)
            ->get();
        $existingNumbers = $existingViveros->pluck('consecutivo_vivero_ingenio')->toArray();

        // Delete viveros that were removed in the UI
        $toDelete = array_diff($existingNumbers, $consecutivos);
        foreach ($toDelete as $delId) {
            $vDel = $existingViveros->where('consecutivo_vivero_ingenio', $delId)->first();
            if ($vDel) {
                // Check if it has real data
                if ($vDel->proyecto_id || $vDel->responsable_id || $vDel->caracter_id || $vDel->ambiente) {
                    throw new \Exception("No se puede eliminar o cambiar el ID del Vivero {$delId} porque ya tiene datos registrados.");
                }
                $hasVarieties = \Illuminate\Support\Facades\DB::connection('sivar')
                    ->table('vivero_parcelas')->where('vivero_id', $vDel->id)->whereNotNull('variedad_id')->exists();
                if ($hasVarieties) {
                    throw new \Exception("No se puede eliminar o cambiar el ID del Vivero {$delId} porque tiene variedades registradas en sus parcelas.");
                }
                $vDel->forceDelete();
            }
        }

        foreach ($consecutivos as $i) {
            // Determine how many parcelas this specific Vivero should have
            $totalParcelas = 10;
            if (isset($parcelasPorViveroRequest[$i])) {
                $totalParcelas = intval($parcelasPorViveroRequest[$i]);
            } else {
                $existingVivero = $existingViveros->where('consecutivo_vivero_ingenio', $i)->first();
                if ($existingVivero && $existingVivero->total_parcelas) {
                    $totalParcelas = $existingVivero->total_parcelas;
                } else {
                    $totalParcelas = $lote->total_parcelas_vivero ?? 0;
                }
            }

            $inicioParcela = 1;
            if (isset($inicioPorViveroRequest[$i])) {
                $inicioParcela = intval($inicioPorViveroRequest[$i]);
            } else {
                $existingVivero = $existingViveros->where('consecutivo_vivero_ingenio', $i)->first();
                if ($existingVivero && $existingVivero->parcela_inicio) {
                    $inicioParcela = $existingVivero->parcela_inicio;
                }
            }

            $customNombre = null;
            if (isset($nombresPorViveroRequest[$i])) {
                $trimmed = trim($nombresPorViveroRequest[$i]);
                if ($trimmed !== '') {
                    $customNombre = $trimmed;
                }
            }
            $defaultNombre = "Vivero {$i}";

            if (!in_array($i, $existingNumbers)) {
                // Generate unique identifier
                $ingenio = $lote->ingenio_codigo ?: '00';
                $hacienda = $lote->hacienda_codigo ?: '00';
                $haciendaCleaned = ltrim($hacienda, '0');
                $suerte = $lote->nombre_lote ?: '00';
                $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
                $anio = $year;
                $identificador = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $haciendaCleaned, $suerteCleaned, $i);

                $vivero = Vivero::create([
                    'identificador_unico' => $identificador,
                    'nombre' => $customNombre ?? $defaultNombre,
                    'ingenio' => $lote->ingenio_codigo,
                    'hacienda' => $lote->hacienda_codigo,
                    'suerte' => $lote->nombre_lote,
                    'lote_id' => $lote->id,
                    'fecha_siembra' => request('fecha_siembra', now()->format('Y-m-d')),
                    'consecutivo_vivero_ingenio' => $i,
                    'total_parcelas' => $totalParcelas,
                    'parcela_inicio' => $inicioParcela
                ]);

                // Create default parcelas
                for ($p = 0; $p < $totalParcelas; $p++) {
                    \Illuminate\Support\Facades\DB::connection('sivar')
                        ->table('vivero_parcelas')
                        ->insert([
                            'vivero_id' => $vivero->id,
                            'numero_parcela' => $inicioParcela + $p,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            } else {
                // Vivero already exists, check if we need to adjust its parcelas
                $vivero = $existingViveros->where('consecutivo_vivero_ingenio', $i)->first();

                if ($vivero) {
                    if ($vivero->trashed()) {
                        $vivero->restore();
                    }

                    $updates = [
                        'total_parcelas' => $totalParcelas,
                        'parcela_inicio' => $inicioParcela
                    ];

                    if ($customNombre !== null && !$vivero->proyecto_id) {
                        $updates['nombre'] = $customNombre;
                    }

                    // Handle renaming parcel IDs if `inicio` changed, but only if they don't have varieties assigned!
                    if ($vivero->parcela_inicio != $inicioParcela) {
                        $hasVarieties = \Illuminate\Support\Facades\DB::connection('sivar')
                            ->table('vivero_parcelas')
                            ->where('vivero_id', $vivero->id)
                            ->whereNotNull('variedad_id')
                            ->exists();
                        if ($hasVarieties) {
                            throw new \Exception("No se puede cambiar el ID Inicial del Vivero {$vivero->consecutivo_vivero_ingenio} porque ya tiene variedades registradas en sus parcelas. Hazlo manualmente desde la administración del vivero.");
                        } else {
                            // If empty, it's safe to just recreate the parcels from scratch
                            \Illuminate\Support\Facades\DB::connection('sivar')
                                ->table('vivero_parcelas')
                                ->where('vivero_id', $vivero->id)
                                ->delete();
                        }
                    }

                    $existingParcelCount = \Illuminate\Support\Facades\DB::connection('sivar')
                        ->table('vivero_parcelas')
                        ->where('vivero_id', $vivero->id)
                        ->count();

                    if ($existingParcelCount < $totalParcelas) {
                        // Find the max parcel to continue sequence
                        $maxParcela = \Illuminate\Support\Facades\DB::connection('sivar')
                            ->table('vivero_parcelas')
                            ->where('vivero_id', $vivero->id)
                            ->max('numero_parcela');
                            
                        if ($maxParcela === null) {
                            $maxParcela = $inicioParcela - 1;
                        }

                        $toAdd = $totalParcelas - $existingParcelCount;
                        for ($p = 1; $p <= $toAdd; $p++) {
                            \Illuminate\Support\Facades\DB::connection('sivar')
                                ->table('vivero_parcelas')
                                ->insert([
                                    'vivero_id' => $vivero->id,
                                    'numero_parcela' => $maxParcela + $p,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                        }
                    } elseif ($existingParcelCount > $totalParcelas) {
                        // Sort by numero_parcela descending and get the ones to delete
                        $toRemove = $existingParcelCount - $totalParcelas;
                        
                        $parcelasToDelete = \Illuminate\Support\Facades\DB::connection('sivar')
                            ->table('vivero_parcelas')
                            ->where('vivero_id', $vivero->id)
                            ->orderBy('numero_parcela', 'desc')
                            ->limit($toRemove)
                            ->get();
                            
                        $idsToDelete = [];
                        $parcelasConDatos = [];
                        
                        foreach ($parcelasToDelete as $par) {
                            if ($par->variedad_id !== null) {
                                $parcelasConDatos[] = $par->numero_parcela;
                            } else {
                                $idsToDelete[] = $par->id;
                            }
                        }

                        if (!empty($parcelasConDatos)) {
                            $nums = implode(', ', $parcelasConDatos);
                            throw new \Exception("No se puede reducir la capacidad de parcelas del Vivero {$vivero->consecutivo_vivero_ingenio} a {$totalParcelas} porque las últimas parcelas ({$nums}) contienen variedades registradas. Bórralas manualmente.");
                        }

                        if (!empty($idsToDelete)) {
                            \Illuminate\Support\Facades\DB::connection('sivar')
                                ->table('vivero_parcelas')
                                ->whereIn('id', $idsToDelete)
                                ->delete();
                        }
                    }

                    if (request()->has('fecha_siembra')) {
                        $updates['fecha_siembra'] = request('fecha_siembra');
                    }

                    $vivero->update($updates);
                }
            }
        }
    }
}
