import type { Ref } from "vue";

export type HeatmapMode = "none" | "dg" | "ic" | string;

/**
 * Composable con los cálculos puros de Cruzamientos (Índice Combinado,
 * Distancia Genética con caché O(1), clases de mapa de calor y VM por fila).
 */
export function useCrossingCalculations(
  tipoMapaCalor: Ref<HeatmapMode>,
  getDistanciasSource: () => Record<string, Record<string, any>> | undefined
) {
  let distanciasCache: Map<string, Map<string, any>> | null = null;

  function buildDistanciasCache() {
    const distancias = getDistanciasSource() || {};
    const cache = new Map<string, Map<string, any>>();

    for (const [keyA, subDist] of Object.entries(distancias)) {
      const cleanA = keyA.trim().toUpperCase();
      const subCache = new Map<string, any>();

      if (subDist && typeof subDist === "object") {
        for (const [keyB, val] of Object.entries(subDist)) {
          subCache.set(keyB.trim().toUpperCase(), val);
        }
      }
      cache.set(cleanA, subCache);
    }
    distanciasCache = cache;
  }

  function resetDistanciasCache() {
    distanciasCache = null;
  }

  function getDistancia(varA: string, varB: string): string {
    if (!varA || !varB || varA === varB) return "NA";

    if (!distanciasCache) {
      buildDistanciasCache();
    }

    const cleanA = varA.trim().toUpperCase();
    const cleanB = varB.trim().toUpperCase();

    const subDist = distanciasCache?.get(cleanA);
    if (!subDist) return "NA";

    const val = subDist.get(cleanB);
    return val !== null && val !== undefined ? String(val) : "NA";
  }

  function getIndiceCombinado(
    varA: string,
    varB: string,
    vmA: number | string | null | undefined,
    vmB?: number | string | null
  ): number {
    const dgRaw = getDistancia(varA, varB);
    const dgVal =
      dgRaw === null || dgRaw === undefined || dgRaw === "" || dgRaw === "-" || dgRaw === "NA"
        ? NaN
        : Number(dgRaw);

    const validA = vmA !== null && vmA !== undefined && vmA !== "" && !isNaN(Number(vmA));
    const validB = vmB !== null && vmB !== undefined && vmB !== "" && !isNaN(Number(vmB));

    let vmVal = NaN;
    if (validA && validB) {
      vmVal = (Number(vmA) + Number(vmB)) / 2;
    } else if (validA) {
      vmVal = Number(vmA);
    } else if (validB) {
      vmVal = Number(vmB);
    }

    if (isNaN(dgVal) || isNaN(vmVal)) return NaN;

    // Normalizar VM: 1 es excelente (100 pts), 9 es malo (0 pts). Rango de 8.
    let puntajeVM = ((9.0 - vmVal) / 8.0) * 100;
    if (puntajeVM < 0) puntajeVM = 0;
    if (puntajeVM > 100) puntajeVM = 100;

    // Normalizar DG: Topado en 0.70 (100 pts)
    let puntajeDG = (dgVal / 0.7) * 100;
    if (puntajeDG > 100) puntajeDG = 100;
    if (puntajeDG < 0) puntajeDG = 0;

    // Índice Combinado (60% VM, 40% DG)
    return puntajeVM * 0.6 + puntajeDG * 0.4;
  }

  function isDarkBackground(
    varA: string,
    varB: string,
    vmA: number | string | null | undefined,
    vmB?: number | string | null
  ): boolean {
    if (tipoMapaCalor.value === "ic") {
      const ic = getIndiceCombinado(varA, varB, vmA, vmB);
      return !isNaN(ic) && (ic >= 80 || ic < 50);
    } else if (tipoMapaCalor.value === "dg") {
      const dgVal = getDistancia(varA, varB);
      const val = Number(dgVal);
      return !isNaN(val) && (val >= 0.65 || val < 0.35);
    }
    return false;
  }

  function getHeatmapClass(
    varA: string,
    varB: string,
    viabilidad: boolean,
    vmA: number | string | null | undefined,
    vmB?: number | string | null
  ): string {
    if (tipoMapaCalor.value === "none") {
      if (!viabilidad) return "bg-slate-50/50 hover:bg-slate-100/50 border-r border-slate-100 text-slate-400 opacity-60";
      return "bg-emerald-50/50 hover:bg-emerald-100/50 border-r border-emerald-100/50 text-emerald-800";
    }

    if (tipoMapaCalor.value === "ic") {
      const ic = getIndiceCombinado(varA, varB, vmA, vmB);
      if (isNaN(ic)) return "bg-emerald-50/50 border-r border-emerald-100/50 text-emerald-800 hover:bg-emerald-100/50";
      if (ic >= 80) return "bg-indigo-600/90 text-white font-black border-r border-indigo-700/50 shadow-inner hover:bg-indigo-700";
      if (ic >= 65) return "bg-sky-400/80 text-slate-900 font-extrabold border-r border-sky-500/30 hover:bg-sky-500/90";
      if (ic >= 50) return "bg-slate-200/60 text-slate-800 font-bold border-r border-slate-300/30 hover:bg-slate-300/80";
      return "bg-rose-500/90 text-white font-semibold border-r border-rose-600/30 hover:bg-rose-600/80";
    }

    const dgVal = getDistancia(varA, varB);
    const val = Number(dgVal);
    if (isNaN(val)) {
      return "bg-emerald-50/50 border-r border-emerald-100/50 text-emerald-800 hover:bg-emerald-100/50";
    }
    if (val >= 0.65) {
      return "bg-blue-600/90 text-white font-black border-r border-blue-700/50 shadow-inner hover:bg-blue-700";
    } else if (val >= 0.55) {
      return "bg-sky-400/60 text-slate-900 font-extrabold border-r border-sky-500/30 hover:bg-sky-500/50";
    } else if (val >= 0.45) {
      return "bg-slate-200/60 text-slate-800 font-bold border-r border-slate-300/30 hover:bg-slate-300/40";
    } else if (val >= 0.35) {
      return "bg-amber-300/80 text-amber-900 font-semibold border-r border-amber-400/30 hover:bg-amber-400/50";
    } else {
      return "bg-orange-500/90 text-white font-semibold border-r border-orange-600/30 hover:bg-orange-600/80";
    }
  }

  function getRowVm(row: any[]): string | number {
    if (!row || row.length === 0) return "N/D";
    const validCell = row.find((cell) => cell && cell.vm !== null && cell.vm !== undefined && cell.vm !== "");
    return validCell ? validCell.vm : "N/D";
  }

  return {
    buildDistanciasCache,
    resetDistanciasCache,
    getDistancia,
    getIndiceCombinado,
    isDarkBackground,
    getHeatmapClass,
    getRowVm
  };
}
