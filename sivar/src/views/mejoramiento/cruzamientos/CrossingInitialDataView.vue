<template>
  <div class="space-y-8 w-full max-w-4xl mx-auto px-4 pt-6">
    <!-- Botón Volver -->
    <BackButton :to="{ name: 'cruzamientos.show' }" label="Volver" />

    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
          <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
          </div>
          Programación de Cruzamientos
        </h1>
        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full"> Paso 1 de 3: Datos Iniciales </span>
      </div>
      <p class="text-slate-500 ml-11 text-sm">Seleccione el proyecto de mejoramiento activo para cargar las flores disponibles.</p>
    </div>

    <!-- Contenedor del Formulario -->
    <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-premium relative min-h-[200px]">
      <!-- Overlay Loading State -->
      <div
        v-if="isLoading"
        class="absolute inset-0 z-50 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300 rounded-2xl"
      >
        <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center gap-3 border border-emerald-100">
          <svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <span class="text-emerald-800 font-bold tracking-wide animate-pulse text-xs">Cargando proyectos con flores...</span>
        </div>
      </div>

      <div class="space-y-6">
        <div>
          <label class="block uppercase tracking-wider text-slate-600 text-xs font-bold mb-3" for="grid-state"> Proyectos con flores activas </label>
          <div class="relative">
            <ComboBoxMultiple
              :data-list="mappedCrossingInitialData"
              :column-value="columnValueCrossingInitialData"
              :column-to-show="'combinedValue'"
              v-model:selectedData="model.cd_cntble"
              placeholder="Seleccione un proyecto..."
            />
          </div>
        </div>

        <!-- Tabla de Detalle del Proyecto -->
        <div v-if="filteredProject" class="mt-8 space-y-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Detalle del Proyecto Seleccionado</h3>
          <div class="overflow-hidden border border-slate-100 rounded-xl shadow-sm">
            <table class="table-auto w-full divide-y divide-slate-100">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Código</th>
                  <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Nombre del Proyecto</th>
                  <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">Cantidad</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr class="hover:bg-emerald-50/30 transition-colors duration-150">
                  <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-700">{{ filteredProject.cd_cntble }}</td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 font-medium">{{ filteredProject.nm_prycto }}</td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm text-right font-semibold text-emerald-600">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-100">
                      {{ filteredProject.numero }} flores
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón de Navegación -->
    <div class="flex justify-end pt-4">
      <BaseButton variant="primary" size="md" :to="{ name: 'crossing_weighted.show' }" :disabled="!model.cd_cntble">
        Siguiente paso
        <template #icon-right>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </template>
      </BaseButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, reactive, computed } from "vue";
import { useCrossingInitialDataStore } from "@/stores/crossinginitialdata";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import type { CrossingInitialData } from "@/services/types";
import BackButton from "@/components/BackButton.vue";

// Stores y variables
const crossingInitialDataStore = useCrossingInitialDataStore();
const crossingInitialData = ref<CrossingInitialData[]>([]);
const mappedCrossingInitialData = ref<CrossingInitialData[]>([]);
const model = reactive({ cd_cntble: "" } as Partial<CrossingInitialData>); // Modelo reactivo
const isLoading = ref(false);

// Configuración para ComboBoxMultiple
const columnValueCrossingInitialData = "cd_cntble";

// Cargar los datos iniciales cuando el componente esté montado
onMounted(async () => {
  isLoading.value = true;
  await crossingInitialDataStore.getCrossingInitialDataList();
  crossingInitialData.value = crossingInitialDataStore.crossingInitialDataList;

  // Mapeo para concatenar los valores de 'cd_cntble' y 'nm_prycto'
  mappedCrossingInitialData.value = crossingInitialData.value.map((item) => ({
    ...item,
    combinedValue: `${item.cd_cntble} - ${item.nm_prycto}`
  }));

  // Cargar el valor almacenado en local storage (si existe)
  const storedCdCntble = localStorage.getItem("selectedCdCntble");
  if (storedCdCntble) {
    model.cd_cntble = storedCdCntble;
  }
  isLoading.value = false;
});

// Computed para filtrar el proyecto seleccionado
const filteredProject = computed(() => {
  const project = crossingInitialData.value.find((project) => project.cd_cntble === model.cd_cntble) || null;
  if (project) {
    localStorage.setItem("selectedIdProject", project.id_prycto); // Guardar id_prycto en local storage
  }
  return project;
});

// Observa los cambios en el modelo y actualiza el almacenamiento local
watch(
  () => model.cd_cntble,
  (newCdCntble) => {
    localStorage.setItem("selectedCdCntble", newCdCntble || "");
  }
);
</script>
