<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div class="w-full max-w-4xl mx-auto">
      <h1 class="text-center font-bold text-4xl mb-2 text-violet-800">Programación de cruzamientos</h1>
      <h2 class="text-center font-bold text-2xl mb-2 text-violet-800">Matriz de cruzamientos</h2>

      <div class="text-center w-full max-w-4xl mx-auto mb-2">
        <span class="text-green-800 font-semibold uppercase">Proyecto principal: {{ selectedCdCntble }}</span>
        <span class="text-green-800 font-semibold uppercase">Mega Ambiente: {{ selectedMegaAmbiente }}</span>
        <span class="text-green-800 font-semibold uppercase">Variedad: {{ selectedVariety }}</span>
      </div>

      <!-- Tabla de cruzamientos -->
      <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg max-w-4xl mx-auto">
        <table class="table-auto w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Hembra / Macho</th>
              <th
                v-for="flor in SuggestionCrossingStore.suggestionCrossingsFilter.flores || []"
                :key="flor.id_pr"
                class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500"
              >
                {{ flor.vrdad }}<br />Cantidad: {{ flor.numero }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="(viabilidadRow, index) in SuggestionCrossingStore.suggestionCrossingsFilter.viabilidades || []" :key="index" class="hover:bg-blue-50">
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800">
                {{ viabilidadRow.varA }}<br />VM: {{ viabilidadRow.vm }}<br />Polen: {{ viabilidadRow.polen }}
              </td>
              <td v-for="car in viabilidadRow" :key="car.varB" :class="car.viabilidad ? 'bg-green-100' : 'bg-gray-100'">
                <input type="checkbox" :checked="car.viabilidad" @click="toggleCruzamiento(car)" />
                <div class="text-center">DG: {{ getDistancia(car.varA, car.varB) || "NA" }}</div>
              </td>
            </tr>
          </tbody>
        </table>
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
import { useSuggestionCrossingStore } from "@/stores/crossingsuggestion";
import { useToast } from "vue-toastification";
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

// Watch para recargar los datos al cambiar los filtros
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  if (newMegaAmbiente && newCdCntble && newVariety) {
    console.log("Proyectos:", newCdCntble);
    console.log("Testigo:", newVariety);
    console.log("Proyecto:", newCdCntble);
    console.log("Mega Ambiente:", newMegaAmbiente);

    await SuggestionCrossingStore.getSuggestionCrossingList(newCdCntble, newCdCntble, newVariety, newMegaAmbiente);
    console.log(SuggestionCrossingStore.suggestionCrossingsFilter);
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

// Función para enviar los cruzamientos
const submitCruzamientos = () => {
  if (cruzamientos.value) {
    router.push({ name: "crossing_suggestion", params: { cruzamientos: cruzamientos.value } });
  } else {
    toast.error("Selecciona al menos un cruzamiento");
  }
};
</script>
