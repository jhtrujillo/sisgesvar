<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div class="w-full max-w-4xl mx-auto">
      <h1 class="text-center font-bold text-4xl mb-2 text-violet-800">Programación de cruzamientos</h1>
      <h2 class="text-center font-bold text-2xl mb-2 text-violet-800">Sugerencia de cruzamientos</h2>

      <div class="text-center w-full max-w-4xl mx-auto mb-2">
        <span class="text-green-800 font-semibold uppercase">Proyecto principal: {{ selectedCdCntble }}</span>
        <span class="text-green-800 font-semibold uppercase">Mega Ambiente: {{ selectedMegaAmbiente }}</span>
        <span class="text-green-800 font-semibold uppercase">Variedad: {{ selectedVariety }}</span>
      </div>

      <div>
        <div v-if="flattenedViabilidades.length > 0" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg max-w-4xl mx-auto">
          <div class="max-h-96 overflow-x-auto overflow-y-auto scrollbar-thin scrollbar-thumb-violet-500 scrollbar-track-gray-200">
            <table class="table-auto w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Hembra / Posibles Machos</th>
                  <th
                    v-for="(flor, index) in floresSeleccionadas"
                    :key="index"
                    class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500"
                  >
                    {{ flor.variedad }}<br />
                    Cantidad: {{ flor.cantidad }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="(viabilidad, index) in flattenedViabilidades" :key="index">
                  <td
                    :style="{ backgroundColor: getBackgroundColor(viabilidad.polen) }"
                    class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800"
                  >
                    <i :class="getIcon(viabilidad.polen)"></i>
                    <div>
                      <b>{{ viabilidad.varA }}</b>
                      &nbsp; <b>VM:</b> {{ viabilidad.vm }} &nbsp; <b>Polen:</b> {{ viabilidad.polen }}
                    </div>
                    <div><b>Cantidad:</b> {{ getCantidadFlores(viabilidad.varA) }}</div>
                    <div>
                      <label><b>¿Autofecundar?</b></label>
                      <input type="checkbox" @click="toggleAutofecundar(viabilidad)" />
                    </div>
                  </td>
                  <td v-for="car in viabilidad.machos" :key="car.varB" :style="{ backgroundColor: car.viabilidad ? '#E7FFDD' : '#E3E6E2' }" class="text-center">
                    <input type="checkbox" :checked="car.viabilidad" @click="toggleCruzamiento(car)" />
                    <div>
                      <b>{{ car.varB }}</b
                      ><br />
                      Polen: {{ car.polen2 }}<br />
                      VM: {{ car.vm2 }}<br />
                      DG: {{ getDistancia(car.varA, car.varB) }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else>
          <p class="text-center text-gray-500">No hay datos de viabilidades disponibles.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import { useSuggestionCrossingPerProjectStore } from "@/stores/crossingsuggestionperproject";

const SuggestionCrossingPerProjectStore = useSuggestionCrossingPerProjectStore();
const selectedCdCntble = ref(localStorage.getItem("selectedCdCntble") || "");
const selectedMegaAmbiente = ref(localStorage.getItem("selectedMegaAmbiente") || "");
const selectedVariety = ref(localStorage.getItem("selectedVariety") || "");
const selectedIdProject = ref(localStorage.getItem("selectedIdProject") || "");

// Cargar datos iniciales
onMounted(() => {
  loadSuggestionCrossings();
});

watch([selectedIdProject, selectedMegaAmbiente, selectedCdCntble, selectedVariety], loadSuggestionCrossings);

async function loadSuggestionCrossings() {
  if (selectedIdProject.value && selectedMegaAmbiente.value && selectedCdCntble.value && selectedVariety.value) {
    await SuggestionCrossingPerProjectStore.getSuggestionCrossingPerProjectList(
      selectedIdProject.value,
      selectedCdCntble.value,
      selectedMegaAmbiente.value,
      selectedVariety.value
    );
  }
}

const floresSeleccionadas = computed(() => {
  const flores = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.flores;
  return flores
    ? flores.map((flor: { numero: number; vrdad: string }) => ({
        variedad: flor.vrdad,
        cantidad: flor.numero
      }))
    : [];
});

// Aplanar viabilidades
const flattenedViabilidades = computed(() => {
  const viabilidades = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.viabilidades || [];
  return viabilidades.flat();
});

// Métodos auxiliares
function getCantidadFlores(vrdad: string) {
  const flor = floresSeleccionadas.value.find((f: any) => f.variedad === vrdad);
  return flor ? flor.cantidad : 0;
}

function getBackgroundColor(polen: string | number) {
  return +polen <= 20 ? "#FFC0CB" : "#C0E0FF";
}

function getIcon(polen: string | number) {
  return +polen <= 20 ? "fa fa-venus" : "fa fa-mars";
}

function toggleCruzamiento(car: any) {
  car.viabilidad = !car.viabilidad;
}

function toggleAutofecundar(viabilidadRow: any) {
  console.info(`Autofecundación seleccionada para ${viabilidadRow.varA}`);
}

function getDistancia(varA: string, varB: string) {
  const distancias = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.distancias || {};
  return distancias[varA]?.[varB] || "NA";
}
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
