<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Ensayo;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCatalogoRequest;
use App\Http\Requests\UpdateCatalogoRequest;
use App\Http\Requests\MergeCatalogoRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Guard gate: only ADMIN and JEFE are allowed to manage master catalogs.
     */
    private function authorizeAdmin()
    {
        $user = auth('api')->user();
        if (!$user || !in_array($user->role, ['ADMIN', 'JEFE'])) {
            abort(403, 'No tienes permisos para gestionar los Catálogos Maestros.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Catalogo::query();

        // Filtering
        if ($request->has('search') && !empty($request->search)) {
            $query->where('valor', 'ilike', '%' . $request->search . '%');
        }

        if ($request->has('categoria') && !empty($request->categoria)) {
            $query->where('categoria', $request->categoria);
        }

        $catalogos = $query->orderBy('categoria')
            ->orderBy('valor')
            ->get();

        // Fetch usage statistics for each catalog value to show warning counts before delete/merge
        $stats = [];
        
        // We calculate them dynamically for current values
        $uniqueProyectos = Ensayo::select('proyecto', DB::raw('count(*) as total'))->groupBy('proyecto')->pluck('total', 'proyecto')->toArray();
        $uniqueIngenios = Ensayo::select('ingenio', DB::raw('count(*) as total'))->groupBy('ingenio')->pluck('total', 'ingenio')->toArray();
        
        // For Ambiente, check both selection and evaluation
        $selAmbs = Ensayo::select('amb_seleccion', DB::raw('count(*) as total'))->groupBy('amb_seleccion')->pluck('total', 'amb_seleccion')->toArray();
        $evalAmbs = Ensayo::select('amb_evaluacion', DB::raw('count(*) as total'))->groupBy('amb_evaluacion')->pluck('total', 'amb_evaluacion')->toArray();

        $stats = [
            'PROYECTO' => $uniqueProyectos,
            'INGENIO' => $uniqueIngenios,
            'AMBIENTE_SEL' => $selAmbs,
            'AMBIENTE_EVAL' => $evalAmbs
        ];

        return response()->json([
            'catalogos' => $catalogos,
            'stats' => $stats,
            'filters' => $request->only(['search', 'categoria'])
        ], 200);
    }

    public function store(StoreCatalogoRequest $request)
    {
        $trimmed = trim($request->valor);

        // Avoid duplicates
        $exists = Catalogo::where('categoria', $request->categoria)
            ->whereRaw('LOWER(valor) = ?', [strtolower($trimmed)])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'errors' => ['valor' => ['Este elemento ya se encuentra registrado en el catálogo.']]
            ], 422);
        }

        $catalogo = Catalogo::create([
            'categoria' => $request->categoria,
            'valor' => $trimmed
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Elemento agregado exitosamente al catálogo.',
            'catalogo' => $catalogo
        ], 201);
    }

    public function update(UpdateCatalogoRequest $request, Catalogo $catalogo)
    {
        $oldValue = $catalogo->valor;
        $newValue = trim($request->valor);
        $category = $catalogo->categoria;

        if ($oldValue === $newValue) {
            return response()->json([
                'success' => true,
                'no_change' => true,
                'catalogo' => $catalogo
            ], 200);
        }

        DB::beginTransaction();
        try {
            // 1. Update the catalog item
            $catalogo->update(['valor' => $newValue]);

            // 2. CASCADE UPDATE all historical rows in Ensayos table matching this value
            if ($category === 'PROYECTO') {
                Ensayo::where('proyecto', $oldValue)->update(['proyecto' => $newValue]);
            } elseif ($category === 'INGENIO') {
                Ensayo::where('ingenio', $oldValue)->update(['ingenio' => $newValue]);
            } elseif ($category === 'AMBIENTE') {
                Ensayo::where('amb_seleccion', $oldValue)->update(['amb_seleccion' => $newValue]);
                Ensayo::where('amb_evaluacion', $oldValue)->update(['amb_evaluacion' => $newValue]);
            }

            // Log catalog change in audit trail
            \App\Models\Actividad::registrar(
                'EDICION_CATALOGO',
                "Renombró el catálogo '{$oldValue}' a '{$newValue}' (Categoría: {$category}) propagando cambios en cascada.",
                ['categoria' => $category, 'antes' => $oldValue, 'ahora' => $newValue]
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Catálogo modificado. Se actualizaron los históricos en cascada.',
                'catalogo' => $catalogo
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falla al propagar cambios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Catalogo $catalogo)
    {
        $this->authorizeAdmin();

        $catalogo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Elemento eliminado del catálogo.'
        ], 200);
    }

    /**
     * Highly advanced tool to MERGE duplicate catalog entries into one master.
     * E.g. Merge "Humedo" (source) into "Húmedo" (target).
     */
    public function merge(MergeCatalogoRequest $request)
    {
        $source = Catalogo::findOrFail($request->source_id);
        $target = Catalogo::findOrFail($request->target_id);

        if ($source->categoria !== $target->categoria) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes fusionar catálogos de diferentes categorías.'
            ], 422);
        }

        $oldVal = $source->valor;
        $newVal = $target->valor;
        $cat = $source->categoria;

        DB::beginTransaction();
        try {
            // 1. Re-map historical records in Ensayos
            if ($cat === 'PROYECTO') {
                Ensayo::where('proyecto', $oldVal)->update(['proyecto' => $newVal]);
            } elseif ($cat === 'INGENIO') {
                Ensayo::where('ingenio', $oldVal)->update(['ingenio' => $newVal]);
            } elseif ($cat === 'AMBIENTE') {
                Ensayo::where('amb_seleccion', $oldVal)->update(['amb_seleccion' => $newVal]);
                Ensayo::where('amb_evaluacion', $oldVal)->update(['amb_evaluacion' => $newVal]);
            }

            // 2. Delete the redundant Source catalog entry
            $source->delete();

            // Log the massive catalog merge event
            \App\Models\Actividad::registrar(
                'FUSION_CATALOGO',
                "Fusionó el catálogo redundante '{$oldVal}' dentro de '{$newVal}' (Categoría: {$cat}) unificando históricos.",
                ['categoria' => $cat, 'eliminado' => $oldVal, 'conservado' => $newVal]
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Fusión exitosa: '{$oldVal}' fue combinado dentro de '{$newVal}' correctamente."
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error durante la fusión de catálogos: ' . $e->getMessage()
            ], 500);
        }
    }
}
