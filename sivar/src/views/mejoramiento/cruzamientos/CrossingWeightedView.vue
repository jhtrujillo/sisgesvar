<template>
  <div class="space-y-8 w-full max-w-4xl mx-auto px-4 pt-6">
    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
          <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 3m0-3a2 2 0 110 3m-9 8h10M-3 14a2 2 0 110-3m3 3a2 2 0 100-3m9 15h10M7 21a2 2 0 110-3m3 3a2 2 0 100-3" />
            </svg>
          </div>
          Programación de Cruzamientos
        </h1>
        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">
          Paso 2 de 3: Ponderados y Niveles
        </span>
      </div>
      <div class="flex flex-wrap items-center justify-between ml-11 mt-2 text-sm text-slate-500">
        <span>Defina la variedad de referencia y los pesos de importancia de cada característica.</span>
        <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100 mt-2 sm:mt-0">
          Proyecto: {{ selectedCdCntble }}
        </span>
      </div>
    </div>

    <!-- Panel de Configuración -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-premium">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Testigo Selector -->
        <div>
          <label class="block uppercase tracking-wider text-slate-600 text-xs font-bold mb-3">Variedad Testigo (Referencia)</label>
          <div class="relative">
            <ComboBoxMultiple
              :data-list="dataListVariedades"
              :column-value="columnValueVariedades"
              :column-to-show="columnToShowVariedades"
              v-model:selectedData="selectedVariety"
              placeholder="Seleccione la variedad..."
            />
          </div>
        </div>

        <!-- Mega Ambiente Selector -->
        <div>
          <label class="block uppercase tracking-wider text-slate-600 text-xs font-bold mb-3">Mega Ambiente</label>
          <select v-model="selectedMegaAmbiente" class="w-full px-4 py-2.5 text-sm bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition-all duration-200 text-slate-700 font-medium">
            <option value="" disabled>Seleccione el ambiente...</option>
            <option value="Semiseco">Seco Semiseco</option>
            <option value="Humedo">Húmedo</option>
            <option value="Piedemonte">Piedemonte</option>
          </select>
        </div>
      </div>

      <!-- Tabla de Ponderados -->
      <div v-if="selectedVariety && selectedMegaAmbiente" class="mt-8 space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ponderados de Características para la Matriz</h3>
        <div class="overflow-hidden border border-slate-100 rounded-xl shadow-sm">
          <table class="table-auto w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Nombre de la Característica</th>
                <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">Nivel de Entrada</th>
                <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">Valor Individual</th>
                <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">Porcentaje (%)</th>
                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(item, index) in ponderadosFiltrados" :key="index" class="hover:bg-emerald-50/20 transition-colors duration-150">
                <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-700">{{ item.nombre }}</td>
                <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-semibold text-slate-600">{{ item.nivel || "-" }}</td>
                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-slate-600 font-medium">{{ item.ponderado || 0 }}</td>
                <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-bold text-emerald-600 bg-emerald-50/30">{{ calcularPonderado(item) }}%</td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                  <button
                    @click="openModal(item, `${!item.ponderado ? 1 : 0}`)"
                    class="px-4 py-1.5 text-xs font-bold text-emerald-700 bg-white border border-emerald-200 hover:bg-emerald-50 hover:border-emerald-300 rounded-xl transition-all duration-200"
                  >
                    Modificar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Estado vacío cuando no han seleccionado testigo/ambiente -->
      <div v-else class="flex flex-col items-center justify-center py-12 text-center text-slate-400 space-y-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-medium">Seleccione una variedad testigo y un mega ambiente para desplegar y editar los pesos ponderados.</span>
      </div>
    </div>

    <!-- Botones de Navegación -->
    <div class="flex justify-between pt-4">
      <router-link :to="{ name: 'crossing_initial_data.show' }">
        <button
          type="button"
          class="flex items-center px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 transition-all duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Atrás
        </button>
      </router-link>
      <router-link :to="{ name: 'crossing_matrix.show' }">
        <button
          type="button"
          class="flex items-center px-6 py-2.5 text-sm font-bold text-white bg-cenicana hover:bg-cenicana-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition-all duration-200"
          :disabled="!selectedVariety || !selectedMegaAmbiente"
        >
          Generar Matriz
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </router-link>
    </div>

    <!-- Modal para modificar características -->
    <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-100">
        <!-- Header del modal -->
        <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
          <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Modificar Característica</h4>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl">&times;</button>
        </div>

        <!-- Cuerpo del modal -->
        <div class="p-6 space-y-6">
          <div class="p-3 bg-emerald-50/50 text-emerald-900 text-xs font-semibold rounded-lg border border-emerald-100/50">
            Editando <span class="underline font-bold">{{ nombre }}</span> para el proyecto <span class="font-bold">{{ selectedCdCntble }}</span>.
          </div>

          <!-- Mensaje de error -->
          <div v-if="errorMessage" class="bg-red-50 text-red-700 text-xs p-3 rounded-lg border border-red-100 font-medium">
            <strong>Error:</strong> {{ errorMessage }}
          </div>

          <!-- Contenido del formulario -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Nivel -->
            <div>
              <label for="nivel_modal" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nivel</label>
              <input
                v-model="nivel"
                id="nivel_modal"
                type="text"
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
                @input="validateNumber('nivel')"
                placeholder="Ej. 1"
              />
            </div>

            <!-- Ponderado -->
            <div>
              <label for="ponderado_modal" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Ponderado</label>
              <input
                v-model="ponderado"
                id="ponderado_modal"
                type="text"
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
                @input="validateNumber('ponderado')"
                placeholder="Ej. 70"
              />
            </div>
          </div>
        </div>

        <!-- Footer del modal -->
        <div class="flex justify-end items-center border-t border-slate-100 p-5 bg-slate-50">
          <button @click="closeModal" class="mr-3 px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200">
            Cancelar
          </button>
          <button @click="modificarCaracteristica" class="px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200">
            Guardar Cambios
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
const selectedMegaAmbiente = ref<string | null>("");
const selectedCdCntble = ref<string | null>(localStorage.getItem("selectedCdCntble") || "");
const dataListVariedades = varietyStore.Variety;
const columnValueVariedades = "nm_vrdad";
const columnToShowVariedades = "nm_vrdad";

// Computed para almacenar y recuperar los valores desde localStorage
const storedVariety = computed(() => localStorage.getItem("selectedVariety"));
const storedMegaAmbiente = computed(() => localStorage.getItem("selectedMegaAmbiente"));
const storedCdCntble = computed(() => localStorage.getItem("selectedCdCntble"));

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
</script>
