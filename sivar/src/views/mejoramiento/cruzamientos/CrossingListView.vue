<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{ name: 'cruzamientos.show' }"
        >
          Volver
        </router-link>
      </button>
    </div>
    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Cruzamientos</h1>
    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
      <div class="flex flex-col sm:flex-row justify-between items-center w-full px-4 py-2">
        <div class="mb-4 sm:mb-0 sm:w-1/3">
          <input
            type="text"
            name="buscar"
            id="buscar"
            placeholder="Buscar"
            class="block w-full rounded-md border-0 px-2 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            v-model="searchText"
            @input="updateFilteredCrossings"
          />
        </div>
        <div class="flex space-x-2">
          <button
            type="button"
            class="inline-flex items-center rounded-md bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            @click="downloadExcel"
          >
            <i class="bi bi-file-earmark-excel-fill"></i> Excel
          </button>
        </div>
      </div>
      <div class="mt-8 overflow-x-auto">
        <table class="table-auto w-full divide-y divide-gray-300">
          <!-- Encabezados de las columnas -->
          <thead class="bg-gray-50">
            <tr>
              <template v-for="column in tableColumns" :key="column.key">
                <th v-if="columnsToShow.includes(column.key)" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                  {{ column.text }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <!-- Renderizar cada jornal en una fila de la tabla -->
            <tr v-for="crossing in CrossingsListsStore.crossing" :key="getCrossingsKey(crossing)">
              <!-- Renderizar los datos correspondientes a cada columna -->
              <template v-for="column in tableColumns" :key="column.key">
                <td v-if="columnsToShow.includes(column.key)" class="px-4 py-3 text-sm font-medium text-gray-800 whitespace-nowrap">
                  {{ crossing[column.key as keyof typeof crossing] }}
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Controles de paginación -->
      <div v-if="CrossingsListsStore.totalPages > 1" class="flex items-center justify-between m-2 px-4 py-3 sm:px-6">
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="firstPage()"
          :disabled="CrossingsListsStore.currentPage === 1"
        >
          Primera
        </button>
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="previousPage()"
          :disabled="CrossingsListsStore.currentPage === 1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
          </svg>
          Anterior
        </button>
        <button>
          <span class="font-medium">{{ CrossingsListsStore.currentPage }}</span> /
          <span class="font-medium">{{ CrossingsListsStore.totalPages }}</span>
        </button>
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="nextPage()"
          :disabled="CrossingsListsStore.currentPage === CrossingsListsStore.totalPages"
        >
          Siguiente
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
          </svg>
        </button>
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="lastPage()"
          :disabled="CrossingsListsStore.currentPage === CrossingsListsStore.totalPages"
        >
          Última
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCrossingsStore } from "@/stores/crossings";
import * as XLSX from "xlsx";
import { ref, computed, onMounted } from "vue";

const CrossingsListsStore = useCrossingsStore();
const searchText = ref("");
const firstPage = async () => {
  if (CrossingsListsStore.currentPage > 1) {
    CrossingsListsStore.currentPage = 1;
    await CrossingsListsStore.setCurrentPage(CrossingsListsStore.currentPage);
  }
};
const previousPage = async () => {
  if (CrossingsListsStore.currentPage >= 1) {
    CrossingsListsStore.currentPage--;
    await CrossingsListsStore.setCurrentPage(CrossingsListsStore.currentPage);
  }
};
const nextPage = async () => {
  if (CrossingsListsStore.currentPage < CrossingsListsStore.totalPages) {
    CrossingsListsStore.currentPage = CrossingsListsStore.currentPage + 1;
    await CrossingsListsStore.setCurrentPage(CrossingsListsStore.currentPage);
  }
};
const lastPage = async () => {
  if (CrossingsListsStore.currentPage < CrossingsListsStore.totalPages) {
    CrossingsListsStore.currentPage = CrossingsListsStore.totalPages;
    await CrossingsListsStore.setCurrentPage(CrossingsListsStore.currentPage);
  }
};

// Filtrar la lista de jornales según el texto de búsqueda
const filteredJornalesList = computed(() => {
  const normalizedSearchText = searchText.value.trim().toLowerCase();
  return CrossingsListsStore.crossing.filter((crossing) =>
    Object.values(crossing).some((value) => typeof value === "string" && value.toLowerCase().includes(normalizedSearchText))
  );
});
const updateFilteredCrossings = () => {
  CrossingsListsStore.crossing = filteredJornalesList.value;
};
// Función para obtener la clave válida para el jornal en el v-for
const getCrossingsKey = (crossing: any) => crossing.id_crzmnto.toString(); // Asegurar que la clave sea un string válido
// Función para generar el archivo Excel
// Función para generar el archivo Excel con todos los datos
const downloadExcel = () => {
  const allData = CrossingsListsStore.crossing.map((crossing) =>
    Object.fromEntries(Object.entries(crossing).filter(([key]) => columnsToShow.value.includes(key)))
  );
  const worksheet = XLSX.utils.json_to_sheet(allData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Histórico Cruzamientos");
  XLSX.writeFile(workbook, "historico_cruzamientos.xlsx");
};

console.log(CrossingsListsStore.crossing);
const tableColumns = [
  {
    key: "id_crzmnto",
    text: "ID"
  },
  {
    key: "pdgree",
    text: "Pedigree"
  },
  {
    key: "vrdad_mdre",
    text: "Variedad Madre"
  },
  {
    key: "vrdad_pdre1",
    text: "Padre 1"
  },
  {
    key: "vrdad_pdre2",
    text: "Padre 2"
  },
  {
    key: "vrdad_pdre3",
    text: "Padre 3"
  },
  {
    key: "vrdad_pdre4",
    text: "Padre 4"
  },
  {
    key: "vrdad_pdre5",
    text: "Padre 5"
  }
];
const columnsToShow = ref(["id_crzmnto", "pdgree", "vrdad_mdre", "vrdad_pdre1", "vrdad_pdre2", "vrdad_pdre3", "vrdad_pdre4", "vrdad_pdre5"]);
onMounted(async () => {
  await CrossingsListsStore.getCrossings();
});
</script>
