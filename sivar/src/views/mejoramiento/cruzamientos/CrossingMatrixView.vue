<template>
  <div class="w-full flex-col grid place-content-center">
    <div class="w-full max-w-4xl mx-auto">
      <h1 class="text-center font-bold text-4xl mb-2 text-violet-800">Programación de cruzamientos</h1>
      <h2 class="text-center font-bold text-2xl mb-2 text-violet-800">Matriz de cruzamientos</h2>

      <!-- <div class="text-center w-full max-w-4xl mx-auto mb-2">
        <span class="text-green-800 font-semibold uppercase">Proyecto principal: {{ selectedCdCntble }}</span>
        <span class="text-green-800 font-semibold uppercase">Mega Ambiente: {{ selectedMegaAmbiente }}</span>
        <span class="text-green-800 font-semibold uppercase">Variedad: {{ selectedVariety }}</span>
      </div> -->

      <!-- Tabla de cruzamientos -->
      <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg max-w-4xl mx-auto">
        <!-- Envolver la tabla en un div con scroll horizontal y vertical y estilo personalizado para el scroll -->
        <div class="max-h-96 overflow-x-auto overflow-y-auto scrollbar-thin scrollbar-thumb-violet-500 scrollbar-track-gray-200">
          <table class="table-auto w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500 bg-gray-50 sticky top-0 left-0 z-20">todos x todos</th>
                <th
                  v-for="flor in MatrixCrossingStore.matrixCrossingsFilter.flores || []"
                  :key="flor.vrdad"
                  class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500 bg-gray-50 sticky top-0 z-10"
                >
                  {{ flor.vrdad }}<br />Polen: {{ flor.polen }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="(viabilidadRow, indexRow) in MatrixCrossingStore.matrixCrossingsFilter.viabilidad || []" :key="indexRow" class="hover:bg-blue-50">
                <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800 bg-white sticky left-0 z-10">
                  {{ viabilidadRow[0].varA }}<br />VM: {{ viabilidadRow[0].vm }}<br />Polen: {{ viabilidadRow[0].polen }}
                </td>
                <td v-for="(car, indexCol) in viabilidadRow" :key="indexCol" :class="car?.viabilidad ? 'bg-green-200' : 'bg-gray-100'">
                  <input type="checkbox" :checked="!!car?.viabilidad" @click="toggleCruzamiento(car)" />
                  <div class="text-center">DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Botones para atrás y siguiente -->
      <div class="flex justify-between mt-2 max-w-4xl mx-auto">
        <router-link :to="{ name: 'crossing_weighted.show' }">
          <button class="block px-4 py-2 mt-2 text-sm font-semibold text-violet-800 border border-violet-800 rounded-lg hover:text-white hover:bg-violet-800">
            Atrás
          </button>
        </router-link>
        <button
          @click="submitCruzamientos"
          class="block px-4 py-2 mt-2 text-sm font-semibold text-violet-800 border border-violet-800 rounded-lg hover:text-white hover:bg-violet-800"
        >
          Siguiente
        </button>
      </div>
    </div>
  </div>
  <input type="hidden" v-model="selectedCdCntble" id="proyecto_id_modal" />
  <input type="hidden" v-model="selectedVariety" id="variedad" />
  <input type="hidden" v-model="selectedMegaAmbiente" id="ambiente" />
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useMatrixCrossingStore } from "@/stores/crossingmatrix";
import { useToast } from "vue-toastification";
import type { CruzamientoSeleccionado } from "@/services/types";

const MatrixCrossingStore = useMatrixCrossingStore();
const toast = useToast();
const router = useRouter();

const selectedVariety = ref("");
const selectedMegaAmbiente = ref("");
const selectedCdCntble = ref("");

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

// Watch para recargar los datos al cambiar los filtros
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  if (newMegaAmbiente && newCdCntble && newVariety) {
    await MatrixCrossingStore.getMatrixCrossingList(newCdCntble, newCdCntble, newVariety);
  }
});

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
</script>

<style>
/* Estilos personalizados para la barra de desplazamiento */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-thumb {
  background-color: #7c3aed; /* Violeta */
  border-radius: 10px;
}

::-webkit-scrollbar-track {
  background-color: #e5e7eb; /* Gris claro */
}
</style>
