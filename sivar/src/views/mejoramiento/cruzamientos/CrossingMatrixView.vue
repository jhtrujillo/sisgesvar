<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-2 sm:px-4 pt-4">
    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-4">
      <div class="flex items-center justify-between mb-3">
        <h1 class="text-2xl font-extrabold text-slate-800 flex items-center">
          <div class="p-1.5 bg-emerald-50 text-cenicana rounded-lg mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </div>
          Programación de Cruzamientos
        </h1>
        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full">
          Paso 3 de 3: Matriz de Cruzamientos
        </span>
      </div>
      <div class="flex flex-wrap items-center justify-between ml-9 text-xs text-slate-500">
        <span>Seleccione las combinaciones viables en la cuadrícula para programar la polinización.</span>
        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 mt-1 sm:mt-0">
          Proyecto: {{ selectedCdCntble }}
        </span>
      </div>
    </div>

    <!-- Contenedor de la Matriz (Fluido y Balanceado sin Scrollbar Horizontal) -->
    <div class="bg-white border border-slate-100 rounded-xl p-3 sm:p-4 shadow-premium relative min-h-[250px]">
      <!-- Estado de Procesamiento / Cargando -->
      <div v-if="isLoading" class="absolute inset-0 bg-white/95 rounded-xl z-30 flex flex-col items-center justify-center space-y-4 transition-all duration-300">
        <div class="relative w-14 h-14">
          <!-- Círculo de base -->
          <div class="absolute inset-0 rounded-full border-4 border-emerald-50"></div>
          <!-- Círculo giratorio -->
          <div class="absolute inset-0 rounded-full border-4 border-t-cenicana animate-spin"></div>
        </div>
        <div class="flex flex-col items-center text-center px-4 max-w-md">
          <span class="text-sm font-bold text-slate-750 animate-pulse leading-relaxed">
            Cruzando progenitores, calculando valores de mérito, calculando viabilidad y calculando matriz de distancia genética...
          </span>
        </div>
      </div>

      <!-- Leyenda informativa y Botón de Filtro -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 border-b border-slate-100 pb-3">
        <div class="flex flex-wrap gap-3 text-[11px] font-semibold text-slate-500">
          <span class="flex items-center">
            <span class="w-3 h-3 bg-blue-100 border border-blue-200 rounded mr-1"></span>
            Cruce Viable / Seguro
          </span>
          <span v-if="!ocultarInviables" class="flex items-center">
            <span class="w-3 h-3 bg-slate-100 border border-slate-200 rounded mr-1"></span>
            Cruce Inviable / Veto
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
            :class="!ocultarInviables ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm hover:bg-emerald-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
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
      <div v-if="ocultarInviables && MatrixCrossingStore.matrixCrossingsFilter.viabilidad && MatrixCrossingStore.matrixCrossingsFilter.viabilidad.length > 0 && !hasAnyViableCrossing" class="flex flex-col items-center justify-center py-10 text-center text-slate-400 space-y-2 bg-slate-50/50 rounded-xl border border-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="text-xs font-semibold text-slate-500">No se encontraron cruzamientos viables en la configuración actual.</span>
        <span class="text-[11px] text-slate-400">Haga clic en "Ver Inviables" para ver todas las combinaciones o modifique los pesos en el paso anterior.</span>
      </div>

      <!-- Cuadrícula -->
      <div v-else class="overflow-hidden border border-slate-100 rounded-xl shadow-sm">
        <div class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-custom">
          <table class="table-auto w-full divide-y divide-slate-150">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-r border-slate-100 sticky top-0 left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]">
                  PARENTALES
                </th>
                <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
                <template 
                  v-for="(flor, indexCol) in MatrixCrossingStore.matrixCrossingsFilter.flores || []"
                  :key="flor.vrdad"
                >
                  <th
                    v-if="!ocultarInviables || isColumnViable(indexCol)"
                    class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-650 bg-slate-50 border-r border-slate-100 sticky top-0 z-10 min-w-[75px]"
                  >
                    <span class="block font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors" @click="openVarietyProfile(flor.vrdad)">
                      {{ flor.vrdad }}
                    </span>
                    <span class="block text-[9px] text-slate-400 font-bold mt-0.5 mb-0.5">Polen: {{ flor.polen }}</span>
                    <span 
                      v-if="MatrixCrossingStore.matrixCrossingsFilter.viabilidad?.[0]?.[indexCol]?.vm2 !== undefined" 
                      class="inline-block px-1.5 py-0.5 rounded border text-[9px] font-extrabold"
                      :class="Number(MatrixCrossingStore.matrixCrossingsFilter.viabilidad[0][indexCol].vm2) > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-500 border-slate-200'"
                    >
                      VM: {{ MatrixCrossingStore.matrixCrossingsFilter.viabilidad[0][indexCol].vm2 }}
                    </span>
                  </th>
                </template>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
              <template 
                v-for="(viabilidadRow, indexRow) in MatrixCrossingStore.matrixCrossingsFilter.viabilidad || []" 
                :key="indexRow"
              >
                <tr 
                  v-if="!ocultarInviables || isRowViable(viabilidadRow)"
                  class="hover:bg-slate-50/40 transition-colors"
                >
                  <!-- Celda Madre Fija a la izquierda -->
                  <td class="whitespace-nowrap px-2 py-2 text-center text-[11px] font-bold text-slate-700 bg-white border-r border-slate-100 sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]">
                    <span class="block font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors" @click="openVarietyProfile(viabilidadRow[0].varA)">{{ viabilidadRow[0].varA }}</span>
                    <span class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-100">
                      VM: {{ getRowVm(viabilidadRow) }}
                    </span>
                    <span class="block text-[9px] text-slate-400 mt-0.5 font-semibold">Polen: {{ viabilidadRow[0].polen }}</span>
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
                          ? 'bg-blue-50/50 hover:bg-blue-100/50 border-r border-blue-100/50 text-blue-800' 
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
                          class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-100 transition cursor-pointer"
                        />
                        <div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1.5 mt-1 space-y-1">
                          <span class="text-[9px] font-extrabold tracking-tight leading-none text-slate-700 text-center">
                            DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}
                          </span>
                          <!-- Botón Comparador Lado a Lado -->
                          <button 
                            @click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad)"
                            class="text-[8px] font-bold px-1.5 py-0.5 bg-slate-100 hover:bg-emerald-50 text-slate-650 hover:text-emerald-700 rounded border border-slate-200/60 hover:border-emerald-200 transition-all duration-150 flex items-center justify-center space-x-0.5"
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
      <button
        @click="submitCruzamientos"
        class="flex items-center px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200"
      >
        Programar Cruzamientos
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
import { useMatrixCrossingStore } from "@/stores/crossingmatrix";
import { useToast } from "vue-toastification";
import type { CruzamientoSeleccionado } from "@/services/types";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import ParentComparatorModal from "@/components/ParentComparatorModal.vue";

const MatrixCrossingStore = useMatrixCrossingStore();
const toast = useToast();
const router = useRouter();

const selectedVariety = ref("");
const selectedMegaAmbiente = ref("");
const selectedCdCntble = ref("");
const ocultarInviables = ref(true); // Vista compacta limpia por defecto
const isLoading = ref(false);

// Refs for VarietyProfileDrawer
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const getRowVm = (row: any[]) => {
  if (!row || row.length === 0) return "0";
  const validCell = row.find(cell => cell && cell.vm !== 1 && cell.vm !== "1" && cell.vm !== 0 && cell.vm !== "0");
  return validCell ? validCell.vm : (row[0]?.vm || "0");
};

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

  // Agregar cruzamientos seleccionados que ya están en true
  MatrixCrossingStore.matrixCrossingsFilter.viabilidad?.forEach((viabilidadRow) => {
    viabilidadRow.forEach((car: any) => {
      if (car?.viabilidad) {
        addCruzamientoSeleccionado(car);
      }
    });
  });
});

// Watch para recargar los datos al cambiar los filtros con indicador de carga
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  if (newMegaAmbiente && newCdCntble && newVariety) {
    isLoading.value = true;
    try {
      await MatrixCrossingStore.getMatrixCrossingList(newCdCntble, newCdCntble, newVariety);
      
      // Agregar cruzamientos seleccionados que ya están en true tras cargar los datos
      MatrixCrossingStore.matrixCrossingsFilter.viabilidad?.forEach((viabilidadRow) => {
        viabilidadRow.forEach((car: any) => {
          if (car?.viabilidad) {
            addCruzamientoSeleccionado(car);
          }
        });
      });
    } catch (error) {
      console.error("Error al cargar la matriz de cruzamientos:", error);
      toast.error("Error al calcular la matriz de cruzamientos");
    } finally {
      isLoading.value = false;
    }
  }
});

// Helper reactivo para verificar si hay al menos UN cruce viable en toda la matriz
const hasAnyViableCrossing = computed(() => {
  const viabilidad = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  return viabilidad.some((row: any) => row.some((car: any) => car?.viabilidad));
});

// Auto-desactivar "Ocultar Inviables" si no hay ninguna combinación viable
watch(hasAnyViableCrossing, (newVal) => {
  if (newVal === false) {
    ocultarInviables.value = false;
  }
}, { immediate: true });

// Helper para verificar si un índice de columna (Padre) tiene al menos un cruce viable
const isColumnViable = (indexCol: number) => {
  const viabilidad = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  return viabilidad.some((row: any) => row[indexCol]?.viabilidad);
};

// Helper para verificar si una fila (Madre) tiene al menos un cruce viable
const isRowViable = (viabilidadRow: any[]) => {
  return viabilidadRow.some((car: any) => car?.viabilidad);
};

// Función para obtener la distancia entre dos variables
const getDistancia = (varA: string, varB: string) => {
  const distancias = MatrixCrossingStore.matrixCrossingsFilter.distancias || {};
  return distancias[varA]?.[varB] || "NA";
};

// Función para agregar un cruzamiento al array si ya está seleccionado
const addCruzamientoSeleccionado = (car: CruzamientoSeleccionado) => {
  const cruzamientoSeleccionado = {
    varA: car?.varA || "N/A",
    varB: car?.varB || "N/A",
    viabilidad: car?.viabilidad !== undefined ? car.viabilidad : false,
    distancia: getDistancia(car?.varA, car?.varB) || "NA",
    vm: car?.vm || "0",
    vm2: car?.vm2 || "0"
  };

  // Verificar si el cruzamiento ya está en la lista
  const index = MatrixCrossingStore.cruzamientosSeleccionados.findIndex(
    (c) => c.varA === cruzamientoSeleccionado.varA && c.varB === cruzamientoSeleccionado.varB
  );

  if (index === -1) {
    // Si no está, agregarlo
    MatrixCrossingStore.cruzamientosSeleccionados.push(cruzamientoSeleccionado);
  }
};

// Función para alternar el cruzamiento cuando se hace click
const toggleCruzamiento = (car: CruzamientoSeleccionado) => {
  const cruzamientoSeleccionado = {
    varA: car?.varA || "N/A",
    varB: car?.varB || "N/A",
    viabilidad: car?.viabilidad !== undefined ? car.viabilidad : false,
    distancia: getDistancia(car?.varA, car?.varB) || "NA",
    vm: car?.vm || "0",
    vm2: car?.vm2 || "0"
  };

  // Verificar si el cruzamiento ya está en la lista
  const index = MatrixCrossingStore.cruzamientosSeleccionados.findIndex(
    (c) => c.varA === cruzamientoSeleccionado.varA && c.varB === cruzamientoSeleccionado.varB
  );

  if (index === -1) {
    // Si no está en la lista, agregarlo
    MatrixCrossingStore.cruzamientosSeleccionados.push(cruzamientoSeleccionado);
  } else {
    // Si ya está en la lista, removerlo (cuando se deselecciona)
    MatrixCrossingStore.cruzamientosSeleccionados.splice(index, 1);
  }

  // Mostrar los cruzamientos seleccionados después de cada interacción
  console.log("Cruzamientos seleccionados actualmente:", MatrixCrossingStore.cruzamientosSeleccionados);
};

// Función para enviar los cruzamientos y pasar al siguiente paso
const submitCruzamientos = () => {
  router.push({ name: "crossing_suggestion_per_project.show" });
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
