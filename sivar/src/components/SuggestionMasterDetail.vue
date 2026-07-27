<template>
  <div class="flex flex-col md:flex-row gap-4 h-[700px] w-full text-sm">
    <!-- Panel Izquierdo: Hembras -->
    <div class="w-full md:w-1/3 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
      <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-700 flex justify-between items-center sticky top-0 z-10">
        <span class="text-sm">Madres (Hembras)</span>
        <span class="text-xs font-normal text-slate-500 bg-white px-2 py-1 rounded-md border shadow-sm">{{ females.length }}</span>
      </div>
      <div class="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-custom bg-slate-50/30">
        <div 
          v-for="(row, idx) in females" :key="idx"
          @click="$emit('select-female', row)"
          :class="[
            'p-3 rounded-lg border cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-md', 
            selectedFemaleRow === row 
              ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-100 shadow-sm' 
              : 'bg-white border-slate-200 hover:border-emerald-300'
          ]"
        >
          <div class="flex justify-between items-start mb-2">
            <span class="font-bold text-slate-800 text-sm">{{ row[0]?.varA }}</span>
            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold border border-slate-200">
              VM: {{ row[0]?.vm }}
            </span>
          </div>
          <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
            <span class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full" :class="Number(row[0]?.polen) <= 20 ? 'bg-pink-400' : 'bg-slate-400'"></span>
              Polen: {{ row[0]?.polen ? row[0]?.polen + '%' : 'N/A' }}
            </span>
            <span class="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-[10px] font-bold">
              Disp: {{ getDisp(row[0]?.varA) }}
            </span>
          </div>
        </div>
        <div v-if="females.length === 0" class="text-center py-10 text-slate-400 text-xs">
          No hay hembras disponibles
        </div>
      </div>
    </div>

    <!-- Panel Derecho: Machos -->
    <div class="w-full md:w-2/3 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
      <div v-if="!selectedFemaleRow" class="flex-1 flex flex-col items-center justify-center text-slate-400 p-10 text-center bg-slate-50/50">
        <svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
        </svg>
        <p class="text-lg font-medium text-slate-500 mb-2">Selecciona una madre</p>
        <p class="text-sm">Para ver la lista de machos compatibles ordenados de mejor a peor opción.</p>
      </div>
      <div v-else class="flex-1 flex flex-col overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center sticky top-0 z-10">
          <div>
            <div class="flex items-center gap-2 mb-0.5">
              <h3 class="font-black text-slate-800 text-lg">{{ selectedFemaleRow[0]?.varA }}</h3>
              <span class="text-[10px] font-bold text-pink-600 bg-pink-100 px-2 py-0.5 rounded-full">Madre</span>
            </div>
            <p class="text-xs text-slate-500 font-medium">Machos sugeridos ordenados por afinidad</p>
          </div>
          <div class="flex gap-3">
             <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer bg-white px-3 py-1.5 rounded-lg border shadow-sm hover:bg-slate-50">
               <input type="checkbox" :checked="ocultarRiesgos" @change="$emit('toggle-riesgos')" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
               Ocultar Riesgos (DG &lt; 0.35)
             </label>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 scrollbar-custom bg-slate-100/50">
           <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
             <div 
                v-for="cell in sortedMales" 
                :key="cell.varB" 
                :class="[
                  'rounded-xl border p-4 transition-all relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5', 
                  cell.viabilidad 
                    ? 'ring-2 ring-emerald-500 bg-emerald-50 border-transparent' 
                    : 'bg-white border-slate-200 hover:border-emerald-300'
                ]"
             >
                <!-- Indicador de color de riesgo arriba -->
                <div class="absolute top-0 left-0 w-full h-1" :class="getHeatmapBg(cell)"></div>

                <div class="flex justify-between items-start mb-3 mt-1">
                  <div>
                    <span class="font-bold text-slate-800 text-sm block">{{ cell.varB }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">{{ getHeatmapLabel(cell) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <!-- Botón para ajustar flores (solo visible si está seleccionado) -->
                    <button 
                      v-if="cell.viabilidad"
                      @click="$emit('open-flower-modal', cell)"
                      class="flex items-center justify-center space-x-1 bg-white hover:bg-slate-50 rounded-lg border border-slate-200 px-2 py-1 shadow-sm text-[10px] text-slate-700 transition-colors"
                      title="Ajustar consumo de flores"
                    >
                      <span>⚙️</span>
                      <span class="font-bold">{{ cell.flores_madre ?? 1 }} / {{ cell.flores_padre ?? 1 }}</span>
                    </button>

                    <!-- Botón para seleccionar/deseleccionar -->
                    <button 
                      @click="$emit('toggle-cross', cell)" 
                      :class="[
                        'w-8 h-8 rounded-lg flex items-center justify-center border transition-colors', 
                        cell.viabilidad 
                          ? 'bg-emerald-500 border-emerald-500 text-white shadow-inner' 
                          : 'bg-slate-50 border-slate-200 text-slate-300 hover:border-emerald-500 hover:text-emerald-500'
                      ]"
                      :title="cell.viabilidad ? 'Quitar cruce' : 'Seleccionar cruce'"
                    >
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs mb-4 p-2 bg-slate-50 rounded-lg border border-slate-100">
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dist. Genética</span>
                    <span class="font-black text-slate-700">{{ getDistanciaLocal(cell.varA, cell.varB) }}</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">VM Macho</span>
                    <span class="font-black text-slate-700">{{ cell.vm2 }}</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Polen</span>
                    <span class="font-black text-slate-700 flex items-center gap-1">
                      <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                      {{ cell.polen2 }}%
                    </span>
                  </div>
                   <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Disp. Macho</span>
                    <span class="font-black text-emerald-700">{{ getDisp(cell.varB) }}</span>
                  </div>
                </div>

                <!-- Barra de afinidad (Índice Combinado) -->
                <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden" title="Índice Combinado">
                  <div 
                    class="h-full transition-all duration-500" 
                    :class="getAffinityColor(cell)"
                    :style="{ width: getAffinity(cell) + '%' }"
                  ></div>
                </div>

             </div>

             <div v-if="sortedMales.length === 0" class="col-span-full text-center py-10 text-slate-400 text-xs">
                No se encontraron machos compatibles para esta hembra bajo los filtros actuales.
             </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  females: any[];
  selectedFemaleRow: any | null;
  sortedMales: any[];
  ocultarRiesgos: boolean;
  getDisp: (varName: string) => number | string;
  getDistanciaLocal: (varA: string, varB: string) => string | number;
  getIndiceCombinadoLocal: (varA: string, varB: string, vm: string | number) => number;
}>();

defineEmits(['select-female', 'toggle-cross', 'toggle-riesgos', 'open-flower-modal']);

const getAffinity = (cell: any) => {
  const ic = props.getIndiceCombinadoLocal(cell.varA, cell.varB, cell.vm);
  if (isNaN(ic)) return 0;
  return Math.min(100, Math.max(0, ic));
};

const getAffinityColor = (cell: any) => {
  const ic = getAffinity(cell);
  if (ic >= 80) return 'bg-indigo-500';
  if (ic >= 65) return 'bg-blue-500';
  if (ic >= 50) return 'bg-emerald-500';
  if (ic >= 35) return 'bg-amber-500';
  return 'bg-rose-500';
};

const getHeatmapBg = (cell: any) => {
  const val = Number(props.getDistanciaLocal(cell.varA, cell.varB));
  if (isNaN(val)) return 'bg-slate-300';
  if (val >= 0.65) return 'bg-blue-600';
  if (val >= 0.55) return 'bg-sky-400';
  if (val >= 0.45) return 'bg-slate-300';
  if (val >= 0.35) return 'bg-amber-400';
  return 'bg-orange-500';
};

const getHeatmapLabel = (cell: any) => {
  const val = Number(props.getDistanciaLocal(cell.varA, cell.varB));
  if (isNaN(val)) return 'Sin Genotipo';
  if (val >= 0.65) return 'Excelente (>0.65)';
  if (val >= 0.55) return 'Bueno (>0.55)';
  if (val >= 0.45) return 'Aceptable (>0.45)';
  if (val >= 0.35) return 'Cerca (>0.35)';
  return 'Riesgo (<0.35)';
};
</script>
