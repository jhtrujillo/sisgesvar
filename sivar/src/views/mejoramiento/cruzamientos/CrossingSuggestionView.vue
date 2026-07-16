<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-2 sm:px-4 pt-4">
    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-4">
      <div class="flex items-center justify-between mb-3">
        <h1 class="text-2xl font-extrabold text-slate-800 flex items-center">
          <div class="p-1.5 bg-emerald-50 text-cenicana rounded-lg mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
          </div>
          Consolidado de Cruzamientos
        </h1>
        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full">
          Sugerencias Programadas
        </span>
      </div>
      <div class="flex flex-wrap items-center justify-between ml-9 text-xs text-slate-500">
        <span>Confirme y seleccione las combinaciones recomendadas para generar la lista definitiva.</span>
        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 mt-1 sm:mt-0">
          Proyecto: {{ selectedCdCntble }}
        </span>
      </div>
    </div>

    <!-- Contenedor Principal -->
    <div class="bg-white border border-slate-100 rounded-xl p-3 sm:p-4 shadow-premium relative min-h-[250px]">
      
      <!-- Estado de Procesamiento / Cargando -->
      <div v-if="isLoading" class="absolute inset-0 bg-white/95 rounded-xl z-30 flex flex-col items-center justify-center space-y-4 transition-all duration-300">
        <div class="relative w-14 h-14">
          <!-- Círculo de base -->
          <div class="absolute inset-0 rounded-full border-4 border-emerald-50"></div>
          <!-- Círculo giratorio -->
          <div class="absolute inset-0 rounded-full border-4 border-t-cenicana animate-spin"></div>
        </div>
        <div class="flex flex-col items-center text-center px-4">
          <span class="text-sm font-bold text-slate-700 animate-pulse">Calculando las mejores sugerencias de cruzamientos...</span>
        </div>
      </div>

      <!-- Leyenda informativa y Botón de Filtro -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 border-b border-slate-100 pb-3">
        <div class="flex flex-wrap gap-3 text-[11px] font-semibold text-slate-500">
          <span class="flex items-center">
            <span class="w-3 h-3 bg-emerald-100 border border-emerald-200 rounded mr-1"></span>
            Recomendado
          </span>
          <span v-if="!ocultarInviables" class="flex items-center">
            <span class="w-3 h-3 bg-slate-100 border border-slate-200 rounded mr-1"></span>
            No Recomendado
          </span>
          <span class="flex items-center">
            <span class="font-bold text-slate-700 mr-0.5">VM:</span>
            Valor de Mérito
          </span>
          <span class="flex items-center">
            <span class="font-bold text-slate-700 mr-0.5">DG:</span>
            Distancia Genética
          </span>
        </div>

        <!-- Botón de Filtro Interactivo -->
        <div class="flex justify-end">
          <button 
            @click="ocultarInviables = !ocultarInviables"
            class="flex items-center px-3 py-1 text-[11px] font-bold rounded-lg transition-all duration-200 border"
            :class="!ocultarInviables ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm hover:bg-emerald-700' : 'bg-white border-slate-200 text-slate-660 hover:bg-slate-50'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ ocultarInviables ? 'Ver Inviables' : 'Ocultar Inviables' }}
          </button>
        </div>
      </div>

      <!-- Alerta si no hay ningún cruce viable en modo limpio -->
      <div v-if="ocultarInviables && flattenedViabilidades.length > 0 && !hasAnyViableCrossing" class="flex flex-col items-center justify-center py-10 text-center text-slate-400 space-y-2 bg-slate-50/50 rounded-xl border border-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="text-xs font-semibold text-slate-500">No se encontraron cruzamientos programados en la configuración actual.</span>
        <span class="text-[11px] text-slate-400">Haga clic en "Ver Inviables" para ver todas las combinaciones o modifique los pesos en el paso anterior.</span>
      </div>

      <!-- Cuadrícula Estilo Clásico/Compacto de Oro (Sin Barra Horizontal) -->
      <div v-else class="overflow-hidden border border-slate-100 rounded-xl shadow-sm">
        <div class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-custom">
          <table class="table-auto w-full divide-y divide-slate-150">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-r border-slate-100 sticky top-0 left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]">
                  Hembra / Macho
                </th>
                <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
                <template 
                  v-for="(flor, indexCol) in SuggestionCrossingStore.suggestionCrossingsFilter.flores || []"
                  :key="flor.id_pr"
                >
                  <th
                    v-if="!ocultarInviables || isColumnViable(indexCol)"
                    class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-650 bg-slate-50 border-r border-slate-100 sticky top-0 z-10 min-w-[75px]"
                  >
                    <span 
                      class="block text-slate-800 font-extrabold leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors"
                      @click="openVarietyProfile(flor.vrdad)"
                    >
                      {{ flor.vrdad }}
                    </span>
                    <span class="block text-[9px] text-slate-400 font-bold mt-0.5">Cantidad: {{ flor.numero }}</span>
                  </th>
                </template>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
              <template 
                v-for="(viabilidadRow, indexRow) in flattenedViabilidades || []" 
                :key="indexRow"
              >
                <tr 
                  v-if="!ocultarInviables || isRowViable(viabilidadRow)"
                  class="hover:bg-slate-50/40 transition-colors"
                >
                  <!-- Celda Madre Fija a la izquierda -->
                  <td class="whitespace-nowrap px-2 py-2 text-center text-[11px] font-bold text-slate-700 bg-white border-r border-slate-100 sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]">
                    <span class="block font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors" @click="openVarietyProfile(viabilidadRow[0]?.varA)">{{ viabilidadRow[0]?.varA || 'N/A' }}</span>
                    <span class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-100">
                      VM: {{ getRowVm(viabilidadRow) }}
                    </span>
                    <span class="block text-[9px] text-slate-400 mt-0.5 font-semibold">Polen: {{ viabilidadRow[0]?.polen || '0' }}</span>
                  </td>
                  
                  <!-- Celdas de la matriz filtradas por columna -->
                  <template 
                    v-for="(car, indexCol) in viabilidadRow" 
                    :key="indexCol"
                  >
                    <td 
                      v-if="!ocultarInviables || isColumnViable(indexCol)"
                      :class="[
                        car?.viabilidad 
                          ? 'bg-emerald-50/50 hover:bg-emerald-100/50 border-r border-emerald-100/50 text-emerald-800' 
                          : 'bg-slate-50/50 hover:bg-slate-100/50 border-r border-slate-100 text-slate-400 opacity-60'
                      ]"
                      class="p-1.5 text-center border-b border-slate-100 transition-all duration-200 min-w-[75px]"
                    >
                      <div 
                        class="flex flex-col items-center justify-center space-y-1"
                      >
                        <input 
                          type="checkbox" 
                          :checked="!!car?.viabilidad" 
                          @click="toggleCruzamiento(car)" 
                          :disabled="!car?.viabilidad"
                          class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-100 transition cursor-pointer"
                        />
                        <div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1 mt-1 space-y-1">
                          <span class="text-[9px] font-extrabold tracking-tight leading-none text-slate-700 text-center">
                            DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}
                          </span>
                          <!-- Botón Comparador Lado a Lado -->
                          <button 
                            @click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad)"
                            class="text-[8px] font-bold px-1.5 py-0.5 bg-slate-100 hover:bg-emerald-50 text-slate-650 hover:text-emerald-700 rounded border border-slate-200/60 hover:border-emerald-200 transition-all duration-150 flex items-center justify-center space-x-0.5 mx-auto"
                            title="Comparar Progenitores Lado a Lado"
                          >
                            <i class="fa fa-balance-scale"></i>
                            <span>VS</span>
                          </button>
                        </div>
                      </div>
                    </td>
                  </template>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Botones de Navegación -->
    <div class="flex justify-between pt-2">
      <router-link :to="{ name: 'crossing_weighted.show' }">
        <button
          type="button"
          class="flex items-center px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 transition-all duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Atrás
        </button>
      </router-link>

      <!-- Botón de Excel para desempeño -->
      <button
        @click="exportarDesempenoIndividual"
        class="flex items-center px-4 py-2 bg-white text-teal-700 border border-teal-200 rounded-xl shadow-sm hover:bg-teal-50 hover:border-teal-300 font-bold transition-all duration-200"
        title="Descargar Desempeño Individual de todas las Variedades"
      >
        <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Desempeño
      </button>

      <button
        @click="submitCruzamientos"
        class="flex items-center px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200"
      >
        Siguiente
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>
  <input type="hidden" v-model="selectedCdCntble" id="proyecto_id_modal" />
  <input type="hidden" v-model="selectedVariety" id="variedad" />
  <input type="hidden" v-model="selectedMegaAmbiente" id="ambiente" />

  <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
  <VarietyProfileDrawer
    v-model:isOpen="isDrawerOpen"
    :varietyName="selectedVarietyForDrawer"
  />

  <!-- Modal de Comparación Lado a Lado -->
  <ParentComparatorModal
    v-model:isOpen="isComparatorOpen"
    :motherName="comparatorMother"
    :fatherName="comparatorFather"
    :initiallyViable="comparatorInitiallyViable"
  />
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useSuggestionCrossingStore } from "@/stores/crossingsuggestion";
import { useParametizeWeightedCrossingStore } from "@/stores/parametizeweightedcrossing";
import ExcelJS from "exceljs";
import { useToast } from "vue-toastification";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import ParentComparatorModal from "@/components/ParentComparatorModal.vue";

interface Viabilidad {
  varA: string;
  varB: string;
  viabilidad: boolean;
  vm: string;
  vm2: string;
}

const SuggestionCrossingStore = useSuggestionCrossingStore();
const ParametizeWeightedStore = useParametizeWeightedCrossingStore();
const toast = useToast();
const router = useRouter();

const selectedVariety = ref("");
const selectedMegaAmbiente = ref("");
const selectedCdCntble = ref("");
const cruzamientos = ref(localStorage.getItem("cruzamientos") || "");
const ocultarInviables = ref(true); // Vista compacta limpia por defecto
const isLoading = ref(false); // Ref para spinner de carga

const getRowVm = (row: any[]) => {
  if (!row || row.length === 0) return "0";
  const validCell = row.find(cell => cell && cell.vm !== 1 && cell.vm !== "1" && cell.vm !== 0 && cell.vm !== "0");
  return validCell ? validCell.vm : (row[0]?.vm || "0");
};

// Estados para el Drawer de variedades
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const openVarietyProfile = (name: string) => {
  if (name && name !== "null" && name !== "?") {
    selectedVarietyForDrawer.value = name;
    isDrawerOpen.value = true;
  }
};

// Estados para el Comparador Lado a Lado
const isComparatorOpen = ref(false);
const comparatorMother = ref("");
const comparatorFather = ref("");
const comparatorInitiallyViable = ref(true);

const openParentComparator = (mother: string, father: string, viable: boolean) => {
  if (mother && father) {
    comparatorMother.value = mother;
    comparatorFather.value = father;
    comparatorInitiallyViable.value = viable;
    isComparatorOpen.value = true;
  }
};

/**
 * Modificación realizada por: Jhon Henry Trujillo PhD
 * Fecha: 2026-05-16
 * Propósito: Aplanar la matriz de viabilidades (3D a 2D) para que Vue pueda iterarla
 * correctamente en la plantilla (v-for) sin errores de renderizado.
 */
const flattenedViabilidades = computed(() => {
  const viabilidades = SuggestionCrossingStore.suggestionCrossingsFilter.viabilidades || [];
  return viabilidades.flat();
});

// Helper para verificar si un índice de columna (Padre) tiene al menos un cruce viable
const isColumnViable = (indexCol: number) => {
  const rows = flattenedViabilidades.value || [];
  return rows.some((row: any) => row[indexCol]?.viabilidad);
};

// Helper para verificar si una fila (Madre) tiene al menos un cruce viable
const isRowViable = (viabilidadRow: any[]) => {
  return viabilidadRow.some((car: any) => car?.viabilidad);
};

// Helper reactivo para verificar si hay al menos UN cruce viable en toda la matriz
const hasAnyViableCrossing = computed(() => {
  const rows = flattenedViabilidades.value || [];
  return rows.some((row: any) => row.some((car: any) => car?.viabilidad));
});

// Watcher para poblar los cruzamientos recomendados inicialmente
watch(flattenedViabilidades, (newVal) => {
  let initialCrossings = "";
  newVal.forEach((row: any) => {
    row.forEach((car: any) => {
      if (car && car.viabilidad) {
        const valor = `${car.varA}++${car.varB}++${car.viabilidad}++${getDistancia(car.varA, car.varB)}++${car.vm}++${car.vm2}`;
        if (!initialCrossings.includes(valor)) {
          initialCrossings += `---${valor}`;
        }
      }
    });
  });
  cruzamientos.value = initialCrossings;
  localStorage.setItem("cruzamientos", initialCrossings);
});

// Cargar datos del localStorage al montar el componente
onMounted(() => {
  const storedVariety = localStorage.getItem("selectedVariety");
  if (storedVariety) {
    selectedVariety.value = storedVariety;
  }

  const storedMegaAmbiente = localStorage.getItem("selectedMegaAmbiente");
  if (storedMegaAmbiente) {
    selectedMegaAmbiente.value = storedMegaAmbiente;
  }

  const storedCdCntble = localStorage.getItem("selectedCdCntble");
  if (storedCdCntble) {
    selectedCdCntble.value = storedCdCntble;
  }
});

// Watch para recargar los datos al cambiar los filtros con indicador de carga
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  if (newMegaAmbiente && newCdCntble && newVariety) {
    isLoading.value = true;
    try {
      await SuggestionCrossingStore.getSuggestionCrossingList(newCdCntble, newCdCntble, newVariety, newMegaAmbiente);
    } catch (error) {
      console.error("Error al cargar cruzamientos recomendados:", error);
    } finally {
      isLoading.value = false;
    }
  }
});

// Función para obtener la distancia entre dos variables
const getDistancia = (varA: string, varB: string) => {
  const distancias = SuggestionCrossingStore.suggestionCrossingsFilter.distancias || {};
  return distancias[varA]?.[varB] || "NA";
};

// Función para alternar el cruzamiento
const toggleCruzamiento = (car: Viabilidad) => {
  const valor = `${car.varA}++${car.varB}++${car.viabilidad}++${getDistancia(car.varA, car.varB)}++${car.vm}++${car.vm2}`;
  if (!cruzamientos.value.includes(valor)) {
    cruzamientos.value += `---${valor}`;
  } else {
    cruzamientos.value = cruzamientos.value.replace(`---${valor}`, "");
  }
  localStorage.setItem("cruzamientos", cruzamientos.value);
};

/**
 * Modificación realizada por: Jhon Henry Trujillo PhD
 * Fecha: 2026-05-16
 * Propósito: Validar que se haya seleccionado al menos un cruzamiento antes de permitir
 * al usuario avanzar al siguiente paso del proceso, previniendo errores de datos incompletos.
 */

async function exportarDesempenoIndividual() {
  try {
    const filterData = SuggestionCrossingStore.suggestionCrossingsFilter || {};
    const viabilidad = (filterData.viabilidades && filterData.viabilidades.length > 0) ? filterData.viabilidades : [];
    
    // Cargar ponderados si no están listos
    if (!ParametizeWeightedStore.parametizeWeightedCrossingFilter || !ParametizeWeightedStore.parametizeWeightedCrossingFilter.ponderados) {
      if (selectedCdCntble.value) {
        await ParametizeWeightedStore.getParametizeWeightedCrossingList(selectedCdCntble.value, selectedMegaAmbiente.value || "Semiseco");
      }
    }
    const ponderados = ParametizeWeightedStore.parametizeWeightedCrossingFilter?.ponderados || [];
    
    const floresBG = filterData.flores_bg || [];
    const floresPR = filterData.flores_pr || [];
    const floresEIII = filterData.flores_eiii || [];
    const testigoLimpio = filterData.testigo_limpio || {};

    const findVarietyData = (varName) => {
      if (!varName) return undefined;
      const cleanVar = varName.trim().toUpperCase();
      let data = floresBG.find((f) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      if (!data) data = floresPR.find((f) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      if (!data) data = floresEIII.find((f) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      return data;
    };
    
    const headers = [
      "Variedad Evaluada",
      "Rol (Madre/Padre)",
      "Característica Evaluada",
      "Valor Bruto (Variedad)",
      "Valor Testigo",
      "Porcentaje vs Testigo (%)",
      "Rango Evaluado (Regla Sistema)",
      "Nivel Obtenido",
      "Límite Proyecto",
      "¿Cumple Límite Individual?"
    ];

    const headersVM = [
      "Variedad Evaluada",
      "Rol (Madre/Padre)",
      "Característica",
      "Nivel Obtenido",
      "Porcentaje (%)",
      "Aporte al VM (Nivel × % / 100)"
    ];

    const rows = [];
    const rowsVM = [];
    const procesadas = new Set();

    if (viabilidad.length > 0) {
      viabilidad.forEach((row) => {
        if (row && row.length > 0) {
          row.forEach((cell) => {
            if (cell) {
              [
                { varName: cell.varA, rol: "Madre", florData: findVarietyData(cell.varA) },
                { varName: cell.varB, rol: "Padre", florData: findVarietyData(cell.varB) }
              ].forEach(({ varName, rol, florData }) => {
                if (varName && varName !== selectedVariety.value && !procesadas.has(varName) && florData) {
                  procesadas.add(varName);
                  let totalVM = 0;
                  
                  ponderados.forEach((p) => {
                    const caracteristica = p.equivalente ? p.equivalente.toLowerCase() : (p.caracteristica || p.nombre || "UNKNOWN");
                    const nombreCaract = p.nombre || p.caracteristica || caracteristica;
                    if (caracteristica) {
                      const valorReal = florData[caracteristica] ?? "-";
                      const valorTestigo = testigoLimpio[caracteristica] ?? "-";
                      const limiteMax = p.nivel !== null && p.nivel !== undefined ? Number(p.nivel) : "-";
                      const porcentajePeso = p.ponderado ? Number(p.ponderado) : 0;
                      
                      let porcentaje = "-";
                      const isEnfermedad = caracteristica === "msco_r" || caracteristica === "carbon" || caracteristica === "rya_cfe_r" || caracteristica === "roya_naranja";
                      
                      if (isEnfermedad) {
                        porcentaje = "N/A (Evaluación directa)";
                      } else if (valorReal !== "-" && valorTestigo !== "-" && valorReal !== null && valorTestigo !== null && Number(valorTestigo) > 0) {
                        porcentaje = ((Number(valorReal) * 100) / Number(valorTestigo)).toFixed(2) + "%";
                      }
  
                      let nivel = "-";
                      let rangoRegla = "N/A";
                      let cumpleLimite = "-";
                      let aporteVM = "-";
                      
                      if (valorReal !== "-" && valorReal !== null && valorReal !== "") {
                        let lvl = 999;
                        
                        if (caracteristica === "msco_r" || caracteristica === "carbon") {
                          const val = Number(valorReal);
                          rangoRegla = "N1: <=2% | N2: 2.1-3% | N3: 3.1-5% | N4: 5.1-8% | N5: 8.1-11% | N6: 11.1-15% | N7: 15.1-22% | N8: 22.1-30% | N9: >30%";
                          if (val <= 2) lvl = 1;
                          else if (val > 2 && val <= 3) lvl = 2;
                          else if (val > 3 && val <= 5) lvl = 3;
                          else if (val > 5 && val <= 8) lvl = 4;
                          else if (val > 8 && val <= 11) lvl = 5;
                          else if (val > 11 && val <= 15) lvl = 6;
                          else if (val > 15 && val <= 22) lvl = 7;
                          else if (val > 22 && val <= 30) lvl = 8;
                          else lvl = 9;
                        } else if (caracteristica === "tchm") {
                          rangoRegla = "N1: >120% | N2: 110-120% | N3: 95-109.9% | N4: 85-94.9% | N5: <85%";
                          if (valorTestigo !== "-" && valorTestigo !== null && Number(valorTestigo) > 0) {
                            const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                            if (pct > 120) lvl = 1;
                            else if (pct <= 120 && pct >= 110) lvl = 2;
                            else if (pct < 110 && pct >= 95) lvl = 3;
                            else if (pct < 95 && pct >= 85) lvl = 4;
                            else lvl = 5;
                          } else {
                            rangoRegla += " (Falta testigo)";
                          }
                        } else if (caracteristica === "scrsa" || caracteristica === "dmtro_tllo" || caracteristica === "altura_planta" || caracteristica === "poblacion") {
                          rangoRegla = "N1: >120% | N2: 100-120% | N3: 90-99.9% | N4: 80-89.9% | N5: <80%";
                          if (valorTestigo !== "-" && valorTestigo !== null && Number(valorTestigo) > 0) {
                            const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                            if (pct > 120) lvl = 1;
                            else if (pct <= 120 && pct >= 100) lvl = 2;
                            else if (pct < 100 && pct >= 90) lvl = 3;
                            else if (pct < 90 && pct >= 80) lvl = 4;
                            else lvl = 5;
                          } else {
                            rangoRegla += " (Falta testigo)";
                          }
                        } else if (caracteristica === "volcamiento") {
                          rangoRegla = "N1: <10% | N2: 10-19.9% | N3: 20-29.9% | N4: 30-48.9% | N5: >=49% (Menor es mejor)";
                          if (valorTestigo !== "-" && valorTestigo !== null && Number(valorTestigo) > 0) {
                            const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                            if (pct < 10) lvl = 1;
                            else if (pct < 20 && pct >= 10) lvl = 2;
                            else if (pct < 30 && pct >= 20) lvl = 3;
                            else if (pct < 49 && pct >= 30) lvl = 4;
                            else lvl = 5;
                          } else {
                            rangoRegla += " (Falta testigo)";
                          }
                        } else if (caracteristica === "rya_cfe_r" || caracteristica === "roya_naranja") {
                          rangoRegla = "Escala visual directa (1 al 9)";
                          lvl = Number(valorReal);
                        } else {
                          rangoRegla = "Sin regla configurada";
                        }
                        
                        if (lvl !== 999) {
                          nivel = lvl.toString();
                          if (limiteMax !== "-") {
                            cumpleLimite = lvl <= Number(limiteMax) ? "SÍ" : "NO";
                          }
                          const ap = (lvl * porcentajePeso) / 100;
                          aporteVM = ap.toFixed(2);
                          totalVM += ap;
                        }
                      } else {
                         rangoRegla = "Sin datos (Nivel 0 automático)";
                         nivel = "0";
                         aporteVM = "0.00";
                      }
                      
                      rows.push([
                        varName,
                        rol,
                        caracteristica.toUpperCase(),
                        valorReal,
                        valorTestigo,
                        porcentaje,
                        rangoRegla,
                        nivel,
                        limiteMax,
                        cumpleLimite
                      ]);
                      
                      if (porcentajePeso > 0) {
                        rowsVM.push([
                          varName,
                          rol,
                          nombreCaract.toUpperCase(),
                          nivel,
                          porcentajePeso.toFixed(2) + "%",
                          Number(aporteVM)
                        ]);
                      }
                    }
                  });
                  
                  if (rowsVM.length > 0 && rowsVM[rowsVM.length - 1][0] === varName) {
                    rowsVM.push([
                      varName,
                      rol,
                      "TOTAL VM",
                      "-",
                      "100%",
                      Number(totalVM.toFixed(2))
                    ]);
                    // Add empty row for spacing
                    rowsVM.push(["", "", "", "", "", ""]);
                  }
                }
              });
            }
          });
        }
      });
    }

    if (rows.length === 0) {
      toast.warning("No hay datos suficientes para exportar.");
      return;
    }

    const workbook = new ExcelJS.Workbook();
    
    // ----- HOJA 1: Desempeño -----
    const sheet = workbook.addWorksheet("Desempeño Individual");

    const headerContent = [
      ["CENICAÑA - MEMORIA DE DESEMPEÑO INDIVIDUAL (GLOBAL)"],
      ["Proyecto ID (Reglas tomadas):", selectedCdCntble.value || "General"],
      ["Mega Ambiente:", selectedMegaAmbiente.value || "N/A"],
      ["Testigo de Referencia:", selectedVariety.value || "N/A"],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      headers,
      ...rows
    ];

    headerContent.forEach((r) => sheet.addRow(r));

    sheet.mergeCells("A1:I1");
    const titleCell = sheet.getCell("A1");
    titleCell.font = { size: 16, bold: true, color: { argb: "FF0B4A2F" } };
    titleCell.alignment = { horizontal: "center" };

    const headerRow = sheet.getRow(7);
    headerRow.font = { bold: true, color: { argb: "FFFFFFFF" } };
    headerRow.eachCell((cell) => {
      cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF0E7490" } };
      cell.border = { top: { style: "thin" }, left: { style: "thin" }, bottom: { style: "thin" }, right: { style: "thin" } };
    });
    
    // ----- HOJA 2: Valores de Mérito -----
    const sheetVM = workbook.addWorksheet("Cálculo Valores de Mérito");
    const headerVMContent = [
      ["CENICAÑA - CÁLCULO DEL VALOR DE MÉRITO (VM)"],
      ["Proyecto ID (Pesos tomados):", selectedCdCntble.value || "General"],
      ["Mega Ambiente:", selectedMegaAmbiente.value || "N/A"],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      headersVM,
      ...rowsVM
    ];
    
    headerVMContent.forEach((r) => sheetVM.addRow(r));
    
    sheetVM.mergeCells("A1:E1");
    const titleCellVM = sheetVM.getCell("A1");
    titleCellVM.font = { size: 16, bold: true, color: { argb: "FF0B4A2F" } };
    titleCellVM.alignment = { horizontal: "center" };

    const headerRowVM = sheetVM.getRow(6);
    headerRowVM.font = { bold: true, color: { argb: "FFFFFFFF" } };
    headerRowVM.eachCell((cell) => {
      cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF0E7490" } };
      cell.border = { top: { style: "thin" }, left: { style: "thin" }, bottom: { style: "thin" }, right: { style: "thin" } };
    });
    
    // Colorear las filas de "TOTAL VM"
    sheetVM.eachRow((row, rowNumber) => {
      if (rowNumber > 6) {
        const colC = row.getCell(3).value;
        if (colC === "TOTAL VM") {
          row.font = { bold: true, color: { argb: "FF0B4A2F" } };
          row.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFD1FAE5" } }; // Verde muy claro
        }
      }
    });

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `Desempeno_y_ValoresMerito_Cruzamientos_${new Date().toISOString().split("T")[0]}.xlsx`;
    link.click();
    toast.success("Memoria de cálculos exportada correctamente.");
  } catch (error) {
    console.error("Error exportando desempeño individual:", error);
    toast.error("Ocurrió un error al exportar.");
  }
}

const submitCruzamientos = () => {
  if (cruzamientos.value) {
    router.push({ name: "crossing_suggestion_per_project.show" });
  } else {
    toast.error("Selecciona al menos un cruzamiento");
  }
};
</script>

<style>
/* Estilos personalizados para la barra de desplazamiento */
.scrollbar-custom::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background-color: #10b981; /* Esmeralda */
  border-radius: 10px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background-color: #f8fafc; /* Slate 50 */
}
</style>
