<?php

namespace App\Http\Controllers;

use App\Models\Vivero;
use App\Models\ViveroParcela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ViveroParcelaController extends Controller
{
    public function index($vivero_id)
    {
        $vivero = Vivero::findOrFail($vivero_id);
        $parcelas = $vivero->parcelas()->with(['variedad', 'caracter'])->orderBy('numero_parcela')->get();
        return response()->json($parcelas);
    }

    public function store(Request $request, $vivero_id)
    {
        $vivero = Vivero::findOrFail($vivero_id);

        $data = $request->all();
        if (isset($data['numero_parcela_origen']) && $data['numero_parcela_origen'] === '') {
            $data['numero_parcela_origen'] = null;
        }
        if (isset($data['id_plot_origen']) && $data['id_plot_origen'] === '') {
            $data['id_plot_origen'] = null;
        }

        $validator = Validator::make($data, [
            'numero_parcela' => 'required|numeric',
            'variedad_id' => 'required',
            'numero_parcela_origen' => 'nullable|numeric',
            'id_plot_origen' => 'nullable',
            'caracter_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validar que no se duplique ni el plot ni la variedad
        $existsPlot = $vivero->parcelas()->where('numero_parcela', $data['numero_parcela'])->exists();
        if ($existsPlot) {
            return response()->json(['message' => 'El número de parcela ya existe para este vivero.'], 422);
        }

        $existsVariedad = $vivero->parcelas()->where('variedad_id', $data['variedad_id'])->exists();
        if ($existsVariedad) {
            return response()->json(['message' => 'Esta variedad ya fue agregada a este vivero.'], 422);
        }

        $parcela = $vivero->parcelas()->create([
            'numero_parcela' => $data['numero_parcela'],
            'variedad_id' => $data['variedad_id'],
            'numero_parcela_origen' => $data['numero_parcela_origen'] ?? null,
            'id_plot_origen' => $data['id_plot_origen'] ?? null,
            'caracter_id' => $data['caracter_id'] ?? null,
        ]);

        // Load relationships before returning
        $parcela->load(['variedad', 'caracter']);

        return response()->json($parcela, 201);
    }

    public function importBatch(Request $request, $vivero_id)
    {
        $vivero = Vivero::findOrFail($vivero_id);

        $request->validate([
            'parcelas' => 'required|array',
            'parcelas.*.numero_parcela' => 'required|numeric',
            'parcelas.*.variedad_id' => 'required',
            'parcelas.*.numero_parcela_origen' => 'nullable|numeric',
            'parcelas.*.id_plot_origen' => 'nullable',
            'parcelas.*.caracter_id' => 'nullable|numeric',
        ]);

        $parcelasData = $request->input('parcelas');
        $insertedIds = [];

        DB::beginTransaction();
        try {
            foreach ($parcelasData as $data) {
                // Remove empty strings to prevent 422
                $numero_parcela_origen = (isset($data['numero_parcela_origen']) && $data['numero_parcela_origen'] !== '') ? $data['numero_parcela_origen'] : null;
                $id_plot_origen = (isset($data['id_plot_origen']) && $data['id_plot_origen'] !== '') ? $data['id_plot_origen'] : null;
                $caracter_id = (isset($data['caracter_id']) && $data['caracter_id'] !== '') ? $data['caracter_id'] : null;

                // Check if exists (either plot or variedad)
                $existsPlot = $vivero->parcelas()->where('numero_parcela', $data['numero_parcela'])->exists();
                if ($existsPlot) continue;

                $existsVariedad = $vivero->parcelas()->where('variedad_id', $data['variedad_id'])->exists();
                if ($existsVariedad) continue;

                $parcela = $vivero->parcelas()->create([
                    'numero_parcela' => $data['numero_parcela'],
                    'variedad_id' => $data['variedad_id'],
                    'numero_parcela_origen' => $numero_parcela_origen,
                    'id_plot_origen' => $id_plot_origen,
                    'caracter_id' => $caracter_id,
                ]);
                
                $insertedIds[] = $parcela->id;
            }
            DB::commit();
            return response()->json(['message' => 'Parcelas importadas correctamente', 'inserted_count' => count($insertedIds)], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al importar parcelas', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($vivero_id, $parcela_id)
    {
        $parcela = ViveroParcela::where('vivero_id', $vivero_id)->findOrFail($parcela_id);
        $parcela->delete();

        return response()->json(null, 204);
    }

    public function destroyAll($vivero_id)
    {
        $vivero = Vivero::findOrFail($vivero_id);
        $vivero->parcelas()->delete();

        return response()->json(['message' => 'Todas las parcelas han sido eliminadas correctamente.']);
    }
}
