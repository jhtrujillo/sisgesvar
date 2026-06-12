<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        // Restrict to ADMIN/JEFE only
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['ADMIN', 'JEFE'])) {
            abort(403, 'No tienes permisos para auditar la plataforma.');
        }

        $query = Actividad::with('user:id,name,role');

        // Filter by term or description
        if ($request->has('search') && !empty($request->search)) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('descripcion', 'ilike', "%{$s}%")
                  ->orWhere('accion', 'ilike', "%{$s}%");
            });
        }

        // Filter by Action Category
        if ($request->has('accion') && !empty($request->accion)) {
            $query->where('accion', $request->accion);
        }

        // Filter by User
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $actividades = $query->latest()->paginate(25)->withQueryString();

        $users = \App\Models\User::select('id', 'name')->orderBy('name')->get();

        // --- 📊 CÁLCULO DE MÉTRICAS EN TIEMPO REAL PARA EL DASHBOARD ---
        $kpis = [
            'total_historico' => Actividad::count(),
            'operaciones_hoy' => Actividad::whereDate('created_at', \Illuminate\Support\Carbon::today())->count(),
            'estandarizaciones' => Actividad::where('accion', 'ESTANDARIZACION_MASIVA')->count(),
            'ediciones_celda' => Actividad::where('accion', 'EDICION_CELDA')->count(),
        ];

        return response()->json([
            'actividades' => $actividades,
            'users' => $users,
            'kpis' => $kpis,
            'filters' => $request->only(['search', 'accion', 'user_id']),
        ], 200);
    }
}
