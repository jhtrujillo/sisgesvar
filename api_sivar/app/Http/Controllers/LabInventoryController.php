<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabInventory;

class LabInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = LabInventory::query();
        if ($request->has('area')) {
            $query->where('area', $request->area);
        }
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area' => 'nullable|string|max:255',
            'consumible' => 'nullable|string|max:255',
            'actividad' => 'nullable|string|max:255',
            'codigo_cg1' => 'nullable|string|max:255',
            'descripcion_item' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'unidad' => 'nullable|string|max:255',
            'cantidad_en_stock' => 'nullable|numeric',
            'cantidad_critica' => 'nullable|numeric',
            'ubicacion' => 'nullable|string|max:255',
            'solicitante' => 'nullable|string|max:255',
            'fecha_solicitud' => 'nullable|date',
            'fecha_ultima_revision' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ]);

        $item = LabInventory::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = LabInventory::findOrFail($id);
        
        $validated = $request->validate([
            'area' => 'nullable|string|max:255',
            'consumible' => 'nullable|string|max:255',
            'actividad' => 'nullable|string|max:255',
            'codigo_cg1' => 'nullable|string|max:255',
            'descripcion_item' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'unidad' => 'nullable|string|max:255',
            'cantidad_en_stock' => 'nullable|numeric',
            'cantidad_critica' => 'nullable|numeric',
            'ubicacion' => 'nullable|string|max:255',
            'solicitante' => 'nullable|string|max:255',
            'fecha_solicitud' => 'nullable|date',
            'fecha_ultima_revision' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = LabInventory::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Eliminado exitosamente']);
    }

    public function getMovements($id)
    {
        $item = LabInventory::findOrFail($id);
        return response()->json($item->movements()->orderBy('created_at', 'desc')->get());
    }

    public function storeMovement(Request $request, $id)
    {
        $item = LabInventory::findOrFail($id);
        
        $validated = $request->validate([
            'tipo_movimiento' => 'required|in:INGRESO,EGRESO',
            'cantidad' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string'
        ]);

        $cantidad = (float) $validated['cantidad'];
        $stockAnterior = (float) $item->cantidad_en_stock;

        if ($validated['tipo_movimiento'] === 'EGRESO' && $cantidad > $stockAnterior) {
            return response()->json(['message' => 'La cantidad a retirar supera el stock actual disponible.'], 400);
        }

        $stockNuevo = $validated['tipo_movimiento'] === 'INGRESO' 
            ? $stockAnterior + $cantidad 
            : $stockAnterior - $cantidad;

        $user = auth('api')->user();
        $userName = $user ? $user->name : 'Usuario Desconocido';

        // Registrar movimiento
        $movement = $item->movements()->create([
            'tipo_movimiento' => $validated['tipo_movimiento'],
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'user_id' => $user ? $user->id : null,
            'user_name' => $userName,
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // Actualizar inventario padre
        $item->update(['cantidad_en_stock' => $stockNuevo]);

        // Lógica de alerta por correo si cruzó el umbral crítico
        if ($stockAnterior > $item->cantidad_critica && $stockNuevo <= $item->cantidad_critica) {
            $alertEmails = \App\Models\LabInventoryAlertEmail::where('activo', true)->pluck('email')->toArray();
            if (count($alertEmails) > 0) {
                try {
                    \Illuminate\Support\Facades\Mail::to($alertEmails)
                        ->send(new \App\Mail\LabInventoryCriticalAlert($item, $userName));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error enviando alerta de inventario: ' . $e->getMessage());
                }
            }
        }

        return response()->json($movement, 201);
    }

    public function deleteMovements($id)
    {
        $item = LabInventory::findOrFail($id);
        $item->movements()->delete();
        return response()->json(['message' => 'Historial eliminado exitosamente']);
    }
}
