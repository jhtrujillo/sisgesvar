<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div class="w-full max-w-4xl mx-auto">
      <h1 class="text-center font-bold text-4xl mb-2 text-violet-800">Programación de cruzamientos</h1>
      <h2 class="text-center font-bold text-2xl mb-2 text-violet-800">Ponderados y niveles</h2>

      <div class="text-center w-full max-w-4xl mx-auto mb-2">
        <span class="text-green-800 font-semibold uppercase">Proyecto principal: {{ selectedCdCntble }}</span>
      </div>

      <div class="flex flex-wrap justify-between p-2 text-center w-full max-w-4xl mx-auto">
        <div class="w-full px-3 mb-2">
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2">Variedad testigo:</label>
          <div class="combo-box-wrapper">
            <ComboBoxMultiple
              :data-list="dataListVariedades"
              :column-value="columnValueVariedades"
              :column-to-show="columnToShowVariedades"
              v-model:selectedData="selectedVariety"
            />
          </div>
        </div>

        <div class="w-full px-3 mb-2">
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2">Mega ambiente:</label>
          <select v-model="selectedMegaAmbiente" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-md">
            <option value="Semiseco">Seco Semiseco</option>
            <option value="Humedo">Húmedo</option>
            <option value="Piedemonte">Piedemonte</option>
          </select>
        </div>
      </div>

      <!-- Tabla de Ponderados -->
      <div v-if="selectedVariety && selectedMegaAmbiente" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg max-w-4xl mx-auto">
        <table class="table-auto w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Nombre</th>
              <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Nivel</th>
              <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Ponderado Individual</th>
              <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Ponderado (%)</th>
              <th class="px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="(item, index) in ponderadosFiltrados" :key="index" class="hover:bg-blue-50">
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800">{{ item.nombre }}</td>
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800">{{ item.nivel || "-" }}</td>
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800">{{ item.ponderado || 0 }}</td>
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800 hidden">{{ item.id_caracteristica || 0 }}</td>
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800">{{ calcularPonderado(item) }}%</td>
              <td class="whitespace-nowrap py-2 pl-4 pr-3 text-center text-sm font-medium text-gray-800">
                <button
                  @click="openModal(item, `${!item.ponderado ? 1 : 0}`)"
                  class="text-violet-600 hover:text-white hover:bg-violet-600 px-4 py-1 rounded-full border border-violet-600"
                >
                  Modificar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between mt-2 max-w-4xl mx-auto">
        <router-link :to="{ name: 'crossing_initial_data.show' }">
          <button
            type="button"
            class="block px-4 py-2 mt-2 text-sm font-semibold text-violet-800 bg-transparent border border-violet-800 rounded-lg hover:text-white hover:bg-violet-800 focus:outline-none focus:ring-2 focus:ring-violet-800"
          >
            Atrás
          </button>
        </router-link>
        <router-link :to="{ name: 'crossing_matrix.show' }">
          <button
            type="button"
            class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold text-violet-800 bg-transparent border border-violet-800 rounded-lg hover:text-white hover:bg-violet-800 focus:outline-none focus:ring-2 focus:ring-violet-800"
          >
            Siguiente
          </button>
        </router-link>
      </div>
    </div>
  </div>

  <div>
    <!-- Modal para modificar características -->
    <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <!-- Header del modal -->
        <div class="flex justify-between items-center border-b p-4">
          <h4 class="text-lg font-semibold">Modificar característica {{ nombre }} en el proyecto {{ selectedCdCntble }}</h4>
          <button @click="closeModal" class="text-gray-600 hover:text-red-600">&times;</button>
        </div>

        <!-- Cuerpo del modal -->
        <div class="p-4">
          <!-- Mensaje de error -->
          <div v-if="errorMessage" class="bg-red-100 text-red-700 p-2 mb-4 rounded"><strong>Error!</strong> {{ errorMessage }}</div>

          <!-- Contenido del formulario -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Nivel -->
            <div>
              <label for="nivel_modal" class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
              <input
                v-model="nivel"
                id="nivel_modal"
                type="text"
                class="w-full border border-gray-300 rounded px-3 py-2"
                @input="validateNumber('nivel')"
                placeholder="Nivel"
              />
            </div>

            <!-- Ponderado -->
            <div>
              <label for="ponderado_modal" class="block text-sm font-medium text-gray-700 mb-2">Ponderado</label>
              <input
                v-model="ponderado"
                id="ponderado_modal"
                type="text"
                class="w-full border border-gray-300 rounded px-3 py-2"
                @input="validateNumber('ponderado')"
                placeholder="Ponderado"
              />
            </div>
          </div>

          <!-- Campos hidden -->
          <input type="hidden" v-model="id_caracteristica" id="caracteristica_id_modal" />
          <input type="hidden" v-model="selectedCdCntble" id="proyecto_id_modal" />
          <input type="hidden" v-model="nuevo" id="nuevo" />
        </div>

        <!-- Footer del modal -->
        <div class="flex justify-end items-center border-t p-4">
          <button @click="closeModal" class="mr-3 px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200">
            Cancelar
          </button>
          <button @click="modificarCaracteristica" class="px-4 py-2 text-sm font-semibold text-white bg-violet-600 rounded-lg hover:bg-violet-700">
            Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted } from "vue";
import { useVarietyStore } from "@/stores/variety";
import { useParametizeWeightedCrossingStore } from "@/stores/crossignparametizeweighted";
import { useModifyFeaturesCrossingStore } from "@/stores/crossignmodifyfeatures";
import { useToast } from "vue-toastification";
import { useMainStore } from "@/stores/main";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";

// Declaración de variables
const varietyStore = useVarietyStore();
const parametizeWeightedCrossignStore = useParametizeWeightedCrossingStore();
const modifyFeaturesStore = useModifyFeaturesCrossingStore();
const toast = useToast();
const mainStore = useMainStore();

// Variables para almacenar las selecciones
const selectedVariety = ref<string | null>(null);
const selectedMegaAmbiente = ref<string | null>(null);
const selectedCdCntble = ref<string | null>(localStorage.getItem("selectedCdCntble") || "");
const dataListVariedades = varietyStore.Variety;
const columnValueVariedades = "nm_vrdad";
const columnToShowVariedades = "nm_vrdad";

// Computed para almacenar y recuperar los valores desde localStorage
const storedVariety = computed(() => localStorage.getItem("selectedVariety"));
const storedMegaAmbiente = computed(() => localStorage.getItem("selectedMegaAmbiente"));
const storedCdCntble = computed(() => localStorage.getItem("selectedCdCntble"));

console.log(storedVariety, storedMegaAmbiente, storedCdCntble); // Logs para verificar el almacenamiento

// Variables y estado para manejar el modal
const isModalOpen = ref(false);
const proyecto = ref("");
const nombre = ref("");
const ponderado = ref("");
const nivel = ref("");
const id_caracteristica = ref("");
const nuevo = ref("");
const errorMessage = ref<string | null>(null);

// OnMounted para cargar datos y recuperar valores de localStorage
onMounted(async () => {
  await varietyStore.getVariety();

  // Cargar valores desde el localStorage si existen
  if (storedVariety.value) selectedVariety.value = storedVariety.value;
  if (storedMegaAmbiente.value) selectedMegaAmbiente.value = storedMegaAmbiente.value;
});

// Watchers para guardar los cambios en localStorage cuando se actualizan las selecciones
watch(
  () => selectedVariety.value,
  (newVariety) => {
    if (newVariety) localStorage.setItem("selectedVariety", newVariety);
  }
);

watch(
  () => selectedMegaAmbiente.value,
  (newMegaAmbiente) => {
    if (newMegaAmbiente) localStorage.setItem("selectedMegaAmbiente", newMegaAmbiente);
  }
);

// Watch para filtrar en base a los cambios
watch([selectedMegaAmbiente, selectedCdCntble], async ([selectedMegaAmbiente, selectedCdCntble]) => {
  if (selectedMegaAmbiente && selectedCdCntble !== null) {
    const proyecto = selectedCdCntble;
    const megaAmbiente = selectedMegaAmbiente;
    await parametizeWeightedCrossignStore.getParametizeWeightedCrossingList(proyecto, megaAmbiente);
  }
});

// Abrir modal y asignar valores iniciales
const openModal = (data: any, nuevoFlag: string) => {
  proyecto.value = data.selectedCdCntble;
  nombre.value = data.nombre;
  ponderado.value = data.ponderado;
  nivel.value = data.nivel;
  id_caracteristica.value = data.id_caracteristica;
  nuevo.value = nuevoFlag;
  isModalOpen.value = true;
};

// Cerrar modal
const closeModal = () => {
  isModalOpen.value = false;
  errorMessage.value = null;
};

// Validación para que solo se ingresen números
const validateNumber = (field: "ponderado" | "nivel") => {
  if (field === "ponderado") {
    ponderado.value = ponderado.value.replace(/[^0-9]/g, "");
  } else if (field === "nivel") {
    nivel.value = nivel.value.replace(/[^0-9]/g, "");
  }
};

// Filtrar los ponderados correctamente
const ponderadosFiltrados = computed(() => {
  return parametizeWeightedCrossignStore.parametizeWeightedCrossingFilter.ponderados || [];
});

// Cálculo del porcentaje ponderado
const calcularPonderado = (item: any) => {
  const sumaPonderados = Number(ponderadosFiltrados.value.reduce((acc: number, curr: any) => acc + (Number(curr.ponderado) || 0), 0));

  const itemPonderado = Number(item.ponderado) || 0;
  return sumaPonderados ? ((itemPonderado / sumaPonderados) * 100).toFixed(2) : "0.00";
};

// Función para modificar las características
const modificarCaracteristica = async () => {
  const nivelValue = Number(nivel.value);
  const ponderadoValue = Number(ponderado.value);

  if (isNaN(nivelValue)) {
    errorMessage.value = "El nivel debe ser un número válido.";
    return;
  } else if (isNaN(ponderadoValue)) {
    errorMessage.value = "El ponderado debe ser un número válido.";
    return;
  }

  const nuevoValue = Number(nuevo.value);
  if (isNaN(nuevoValue)) {
    errorMessage.value = "El nuevo debe ser un número válido.";
    return;
  }

  try {
    const result = await modifyFeaturesStore.getModifyFeaturesCrossingList(
      id_caracteristica.value ?? "",
      selectedCdCntble.value ?? "",
      nivelValue.toString(),
      ponderadoValue.toString(),
      nuevoValue
    );

    if (result) {
      toast.success("Actualizado con éxito");

      const proyecto = selectedCdCntble.value ?? "";
      const megaAmbiente = selectedMegaAmbiente.value ?? "";

      await parametizeWeightedCrossignStore.getParametizeWeightedCrossingList(proyecto, megaAmbiente);
      closeModal();
    }
  } catch (error) {
    console.error(error);
    toast.error("Error al modificar");
  }
};

watch(
  () => mainStore.error,
  () => {
    const error = mainStore.error;
    if (error) {
      toast.error(error);
    }
  }
);

// const isBusy = computed(() => mainStore.isBusy);
</script>
