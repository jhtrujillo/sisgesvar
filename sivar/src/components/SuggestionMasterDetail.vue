<template>
  <div class="flex flex-col md:flex-row gap-4 h-[750px] w-full text-sm">
    <!-- Panel Izquierdo: Hembras -->
    <div class="w-full md:w-1/4 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
      <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sticky top-0 z-10">
        <div class="flex justify-between items-center font-bold text-slate-700">
          <span class="text-sm">Madres (Hembras)</span>
          <span class="text-xs font-normal text-slate-500 bg-white px-2 py-1 rounded-md border shadow-sm">{{ filteredFemales.length }} / {{ females.length }}</span>
        </div>
        
        <!-- Buscador de Hembras -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          <input 
            v-model="searchFemaleQuery" 
            @focus="showFemaleDropdown = true"
            @blur="hideDropdownDelayed"
            type="text" 
            class="block w-full pl-9 pr-3 py-1.5 border border-slate-300 rounded-md leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm shadow-sm transition-colors" 
            placeholder="Buscar variedad..." 
          />
          
          <!-- Lista Desplegable Flotante -->
          <div v-if="showFemaleDropdown && filteredFemales.length > 0" class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-slate-200 max-h-48 overflow-y-auto">
            <ul class="py-1 text-sm text-slate-700">
              <li 
                v-for="(row, idx) in filteredFemales" 
                :key="idx"
                @mousedown.prevent="selectFemaleFromDropdown(row)"
                class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer border-b border-slate-100 last:border-0"
              >
                {{ row?.[0]?.varA }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="flex-1 overflow-auto p-3 space-y-2 scrollbar-custom bg-slate-50/30">
        <div 
          v-for="(row, idx) in filteredFemales" :key="idx"
          @click="$emit('select-female', row)"
          :class="[
            'p-3 rounded-lg border cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-md min-w-[220px]', 
            selectedFemaleRow === row 
              ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-100 shadow-sm' 
              : 'bg-white border-slate-200 hover:border-emerald-300'
          ]"
        >
          <div class="flex justify-between items-start mb-2 gap-2">
            <span class="font-bold text-slate-800 text-sm truncate" :title="row?.[0]?.varA">{{ row?.[0]?.varA }}</span>
            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold border border-slate-200 whitespace-nowrap">
              VM: {{ row?.[0]?.vm }}
            </span>
          </div>
          <div class="flex justify-between items-center text-xs text-slate-500 font-medium gap-2">
            <span class="flex items-center gap-1 whitespace-nowrap">
              <span class="w-2 h-2 rounded-full flex-shrink-0" :class="Number(row?.[0]?.polen) <= 20 ? 'bg-pink-400' : 'bg-slate-400'"></span>
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
    <div class="w-full md:w-3/4 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
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
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pb-10">
             <div 
                v-for="cell in sortedMales" 
                :key="cell.varB" 
                :class="['rounded-xl p-4 transition-all relative overflow-hidden', getCardStyle(cell).container]"
             >

                <div class="flex justify-between items-start mb-3 mt-1">
                  <div>
                    <span class="font-bold text-sm block" :class="getCardStyle(cell).textMain">{{ cell.varB }}</span>
                    <span class="text-[10px] font-medium" :class="getCardStyle(cell).textSub">{{ getHeatmapLabel(cell) }}</span>
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
                
                <div :class="['grid grid-cols-2 gap-y-3 gap-x-2 text-xs mb-4 p-2 rounded-lg border', getCardStyle(cell).innerBox]">
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="getCardStyle(cell).textLabel">Dist. Genética</span>
                    <span class="font-black" :class="getCardStyle(cell).textMain">{{ getDistanciaLocal(cell.varA, cell.varB) }}</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="getCardStyle(cell).textLabel">VM Macho</span>
                    <span class="font-black" :class="getCardStyle(cell).textMain">{{ cell.vm2 }}</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="getCardStyle(cell).textLabel">Polen</span>
                    <span class="font-black flex items-center gap-1" :class="getCardStyle(cell).textMain">
                      <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                      {{ cell.polen2 }}%
                    </span>
                  </div>
                   <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="getCardStyle(cell).textLabel">Disp. Macho</span>
                    <span class="font-black" :class="getCardStyle(cell).textMain">{{ getDisp(cell.varB) }}</span>
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
import { ref, computed } from 'vue';

const props = defineProps<{
  females: any[];
  selectedFemaleRow: any | null;
  sortedMales: any[];
  ocultarRiesgos: boolean;
  tipoMapaCalor: string;
  getDisp: (varName: string) => number | string;
  getDistanciaLocal: (varA: string, varB: string) => string | number;
  getIndiceCombinadoLocal: (varA: string, varB: string, vm: string | number) => number;
}>();

const emit = defineEmits(['select-female', 'toggle-cross', 'toggle-riesgos', 'open-flower-modal']);

const searchFemaleQuery = ref('');
const showFemaleDropdown = ref(false);

const filteredFemales = computed(() => {
  if (!searchFemaleQuery.value) return props.females;
  const q = searchFemaleQuery.value.toLowerCase();
  return props.females.filter(row => {
    const femaleName = row?.[0]?.varA || '';
    return femaleName.toLowerCase().includes(q);
  });
});

const selectFemaleFromDropdown = (row: any) => {
  emit('select-female', row);
  showFemaleDropdown.value = false;
  searchFemaleQuery.value = '';
};

const hideDropdownDelayed = () => {
  // Retardo pequeño para permitir que el click en la lista se procese antes de ocultarla
  setTimeout(() => {
    showFemaleDropdown.value = false;
  }, 200);
};

const getAffinity = (cell: any) => {
  const ic = props.getIndiceCombinadoLocal(cell.varA, cell.varB, cell.vm2);
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

const getCardStyle = (cell: any) => {
  const isSelected = cell.viabilidad;
  const baseBorder = isSelected 
    ? 'ring-2 shadow-md border-[3px] ' 
    : 'opacity-70 hover:opacity-100 hover:-translate-y-0.5 hover:shadow-md border-2 ';
  
  // Helper to generate the style object
  const createStyle = (bgSelected: string, borderSelected: string, borderUnselected: string, isDark: boolean) => {
    // Both selected and unselected use the same background color now
    const bg = bgSelected;
    const border = isSelected ? borderSelected : borderUnselected;
    // Both use the same text contrast now depending on the background
    const darkText = isDark;
    
    return {
      container: `${baseBorder} ${bg} ${border} ${isSelected ? 'ring-emerald-500' : ''}`,
      textMain: darkText ? 'text-white' : 'text-slate-800',
      textSub: darkText ? 'text-white/80' : 'text-slate-500',
      textLabel: darkText ? 'text-white/70' : 'text-slate-400',
      innerBox: darkText ? 'bg-black/10 border-white/10' : 'bg-white/60 border-slate-200/50'
    };
  };

  if (props.tipoMapaCalor === 'none') {
    return createStyle('bg-emerald-50', 'border-emerald-500', 'border-slate-200', false);
  }

  if (props.tipoMapaCalor === 'ic') {
    const ic = props.getIndiceCombinadoLocal(cell.varA, cell.varB, cell.vm2);
    if (isNaN(ic)) return createStyle('bg-slate-100', 'border-slate-300', 'border-slate-200', false);
    
    if (ic >= 80) return createStyle('bg-indigo-600', 'border-indigo-700', 'border-indigo-400', true);
    if (ic >= 65) return createStyle('bg-sky-400', 'border-sky-500', 'border-sky-300', false);
    if (ic >= 50) return createStyle('bg-emerald-500', 'border-emerald-600', 'border-emerald-400', true);
    return createStyle('bg-rose-500', 'border-rose-600', 'border-rose-400', true);
  }

  // Fallback to DG
  const val = Number(props.getDistanciaLocal(cell.varA, cell.varB));
  if (isNaN(val)) return createStyle('bg-slate-100', 'border-slate-300', 'border-slate-200', false);
  
  if (val >= 0.65) return createStyle('bg-blue-600', 'border-blue-700', 'border-blue-400', true);
  if (val >= 0.55) return createStyle('bg-sky-400', 'border-sky-500', 'border-sky-300', false);
  if (val >= 0.45) return createStyle('bg-slate-300', 'border-slate-400', 'border-slate-300', false);
  if (val >= 0.35) return createStyle('bg-amber-400', 'border-amber-500', 'border-amber-300', false);
  return createStyle('bg-orange-500', 'border-orange-600', 'border-orange-400', true);
};

const getHeatmapLabel = (cell: any) => {
  if (props.tipoMapaCalor === 'none') return 'Sin Color';

  if (props.tipoMapaCalor === 'ic') {
    const ic = props.getIndiceCombinadoLocal(cell.varA, cell.varB, cell.vm2);
    if (isNaN(ic)) return 'Sin Genotipo';
    if (ic >= 80) return 'Excelente (IC ≥ 80)';
    if (ic >= 65) return 'Bueno (IC ≥ 65)';
    if (ic >= 50) return 'Aceptable (IC ≥ 50)';
    return 'Riesgo (IC < 50)';
  }

  const val = Number(props.getDistanciaLocal(cell.varA, cell.varB));
  if (isNaN(val)) return 'Sin Genotipo';
  if (val >= 0.65) return 'Excelente (>0.65)';
  if (val >= 0.55) return 'Bueno (>0.55)';
  if (val >= 0.45) return 'Aceptable (>0.45)';
  if (val >= 0.35) return 'Cerca (>0.35)';
  return 'Riesgo (<0.35)';
};
</script>
