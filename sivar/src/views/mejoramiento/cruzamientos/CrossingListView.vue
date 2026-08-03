<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="w-full max-w-[98%] mx-auto px-2 sm:px-4 space-y-6 pt-2 pb-12 animate-fade-in">
    <!-- Botón Volver -->
    <BackButton :to="{ name: 'cruzamientos.show' }" label="Volver" />

    <!-- Headers -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 pb-4 gap-4">
      <div>
        <h1
          class="text-3xl font-extrabold tracking-tight text-slate-800 bg-gradient-to-r from-cenicana-800 to-emerald-600 bg-clip-text text-transparent flex items-center"
        >
          <div class="p-1.5 bg-emerald-50 text-cenicana rounded-xl mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
              />
            </svg>
          </div>
          Historial de Cruzamientos
        </h1>
        <p class="mt-1.5 text-xs font-semibold text-slate-450 ml-10">
          Consulte y filtre el registro completo de cruces completados en la plataforma de mejoramiento.
        </p>
      </div>

      <div class="flex items-center gap-3 self-end lg:self-auto w-full lg:w-auto justify-end">
        <!-- Search bar -->
        <div class="relative rounded-xl shadow-sm max-w-xs w-full">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            name="buscar"
            id="buscar"
            placeholder="Filtrar registros..."
            class="block w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all duration-200 bg-white shadow-inner"
            v-model="searchText"
            @input="updateFilteredCrossings"
          />
        </div>

        <!-- Download Excel -->
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 border border-transparent text-xs font-bold text-white hover:bg-cenicana-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cenicana transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0"
          @click="downloadExcel"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Exportar Excel
        </button>
      </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-premium overflow-hidden">
      <div class="overflow-x-auto scrollbar-custom">
        <table class="min-w-full divide-y divide-slate-100">
          <thead class="bg-slate-50/75">
            <tr>
              <template v-for="column in tableColumns" :key="column.key">
                <th v-if="columnsToShow.includes(column.key)" scope="col" class="px-5 py-3 text-left border-b border-slate-100">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                    {{ column.text }}
                  </div>
                  <!-- Input de filtro por columna -->
                  <input
                    type="text"
                    :placeholder="'Filtrar ' + column.text.toLowerCase() + '...'"
                    class="block w-full min-w-[100px] px-2 py-1.5 border border-slate-200 rounded-lg text-[10px] text-slate-700 placeholder-slate-400 focus:ring-1 focus:ring-emerald-200 focus:border-cenicana transition-all bg-white shadow-inner"
                    @input="updateColumnFilter(column.key, ($event.target as HTMLInputElement).value)"
                  />
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-50">
            <tr
              v-for="crossing in CrossingsListsStore.crossing"
              :key="getCrossingsKey(crossing)"
              class="hover:bg-slate-50/40 hover:shadow-[inset_4px_0_0_#10b981] transition-all duration-200 group"
            >
              <template v-for="column in tableColumns" :key="column.key">
                <td
                  v-if="columnsToShow.includes(column.key)"
                  class="px-5 py-3 whitespace-nowrap text-xs text-slate-700 group-hover:text-slate-900 font-medium border-slate-100"
                >
                  <!-- ID Badge -->
                  <span
                    v-if="column.key === 'id_crzmnto'"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-slate-50 border border-slate-200/80 text-slate-500 shadow-sm"
                  >
                    #{{ crossing[column.key as keyof typeof crossing] }}
                  </span>

                  <!-- Pedigree Styling -->
                  <span
                    v-else-if="column.key === 'pdgree'"
                    class="font-mono text-[10px] py-1 px-2 bg-slate-50 border border-slate-200/40 rounded-lg text-slate-650 font-semibold shadow-sm inline-block"
                  >
                    {{ crossing[column.key as keyof typeof crossing] || "N/A" }}
                  </span>

                  <!-- Variedad Madre Style -->
                  <span
                    v-else-if="column.key === 'vrdad_mdre'"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-100 shadow-sm cursor-pointer hover:bg-emerald-100 hover:text-emerald-950 transition-colors"
                    @click="openVarietyProfile(crossing[column.key as keyof typeof crossing])"
                  >
                    {{ crossing[column.key as keyof typeof crossing] }}
                  </span>

                  <!-- Padres Style -->
                  <span v-else-if="column.key.startsWith('vrdad_pdre')">
                    <span
                      v-if="crossing[column.key as keyof typeof crossing]"
                      class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-800 border border-sky-100/80 shadow-sm cursor-pointer hover:bg-sky-100 hover:text-sky-950 transition-colors"
                      @click="openVarietyProfile(crossing[column.key as keyof typeof crossing])"
                    >
                      {{ crossing[column.key as keyof typeof crossing] }}
                    </span>
                    <span v-else class="text-slate-300 font-bold text-center block max-w-[20px]">—</span>
                  </span>

                  <!-- Default column -->
                  <span v-else>
                    {{ crossing[column.key as keyof typeof crossing] }}
                  </span>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="CrossingsListsStore.crossing.length === 0" class="flex flex-col items-center justify-center py-16 px-4 text-center">
        <svg class="h-12 w-12 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
        <h3 class="mt-3 text-sm font-bold text-slate-700">No se encontraron registros</h3>
        <p class="mt-1 text-xs text-slate-400 max-w-sm">No hay datos de cruzamientos registrados que coincidan con la búsqueda actual.</p>
      </div>

      <!-- Pagination -->
      <div
        v-if="CrossingsListsStore.totalPages > 1"
        class="bg-slate-50/50 px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2"
      >
        <div class="flex gap-2">
          <button
            class="inline-flex items-center px-3 py-1.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            @click="firstPage()"
            :disabled="CrossingsListsStore.currentPage === 1"
          >
            Primera
          </button>
          <button
            class="inline-flex items-center px-3 py-1.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            @click="previousPage()"
            :disabled="CrossingsListsStore.currentPage === 1"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Anterior
          </button>
        </div>

        <div class="text-xs font-bold text-slate-500 bg-white border border-slate-200/80 px-3 py-1 rounded-xl shadow-sm">
          Página <span class="text-emerald-700 font-extrabold">{{ CrossingsListsStore.currentPage }}</span> de
          <span class="text-slate-800 font-extrabold">{{ CrossingsListsStore.totalPages }}</span>
        </div>

        <div class="flex gap-2">
          <button
            class="inline-flex items-center px-3 py-1.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            @click="nextPage()"
            :disabled="CrossingsListsStore.currentPage === CrossingsListsStore.totalPages"
          >
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          <button
            class="inline-flex items-center px-3 py-1.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            @click="lastPage()"
            :disabled="CrossingsListsStore.currentPage === CrossingsListsStore.totalPages"
          >
            Última
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
  <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />
</template>

<script setup lang="ts">
import { useCrossingsStore } from "@/stores/crossings";
import * as XLSX from "xlsx";
import { ref, computed, onMounted } from "vue";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import BackButton from "@/components/BackButton.vue";

const CrossingsListsStore = useCrossingsStore();
const searchText = ref("");

// Estados para el Drawer de variedades
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const openVarietyProfile = (name: any) => {
  const nameStr = String(name || "").trim();
  if (nameStr && nameStr !== "null" && nameStr !== "?") {
    selectedVarietyForDrawer.value = nameStr;
    isDrawerOpen.value = true;
  }
};

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

let searchTimeout: any = null;
const updateFilteredCrossings = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    await CrossingsListsStore.setSearchQuery(searchText.value);
  }, 500); // 500ms debounce
};

const columnTimeouts: Record<string, any> = {};
const updateColumnFilter = (columnKey: string, value: string) => {
  if (columnTimeouts[columnKey]) clearTimeout(columnTimeouts[columnKey]);

  // Agrupar todos los padres en un solo filtro si es necesario,
  // pero el backend ya lo maneja genéricamente para padres o usa el key específico
  let backendCol = columnKey;
  if (columnKey.startsWith("vrdad_pdre")) {
    backendCol = "padres";
    // El backend buscará en cualquiera de los padres si mandamos 'padres'
  }

  columnTimeouts[columnKey] = setTimeout(async () => {
    await CrossingsListsStore.setColumnFilter(backendCol, value);
  }, 500);
};

// Función para obtener la clave válida para el jornal en el v-for
const getCrossingsKey = (crossing: any) => crossing.id_crzmnto.toString(); // Asegurar que la clave sea un string válido
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
