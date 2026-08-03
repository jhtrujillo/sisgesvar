<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Vivero;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Lote::query();
        if ($request->has('ingenio_codigo') && $request->ingenio_codigo) {
            $query->where('ingenio_codigo', $request->ingenio_codigo);
        }
        if ($request->has('hacienda_codigo') && $request->hacienda_codigo) {
            $query->where('hacienda_codigo', $request->hacienda_codigo);
        }
        
        $lotes = $query->get()->map(function($lote) {
            $lote->viveros_activos_count = Vivero::where('lote_id', $lote->id)->count();
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
            'capacidad_maxima' => 'required|integer|min:1'
        ]);

        $lote = Lote::create($request->all());
        return response()->json($lote, 201);
    }

    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);
        
        $request->validate([
            'hacienda_codigo' => 'sometimes|nullable|string',
            'nombre_lote' => 'sometimes|required|string',
            'capacidad_maxima' => 'sometimes|required|integer|min:1'
        ]);

        $lote->update($request->all());
        return response()->json($lote);
    }

    public function destroy($id)
    {
        $lote = Lote::findOrFail($id);
        
        $hasViveros = Vivero::where('lote_id', $lote->id)->exists();
        if ($hasViveros) {
            return response()->json([
                'message' => 'No se puede eliminar este lote porque tiene viveros asignados actualmente.'
            ], 400);
        }

        $lote->delete();
        return response()->json(null, 204);
    }
}
