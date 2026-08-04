<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Vivero;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Lote::query()->with('viveros')->orderBy('nombre_lote', 'asc');
        if ($request->has('ingenio_codigo') && $request->ingenio_codigo) {
            $query->where('ingenio_codigo', $request->ingenio_codigo);
        }
        if ($request->has('hacienda_codigo') && $request->hacienda_codigo) {
            $query->where('hacienda_codigo', $request->hacienda_codigo);
        }
        
        $lotes = $query->get()->map(function($lote) {
            $lote->viveros_activos_count = $lote->viveros()->whereNotNull('proyecto_id')->count();
            return $lote;
        });

        return response()->json($lotes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingenio_codigo' => 'required|string',
            'hacienda_codigo' => 'nullable|string',
            'nombre_lote' => 'required|string',
            'capacidad_maxima' => 'required|integer|min:1',
            'total_parcelas_vivero' => 'nullable|integer|min:1'
        ]);

        $lote = Lote::create($request->all());
        $this->syncViverosAndParcelas($lote);

        return response()->json($lote, 201);
    }

    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);
        
        $request->validate([
            'hacienda_codigo' => 'sometimes|nullable|string',
            'nombre_lote' => 'sometimes|required|string',
            'capacidad_maxima' => 'sometimes|required|integer|min:1',
            'total_parcelas_vivero' => 'sometimes|nullable|integer|min:1'
        ]);

        $lote->update($request->all());
        $this->syncViverosAndParcelas($lote);

        return response()->json($lote);
    }

    public function destroy($id)
    {
        $lote = Lote::findOrFail($id);
        
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
    }

    private function syncViverosAndParcelas($lote)
    {
        $capacidad = $lote->capacidad_maxima;
        $parcelasPorViveroRequest = request('parcelas_por_vivero'); // Array of [consecutivo => total_parcelas]

        // Update suerte field for all existing viveros of this lote
        Vivero::where('lote_id', $lote->id)->update([
            'suerte' => $lote->nombre_lote
        ]);

        // Get existing viveros in this lote (including soft-deleted ones to avoid unique constraint issues)
        $existingViveros = Vivero::withTrashed()->where('lote_id', $lote->id)->get();
        $existingNumbers = $existingViveros->pluck('consecutivo_vivero_ingenio')->toArray();

        for ($i = 1; $i <= $capacidad; $i++) {
            // Determine how many parcelas this specific Vivero should have
            $totalParcelas = 10;
            if ($parcelasPorViveroRequest && isset($parcelasPorViveroRequest[$i])) {
                $totalParcelas = intval($parcelasPorViveroRequest[$i]);
            } else {
                $existingVivero = $existingViveros->where('consecutivo_vivero_ingenio', $i)->first();
                if ($existingVivero && $existingVivero->total_parcelas) {
                    $totalParcelas = $existingVivero->total_parcelas;
                } else {
                    $totalParcelas = $lote->total_parcelas_vivero ?: 10;
                }
            }

            if (!in_array($i, $existingNumbers)) {
                // Generate unique identifier
                $ingenio = $lote->ingenio_codigo ?: '00';
                $hacienda = $lote->hacienda_codigo ?: '00';
                $suerte = $lote->nombre_lote ?: '00';
                $anio = date('Y');
                $identificador = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $hacienda, $suerte, $i);

                $vivero = Vivero::create([
                    'identificador_unico' => $identificador,
                    'nombre' => "Vivero {$i}",
                    'ingenio' => $lote->ingenio_codigo,
                    'hacienda' => $lote->hacienda_codigo,
                    'suerte' => $lote->nombre_lote,
                    'lote_id' => $lote->id,
                    'fecha_siembra' => now()->format('Y-m-d'),
                    'consecutivo_vivero_ingenio' => $i,
                    'total_parcelas' => $totalParcelas
                ]);

                // Create default parcelas
                for ($p = 1; $p <= $totalParcelas; $p++) {
                    \Illuminate\Support\Facades\DB::connection('sivar')
                        ->table('vivero_parcelas')
                        ->insert([
                            'vivero_id' => $vivero->id,
                            'numero_parcela' => $p,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            } else {
                // Vivero already exists, check if we need to adjust its parcelas
                $vivero = $existingViveros->where('consecutivo_vivero_ingenio', $i)->first();

                if ($vivero) {
                    // Restore if it was soft-deleted
                    if ($vivero->trashed()) {
                        $vivero->restore();
                    }

                    // Update total_parcelas attribute
                    $vivero->update(['total_parcelas' => $totalParcelas]);

                    $existingParcelCount = \Illuminate\Support\Facades\DB::connection('sivar')
                        ->table('vivero_parcelas')
                        ->where('vivero_id', $vivero->id)
                        ->count();

                    if ($existingParcelCount < $totalParcelas) {
                        for ($p = $existingParcelCount + 1; $p <= $totalParcelas; $p++) {
                            \Illuminate\Support\Facades\DB::connection('sivar')
                                ->table('vivero_parcelas')
                                ->insert([
                                    'vivero_id' => $vivero->id,
                                    'numero_parcela' => $p,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                        }
                    } elseif ($existingParcelCount > $totalParcelas) {
                        \Illuminate\Support\Facades\DB::connection('sivar')
                            ->table('vivero_parcelas')
                            ->where('vivero_id', $vivero->id)
                            ->where('numero_parcela', '>', $totalParcelas)
                            ->delete();
                    }
                }
            }
        }
    }
}
