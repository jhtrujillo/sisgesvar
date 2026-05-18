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
                      VM: {{ viabilidadRow[0]?.vm || '0' }}
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
                          : (ocultarInviables ? 'bg-transparent border-r border-slate-100/40' : 'bg-slate-50/50 hover:bg-slate-100/50 border-r border-slate-100 text-slate-400 opacity-60')
                      ]"
                      class="p-1.5 text-center border-b border-slate-100 transition-all duration-200 min-w-[75px]"
                    >
                      <div 
                        v-show="car?.viabilidad || !ocultarInviables"
                        class="flex flex-col items-center justify-center space-y-1"
                      >
                        <input 
                          type="checkbox" 
                          :checked="!!car?.viabilidad" 
                          @click="toggleCruzamiento(car)" 
                          :disabled="!car?.viabilidad"
                          class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-100 transition cursor-pointer"
                        />
                        <span class="text-[9px] font-extrabold tracking-tight mt-0.5 leading-none text-slate-700">
                          DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}
                        </span>
                        <!-- Botón Comparador Lado a Lado -->
                        <button 
                          @click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad)"
                          class="text-[8px] font-black px-1.5 py-0.5 bg-slate-50 hover:bg-emerald-50 text-slate-500 hover:text-emerald-700 rounded border border-slate-200/65 hover:border-emerald-200 transition-all duration-150 mt-1"
                          title="Comparar Progenitores Lado a Lado"
                        >
                          Comparar
                        </button>
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
const toast = useToast();
const router = useRouter();

const selectedVariety = ref("");
const selectedMegaAmbiente = ref("");
const selectedCdCntble = ref("");
const cruzamientos = ref(localStorage.getItem("cruzamientos") || "");
const ocultarInviables = ref(true); // Vista compacta limpia por defecto
const isLoading = ref(false); // Ref para spinner de carga

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
