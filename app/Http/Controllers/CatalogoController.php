<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Ensayo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Guard gate: only ADMIN and JEFE are allowed to manage master catalogs.
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
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

        return Inertia::render('Catalogos/Index', [
            'catalogos' => $catalogos,
            'stats' => $stats,
            'filters' => $request->only(['search', 'categoria'])
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'categoria' => 'required|string|in:PROYECTO,INGENIO,AMBIENTE',
            'valor' => 'required|string|max:255',
        ]);

        $trimmed = trim($request->valor);

        // Avoid duplicates
        $exists = Catalogo::where('categoria', $request->categoria)
            ->whereRaw('LOWER(valor) = ?', [strtolower($trimmed)])
            ->exists();

        if ($exists) {
            return back()->withErrors(['valor' => 'Este elemento ya se encuentra registrado en el catálogo.']);
        }

        Catalogo::create([
            'categoria' => $request->categoria,
            'valor' => $trimmed
        ]);

        return redirect()->route('catalogos.index')
            ->with('success', 'Elemento agregado exitosamente al catálogo.');
    }

    public function update(Request $request, Catalogo $catalogo)
    {
        $this->authorizeAdmin();

        $request->validate([
            'valor' => 'required|string|max:255',
        ]);

        $oldValue = $catalogo->valor;
        $newValue = trim($request->valor);
        $category = $catalogo->categoria;

        if ($oldValue === $newValue) {
            return back();
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
            return redirect()->route('catalogos.index')
                ->with('success', 'Catálogo modificado. Se actualizaron los históricos en cascada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['valor' => 'Falla al propagar cambios: ' . $e->getMessage()]);
        }
    }

    public function destroy(Catalogo $catalogo)
    {
        $this->authorizeAdmin();

        // Simply delete, will not delete trials but orphans them logically in the cat lookup. 
        // Safer to just delete.
        $catalogo->delete();

        return redirect()->route('catalogos.index')
            ->with('success', 'Elemento eliminado del catálogo.');
    }

    /**
     * Highly advanced tool to MERGE duplicate catalog entries into one master.
     * E.g. Merge "Humedo" (source) into "Húmedo" (target).
     */
    public function merge(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'source_id' => 'required|exists:catalogos,id',
            'target_id' => 'required|exists:catalogos,id|different:source_id',
        ]);

        $source = Catalogo::findOrFail($request->source_id);
        $target = Catalogo::findOrFail($request->target_id);

        if ($source->categoria !== $target->categoria) {
            return back()->withErrors(['merge' => 'No puedes fusionar catálogos de diferentes categorías.']);
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
            return redirect()->route('catalogos.index')
                ->with('success', "Fusión exitosa: '{$oldVal}' fue combinado dentro de '{$newVal}' correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['merge' => 'Error durante la fusión de catálogos: ' . $e->getMessage()]);
        }
    }
}
