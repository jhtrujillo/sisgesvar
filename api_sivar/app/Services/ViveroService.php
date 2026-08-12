<?php

namespace App\Services;

use App\Models\Vivero;

class ViveroService
{
    /**
     * Generates a unique identifier for a nursery (vivero).
     */
    public function generarIdentificadorUnico($ingenioCd, $haciendaCd, $suerteCd, $fechaSiembra, $consecutivo)
    {
        $ingenio = $ingenioCd ?: '00';
        $hacienda = $haciendaCd ?: '00';
        $haciendaCleaned = ltrim($hacienda, '0');
        $suerte = $suerteCd ?: '00';
        $suerteCleaned = trim(preg_replace('/\b(lote|vivero)\b/i', '', $suerte));
        $anioSiembra = $fechaSiembra ? date('Y', strtotime($fechaSiembra)) : date('Y');

        return sprintf('%s%s-%s-%s-%d', $ingenio, $anioSiembra, $haciendaCleaned, $suerteCleaned, $consecutivo);
    }

    /**
     * Loads the hierarchical tree structure for a given nursery.
     */
    public function getEstructura($id)
    {
        $vivero = Vivero::with([
            'parcelas.variedad', 
            'origenLote', 
            'lote',
            'cosechas'
        ])->findOrFail($id);

        $this->loadEstructuraRecursiva($vivero);
        $vivero->identificador_origen = $this->formatIdViveroOrigen($vivero);

        return $vivero;
    }

    private function loadEstructuraRecursiva($vivero)
    {
        // 1. Fetch children where this vivero is the explicitly linked parent
        $childrenById = Vivero::with(['parcelas.variedad', 'origenLote', 'lote', 'cosechas'])
            ->where('origen_vivero_id', $vivero->id)
            ->get();

        // 2. Fetch children linked by the prefix of origen_parcela
        $baseId = explode('-', $vivero->identificador_unico);
        $baseIdStr = count($baseId) >= 4 ? implode('-', array_slice($baseId, 0, 4)) : $vivero->identificador_unico;
        
        $childrenByPrefix = Vivero::with(['parcelas.variedad', 'origenLote', 'lote', 'cosechas'])
            ->where('origen_parcela', 'like', $baseIdStr . '-%')
            ->whereNull('origen_vivero_id') 
            ->where('id', '!=', $vivero->id)
            ->get();

        $children = $childrenById->concat($childrenByPrefix)->unique('id');

        $plotCortes = []; 
        $generalCortes = collect(); 

        foreach ($children as $child) {
            $matchedPlot = null;
            if ($child->origen_parcela) {
                $parts = explode('-', $child->origen_parcela);
                if (count($parts) >= 5 && is_numeric($parts[4])) {
                    $matchedPlot = (int)$parts[4];
                }
            }

            if ($matchedPlot !== null) {
                if (!isset($plotCortes[$matchedPlot])) {
                    $plotCortes[$matchedPlot] = collect();
                }
                $plotCortes[$matchedPlot]->push($child);
            } else {
                $generalCortes->push($child);
            }
        }

        // Recursively load descendants
        foreach ($children as $child) {
            $this->loadEstructuraRecursiva($child);
        }

        // Distribute to actual parcelas
        foreach ($vivero->parcelas as $parcela) {
            $num = (int)$parcela->numero_parcela;
            $parcela->cortes_recursivos = isset($plotCortes[$num]) ? $plotCortes[$num]->values() : collect();
        }

        // Assign direct children to the vivero object
        $vivero->hijos_directos = $generalCortes->values();

        // Inject historical cuts (cosechas) as "Corte" nodes
        $cosechas = $vivero->cosechas()->orderBy('numero_corte_anterior', 'asc')->get();
        foreach ($cosechas as $cosecha) {
            $numCorte = $cosecha->numero_corte_anterior;
            $fakeCorte = [
                'id' => 'cosecha_' . $cosecha->id,
                'identificador_unico' => $vivero->identificador_unico . '-' . $numCorte,
                'nombre' => $vivero->nombre . ' (Corte ' . $numCorte . ')',
                'numero_corte' => $numCorte,
                'fecha_siembra' => $cosecha->fecha_cosecha,
                'ambiente' => $cosecha->ambiente,
                'is_historical_cut' => true,
                'hijos_directos' => [],
                'parcelas' => [],
                'cosechas' => []
            ];
            $vivero->hijos_directos->push($fakeCorte);
        }
    }

    private function formatIdViveroOrigen($vivero)
    {
        if ($vivero->origenVivero) {
            return $vivero->origenVivero->identificador_unico;
        }

        // Si tiene origen_parcela con formato de ID de parcela de Vivero
        if ($vivero->origen_parcela && count(explode('-', $vivero->origen_parcela)) > 3) {
            $parts = explode('-', $vivero->origen_parcela);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                array_pop($parts); 
                $cleanedParts = array_map(function($p) {
                    return trim(preg_replace('/\b(lote|vivero)\b/i', '', $p));
                }, $parts);
                return implode('-', $cleanedParts); 
            }
        }

        // Construir usando códigos de la base de datos
        $info = [];
        $ingenioAnio = '';
        if ($vivero->origen_ingenio) {
            $ingenioAnio .= $vivero->origen_ingenio;
        }
        if ($vivero->origen_anio) {
            $ingenioAnio .= $vivero->origen_anio;
        }
        if ($ingenioAnio !== '') {
            $info[] = $ingenioAnio;
        }
        if ($vivero->origen_hacienda) $info[] = $vivero->origen_hacienda;
        
        $loteNombre = '';
        if ($vivero->origenLote) {
            $loteNombre = $vivero->origenLote->nombre_lote;
        } else {
            $loteNombre = $vivero->origen_suerte;
        }
        if ($loteNombre) {
            $info[] = trim(preg_replace('/\b(lote|vivero)\b/i', '', $loteNombre));
        }
        
        if ($vivero->origen_parcela) {
            $info[] = trim(preg_replace('/\b(lote|vivero)\b/i', '', $vivero->origen_parcela));
        }

        $info = array_filter(array_map('trim', $info));

        return count($info) > 0 ? implode('-', $info) : 'N/A';
    }
}
