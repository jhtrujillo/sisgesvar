<template>
  <div class="flex flex-col md:flex-row gap-4 h-[750px] w-full text-sm">
    <!-- Panel Izquierdo: Hembras -->
    <div class="w-full md:w-1/4 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
      <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sticky top-0 z-10">
        <div class="flex justify-between items-center font-bold text-slate-700">
          <span class="text-sm">Madres (Hembras)</span>
          <span class="text-xs font-normal text-slate-500 bg-white px-2 py-1 rounded-md border shadow-sm">{{ filteredFemales.length }} / {{ females.length }}</span>
        </div>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          <input v-model="searchFemaleQuery" @focus="showFemaleDropdown = true" @blur="hideDropdownDelayed" type="text"
            class="block w-full pl-9 pr-3 py-1.5 border border-slate-300 rounded-md leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm shadow-sm transition-colors"
            placeholder="Buscar variedad..." />
          <div v-if="showFemaleDropdown && filteredFemales.length > 0" class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-slate-200 max-h-48 overflow-y-auto">
            <ul class="py-1 text-sm text-slate-700">
              <li v-for="(row, idx) in filteredFemales" :key="idx" @mousedown.prevent="selectFemaleFromDropdown(row)"
                class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer border-b border-slate-100 last:border-0">
                {{ row?.[0]?.varA }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="flex-1 overflow-auto p-3 space-y-2 scrollbar-custom bg-slate-50/30">
        <div v-for="(row, idx) in filteredFemales" :key="idx" @click="$emit('select-female', row)"
          :class="['p-3 rounded-lg border cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-md', selectedFemaleRow === row ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-100 shadow-sm' : 'bg-white border-slate-200 hover:border-emerald-300']">
          <div class="flex justify-between items-start mb-2 gap-2">
            <span class="font-bold text-slate-800 text-sm truncate" :title="row?.[0]?.varA">{{ row?.[0]?.varA }}</span>
            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold border border-slate-200 whitespace-nowrap">VM: {{ row?.[0]?.vm }}</span>
          </div>
          <div class="flex justify-between items-center text-xs text-slate-500 font-medium gap-2">
            <span class="flex items-center gap-1 whitespace-nowrap">
              <span class="w-2 h-2 rounded-full flex-shrink-0" :class="Number(row?.[0]?.polen) <= 20 ? 'bg-pink-400' : 'bg-slate-400'"></span>
              Polen: {{ row[0]?.polen ? row[0]?.polen + '%' : 'N/A' }}
            </span>
            <span class="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-[10px] font-bold">Disp: {{ getDisp(row[0]?.varA) }}</span>
          </div>
        </div>
        <div v-if="females.length === 0" class="text-center py-10 text-slate-400 text-xs">No hay hembras disponibles</div>
      </div>
    </div>

    <!-- Panel Derecho: Tabla -->
    <div class="w-full md:w-3/4 bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
      <div v-if="!selectedFemaleRow" class="flex-1 flex flex-col items-center justify-center text-slate-400 p-10 text-center bg-slate-50/50">
        <svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
        </svg>
        <p class="text-lg font-medium text-slate-500 mb-2">Selecciona una madre</p>
        <p class="text-sm">Para ver la tabla de machos compatibles ordenados de mejor a peor opción.</p>
      </div>

      <div v-else class="flex-1 flex flex-col overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap justify-between items-center gap-2">
          <div>
            <div class="flex items-center gap-2 mb-0.5">
              <h3 class="font-black text-slate-800 text-lg">{{ selectedFemaleRow[0]?.varA }}</h3>
              <span class="text-[10px] font-bold text-pink-600 bg-pink-100 px-2 py-0.5 rounded-full">Madre</span>
            </div>
            <p class="text-xs text-slate-500 font-medium">{{ filteredSortedMales.length }} padres · Página {{ currentPage + 1 }} / {{ totalPages }}</p>
          </div>
          <div class="flex items-center gap-2">
            <input v-model="searchMaleQuery" type="text" placeholder="Filtrar padres..."
              class="pl-3 pr-3 py-1.5 border border-slate-300 rounded-md text-xs bg-white placeholder-slate-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 shadow-sm w-36" />
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer bg-white px-3 py-1.5 rounded-lg border shadow-sm hover:bg-slate-50">
              <input type="checkbox" :checked="ocultarRiesgos" @change="$emit('toggle-riesgos')" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
              Ocultar Riesgos (DG &lt; 0.35)
            </label>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto scrollbar-custom">
          <table class="w-full text-xs border-collapse">
            <thead class="sticky top-0 z-10">
              <tr class="bg-slate-800 text-white">
                <th class="px-3 py-2.5 text-left font-bold cursor-pointer hover:bg-slate-700 select-none" @click="setSort('varB')">
                  <div class="flex items-center gap-1"># Padre
                    <svg class="w-3 h-3 shrink-0 transition-transform" :class="sortCol==='varB' ? 'text-emerald-400' : 'text-slate-500'" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11L4 7h8z"/></svg>
                  </div>
                </th>
                <th class="px-3 py-2.5 text-center font-bold cursor-pointer hover:bg-slate-700 select-none w-20" @click="setSort('dg')">
                  <div class="flex items-center justify-center gap-1">DG
                    <svg class="w-3 h-3 shrink-0" :class="sortCol==='dg' ? 'text-emerald-400' : 'text-slate-500'" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11L4 7h8z"/></svg>
                  </div>
                </th>
                <th class="px-3 py-2.5 text-center font-bold cursor-pointer hover:bg-slate-700 select-none w-24" @click="setSort('vm')">
                  <div class="flex items-center justify-center gap-1">VM Macho
                    <svg class="w-3 h-3 shrink-0" :class="sortCol==='vm' ? 'text-emerald-400' : 'text-slate-500'" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11L4 7h8z"/></svg>
                  </div>
                </th>
                <th class="px-3 py-2.5 text-center font-bold cursor-pointer hover:bg-slate-700 select-none w-20" @click="setSort('ic')">
                  <div class="flex items-center justify-center gap-1">Índice
                    <svg class="w-3 h-3 shrink-0" :class="sortCol==='ic' ? 'text-emerald-400' : 'text-slate-500'" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11L4 7h8z"/></svg>
                  </div>
                </th>
                <th class="px-3 py-2.5 text-center font-bold cursor-pointer hover:bg-slate-700 select-none w-16" @click="setSort('polen')">
                  <div class="flex items-center justify-center gap-1">Polen
                    <svg class="w-3 h-3 shrink-0" :class="sortCol==='polen' ? 'text-emerald-400' : 'text-slate-500'" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11L4 7h8z"/></svg>
                  </div>
                </th>
                <th class="px-3 py-2.5 text-center font-bold cursor-pointer hover:bg-slate-700 select-none w-16" @click="setSort('disp')">
                  <div class="flex items-center justify-center gap-1">Disp.
                    <svg class="w-3 h-3 shrink-0" :class="sortCol==='disp' ? 'text-emerald-400' : 'text-slate-500'" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11L4 7h8z"/></svg>
                  </div>
                </th>
                <th class="px-3 py-2.5 text-center font-bold w-28">Acción</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(cell, idx) in paginatedMales" :key="cell.varB">
                <tr @click="toggleDetail(cell)"
                  :class="['border-b border-black/5 cursor-pointer transition-all duration-150', getRowBg(cell), expandedCell === cell ? 'outline outline-2 outline-emerald-400' : 'hover:brightness-95']">
                  <td class="px-3 py-2.5 font-bold" :class="getRowTextMain(cell)">
                    <div class="flex items-center gap-2">
                      <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black shrink-0" :class="isDarkRow(cell) ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'">
                        {{ (currentPage * PAGE_SIZE) + idx + 1 }}
                      </span>
                      {{ cell.varB }}
                    </div>
                  </td>
                  <td class="px-3 py-2.5 text-center font-bold" :class="getRowTextMain(cell)">{{ getDistanciaLocal(cell.varA, cell.varB) }}</td>
                  <td class="px-3 py-2.5 text-center font-semibold" :class="getRowTextSub(cell)">{{ cell.vm2 }}</td>
                  <td class="px-3 py-2.5 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full font-black text-[11px]" :class="getIcBadge(cell)">{{ getIcValue(cell) }}</span>
                  </td>
                  <td class="px-3 py-2.5 text-center font-semibold" :class="getRowTextSub(cell)">{{ cell.polen2 }}%</td>
                  <td class="px-3 py-2.5 text-center font-semibold" :class="getRowTextSub(cell)">{{ getDisp(cell.varB) }}</td>
                  <td class="px-3 py-2.5 text-center">
                    <button @click.stop="$emit('toggle-cross', cell)"
                      :class="['inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold border transition-all', cell.viabilidad ? 'bg-emerald-600 text-white border-emerald-700 shadow hover:bg-emerald-700' : 'bg-white/70 text-slate-600 border-slate-300 hover:bg-white hover:border-emerald-400']">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="cell.viabilidad" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      {{ cell.viabilidad ? 'Selec.' : 'Agregar' }}
                    </button>
                  </td>
                </tr>
                <tr v-if="expandedCell === cell" :class="[getRowBg(cell)]">
                  <td colspan="7" class="px-4 py-3 border-b-2 border-emerald-400">
                    <div class="bg-white/80 rounded-lg p-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs shadow-inner">
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Variedad Padre</span><span class="font-black text-slate-800 text-base">{{ cell.varB }}</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Dist. Genética</span><span class="font-black text-slate-800 text-lg">{{ getDistanciaLocal(cell.varA, cell.varB) }}</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">VM Macho</span><span class="font-black text-slate-800 text-lg">{{ cell.vm2 }}</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Índice Combinado</span><span class="font-black text-slate-800 text-lg">{{ getIcValue(cell) }}</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Polen</span><span class="font-black text-slate-800">{{ cell.polen2 }}%</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Disponibilidad</span><span class="font-black text-slate-800">{{ getDisp(cell.varB) }}</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Estado</span><span :class="cell.viabilidad ? 'text-emerald-700 font-black' : 'text-slate-500 font-semibold'">{{ cell.viabilidad ? '✓ Seleccionado' : 'No seleccionado' }}</span></div>
                      <div class="flex items-end">
                        <button @click.stop="$emit('toggle-cross', cell)" class="px-4 py-1.5 rounded-lg font-bold text-xs transition-all" :class="cell.viabilidad ? 'bg-rose-500 text-white hover:bg-rose-600' : 'bg-emerald-600 text-white hover:bg-emerald-700'">
                          {{ cell.viabilidad ? 'Quitar selección' : '+ Seleccionar' }}
                        </button>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
              <tr v-if="paginatedMales.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">No se encontraron machos compatibles bajo los filtros actuales.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
          <span class="text-xs text-slate-500">Mostrando {{ currentPage * PAGE_SIZE + 1 }}–{{ Math.min((currentPage + 1) * PAGE_SIZE, filteredSortedMales.length) }} de {{ filteredSortedMales.length }} padres</span>
          <div class="flex items-center gap-1">
            <button @click="currentPage = 0" :disabled="currentPage === 0" class="px-2 py-1 text-xs rounded border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed font-bold">«</button>
            <button @click="currentPage--" :disabled="currentPage === 0" class="px-2 py-1 text-xs rounded border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed font-bold">‹</button>
            <span class="px-3 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 rounded border border-emerald-200">{{ currentPage + 1 }} / {{ totalPages }}</span>
            <button @click="currentPage++" :disabled="currentPage >= totalPages - 1" class="px-2 py-1 text-xs rounded border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed font-bold">›</button>
            <button @click="currentPage = totalPages - 1" :disabled="currentPage >= totalPages - 1" class="px-2 py-1 text-xs rounded border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed font-bold">»</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

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

// Hembras
const searchFemaleQuery = ref('');
const showFemaleDropdown = ref(false);
const filteredFemales = computed(() => {
  if (!searchFemaleQuery.value) return props.females;
  const q = searchFemaleQuery.value.toLowerCase();
  return props.females.filter(row => (row?.[0]?.varA || '').toLowerCase().includes(q));
});
const selectFemaleFromDropdown = (row: any) => {
  emit('select-female', row);
  showFemaleDropdown.value = false;
  searchFemaleQuery.value = '';
};
const hideDropdownDelayed = () => setTimeout(() => { showFemaleDropdown.value = false; }, 200);

// Tabla machos
const PAGE_SIZE = 50;
const currentPage = ref(0);
const searchMaleQuery = ref('');
const sortCol = ref<'varB'|'dg'|'vm'|'ic'|'polen'|'disp'>('ic');
const sortAsc = ref(false);
const expandedCell = ref<any>(null);

watch(() => props.selectedFemaleRow, () => { currentPage.value = 0; expandedCell.value = null; });
watch(searchMaleQuery, () => { currentPage.value = 0; });

const getIcValue = (cell: any): number => {
  const ic = props.getIndiceCombinadoLocal(cell.varA, cell.varB, cell.vm2);
  return isNaN(ic) ? 0 : Math.round(ic);
};

const filteredSortedMales = computed(() => {
  let list = [...props.sortedMales];
  if (searchMaleQuery.value) {
    const q = searchMaleQuery.value.toLowerCase();
    list = list.filter(c => c.varB?.toLowerCase().includes(q));
  }
  list.sort((a, b) => {
    if (sortCol.value === 'varB') return sortAsc.value ? String(a.varB).localeCompare(String(b.varB)) : String(b.varB).localeCompare(String(a.varB));
    let vA = 0, vB = 0;
    if (sortCol.value === 'dg') { vA = Number(props.getDistanciaLocal(a.varA, a.varB)) || 0; vB = Number(props.getDistanciaLocal(b.varA, b.varB)) || 0; }
    else if (sortCol.value === 'vm') { vA = Number(a.vm2) || 0; vB = Number(b.vm2) || 0; }
    else if (sortCol.value === 'ic') { vA = getIcValue(a); vB = getIcValue(b); }
    else if (sortCol.value === 'polen') { vA = Number(a.polen2) || 0; vB = Number(b.polen2) || 0; }
    else if (sortCol.value === 'disp') { vA = Number(props.getDisp(a.varB)) || 0; vB = Number(props.getDisp(b.varB)) || 0; }
    return sortAsc.value ? vA - vB : vB - vA;
  });
  return list;
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredSortedMales.value.length / PAGE_SIZE)));
const paginatedMales = computed(() => filteredSortedMales.value.slice(currentPage.value * PAGE_SIZE, (currentPage.value + 1) * PAGE_SIZE));

const setSort = (col: typeof sortCol.value) => {
  if (sortCol.value === col) { sortAsc.value = !sortAsc.value; } else { sortCol.value = col; sortAsc.value = false; }
  currentPage.value = 0;
};
const toggleDetail = (cell: any) => { expandedCell.value = expandedCell.value === cell ? null : cell; };

// Colores heatmap
const isDarkRow = (cell: any): boolean => {
  if (props.tipoMapaCalor === 'none') return false;
  if (props.tipoMapaCalor === 'ic') { const ic = getIcValue(cell); return ic >= 50; }
  const dg = Number(props.getDistanciaLocal(cell.varA, cell.varB));
  return dg >= 0.65 || (!isNaN(dg) && dg < 0.35);
};
const getRowBg = (cell: any): string => {
  if (props.tipoMapaCalor === 'none') return cell.viabilidad ? 'bg-emerald-50' : 'bg-white';
  if (props.tipoMapaCalor === 'ic') {
    const ic = getIcValue(cell);
    if (ic >= 80) return 'bg-indigo-600'; if (ic >= 65) return 'bg-sky-400'; if (ic >= 50) return 'bg-emerald-500'; return 'bg-rose-400';
  }
  const dg = Number(props.getDistanciaLocal(cell.varA, cell.varB));
  if (isNaN(dg)) return 'bg-slate-50';
  if (dg >= 0.65) return 'bg-blue-600'; if (dg >= 0.55) return 'bg-sky-400'; if (dg >= 0.45) return 'bg-slate-200'; if (dg >= 0.35) return 'bg-amber-300'; return 'bg-orange-400';
};
const getRowTextMain = (cell: any) => isDarkRow(cell) ? 'text-white' : 'text-slate-800';
const getRowTextSub = (cell: any) => isDarkRow(cell) ? 'text-white/80' : 'text-slate-500';
const getIcBadge = (cell: any): string => {
  const ic = getIcValue(cell);
  if (ic >= 80) return 'bg-indigo-100 text-indigo-800'; if (ic >= 65) return 'bg-sky-100 text-sky-800'; if (ic >= 50) return 'bg-emerald-100 text-emerald-800'; return 'bg-rose-100 text-rose-800';
};
</script>

<style scoped>
.scrollbar-custom::-webkit-scrollbar { width: 8px; height: 8px; }
.scrollbar-custom::-webkit-scrollbar-thumb { background-color: #10b981; border-radius: 10px; }
.scrollbar-custom::-webkit-scrollbar-track { background-color: #f8fafc; }
</style>
