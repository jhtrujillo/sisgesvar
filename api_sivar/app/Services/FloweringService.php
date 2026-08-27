<?php

namespace App\Services;

use App\Models\Flowering;

class FloweringService
{
    /**
     * Get the list of flowerings, either all history or just recent uncompleted ones.
     *
     * @param bool $historico
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFloweringList(bool $historico)
    {
        $query = Flowering::leftJoin('remote_pg_sipro', 'remote_pg_sipro.id_prycto', '=', 'floracion.id_pr')
            ->leftJoin('caracteres', 'caracteres.id_crcter', '=', 'floracion.id_crcter')
            ->leftJoin('usuario', 'usuario.id_usrio', '=', 'floracion.usrio_edto');
            
        if (!$historico) {
            $fechaf = now()->format('Y-m-d');
            $fechai = now()->subDay()->format('Y-m-d');
            $query->where('floracion.estado', 0)
                  ->whereBetween('floracion.fcha', [$fechai, $fechaf]);
        }

        return $query->select(
            'floracion.*',
            'remote_pg_sipro.nm_prycto',
            'caracteres.nmbre_crcter',
            'usuario.prmer_nmbre',
            'usuario.aplldo'
        )->get();
    }
}
