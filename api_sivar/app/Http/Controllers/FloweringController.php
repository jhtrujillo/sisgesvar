<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Flowering;

class FloweringController extends Controller
{
    public function floweringList(Request $request)
    {
        $historico = $request->query('historico') === 'true';
        
        try {
            $query = Flowering::leftJoin('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr')
                ->leftJoin('caracteres', 'caracteres.id_crcter', '=', 'floracion.id_crcter')
                ->leftJoin('usuario', 'usuario.id_usrio', '=', 'floracion.usrio_edto');
                
            if (!$historico) {
                $fechaf = now()->format('Y-m-d');
                $fechai = now()->subDay()->format('Y-m-d');
                $query->where('floracion.estado', 0)
                      ->whereBetween('floracion.fcha', [$fechai, $fechaf]);
            }

            $model = $query->select(
                'floracion.*',
                'remote_pg_sipro.nm_prycto',
                'caracteres.nmbre_crcter',
                'usuario.prmer_nmbre',
                'usuario.aplldo'
            )->get();

            return response()->json($model);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
