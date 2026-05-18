<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div class="w-full max-w-4xl mx-auto">
      <router-link :to="{ name: 'cruzamientos.show' }">
        <button
          type="button"
          class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold text-violet-800 bg-transparent border border-violet-800 rounded-lg hover:text-white hover:bg-violet-800 focus:outline-none focus:ring-2 focus:ring-violet-800"
        >
          Volver
        </button>
      </router-link>
    </div>

    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Programación de cruzamientos</h1>
    <h2 class="text-center font-bold text-3xl mb-6 text-violet-800">Datos Iniciales</h2>

    <div class="flex flex-wrap justify-between p-4 text-center w-full max-w-4xl mx-auto">
      <div class="w-full px-3 mb-4">
        <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="grid-state"> Proyectos con flores </label>
        <div class="mb-4">
          <ComboBoxMultiple
            :data-list="mappedCrossingInitialData"
            :column-value="columnValueCrossingInitialData"
            :column-to-show="'combinedValue'"
            v-model:selectedData="model.cd_cntble"
            placeholder="Selecciona un proyecto"
          />
        </div>
      </div>
    </div>

    <!-- Tabla que muestra los detalles del proyecto seleccionado -->
    <div v-if="filteredProject" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
      <table class="table-auto overflow-x-scroll w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
          <tr>
            <th class="cursor-pointer px-3 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500">Código</th>
            <th class="cursor-pointer px-3 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500">Nombre</th>
            <th class="cursor-pointer px-3 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500">Cantidad</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          <tr class="hover:bg-blue-50">
            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-center text-sm font-medium text-gray-800 sm:pl-6">{{ filteredProject.cd_cntble }}</td>
            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-center text-sm font-medium text-gray-800 sm:pl-6">{{ filteredProject.nm_prycto }}</td>
            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-center text-sm font-medium text-gray-800 sm:pl-6">{{ filteredProject.numero }} flores</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Botón Siguiente -->
    <div class="mt-6">
      <router-link :to="{ name: 'crossing_weighted.show' }">
        <button
          type="button"
          class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold text-violet-800 bg-transparent border border-violet-800 rounded-lg hover:text-white hover:bg-violet-800 focus:outline-none focus:ring-2 focus:ring-violet-800"
          :disabled="!model.cd_cntble"
        >
          Siguiente
        </button>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, reactive, computed } from "vue";
import { useCrossingInitialDataStore } from "@/stores/crossinginitialdata";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import type { CrossingInitialData } from "@/services/types";

// Stores y variables
const crossingInitialDataStore = useCrossingInitialDataStore();
const crossingInitialData = ref<CrossingInitialData[]>([]);
const mappedCrossingInitialData = ref<CrossingInitialData[]>([]);
const model = reactive({ cd_cntble: "" } as Partial<CrossingInitialData>); // Modelo reactivo

// Configuración para ComboBoxMultiple
const columnValueCrossingInitialData = "cd_cntble";

// Cargar los datos iniciales cuando el componente esté montado
onMounted(async () => {
  await crossingInitialDataStore.getCrossingInitialDataList();
  crossingInitialData.value = crossingInitialDataStore.crossingInitialDataList;

  // Mapeo para concatenar los valores de 'cd_cntble' y 'nm_prycto'
  mappedCrossingInitialData.value = crossingInitialData.value.map((item) => ({
    ...item,
    combinedValue: `${item.cd_cntble} - ${item.nm_prycto}`
  }));

  // Cargar el valor almacenado en local storage (si existe)
  const storedCdCntble = localStorage.getItem("selectedCdCntble");
  if (storedCdCntble) model.cd_cntble = storedCdCntble;
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
