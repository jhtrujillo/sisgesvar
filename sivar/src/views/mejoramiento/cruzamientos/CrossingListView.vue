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
          Consulte, filtre y gestione el registro completo de cruces en la plataforma de mejoramiento.
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
        <BaseButton variant="primary" size="sm" @click="downloadExcel">
          <template #icon-left>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
          </template>
          Exportar Excel
        </BaseButton>
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
                    class="block w-full min-w-[90px] px-2 py-1.5 border border-slate-200 rounded-lg text-[10px] text-slate-700 placeholder-slate-400 focus:ring-1 focus:ring-emerald-200 focus:border-cenicana transition-all bg-white shadow-inner"
                    @input="updateColumnFilter(column.key, ($event.target as HTMLInputElement).value)"
                  />
                </th>
              </template>
              <!-- Acciones Column -->
              <th scope="col" class="px-5 py-3 text-center border-b border-slate-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Acción</div>
              </th>
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

                  <!-- ID Plot Vivero Styling -->
                  <span
                    v-else-if="column.key === 'vivero_plot'"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-100 shadow-xs"
                  >
                    {{ crossing.vivero_plot || crossing.ubccion_nvra || crossing.id_actual_nvra || '—' }}
                  </span>

                  <!-- Pedigree Styling -->
                  <span
                    v-else-if="column.key === 'pdgree'"
                    class="font-mono text-[10px] py-1 px-2 bg-slate-50 border border-slate-200/40 rounded-lg text-slate-650 font-semibold shadow-sm inline-block"
                  >
                    {{ crossing[column.key as keyof typeof crossing] || "N/A" }}
                  </span>

                  <!-- Plántulas Totales Styling -->
                  <span
                    v-else-if="column.key === 'plntlas_ttles'"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-mono font-bold"
                    :class="[crossing.plntlas_ttles && Number(crossing.plntlas_ttles) > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60']"
                  >
                    {{ crossing.plntlas_ttles || 0 }} plántulas
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

              <!-- Action Cell -->
              <td class="px-5 py-3 whitespace-nowrap text-center text-xs">
                <button
                  type="button"
                  @click="openEditModal(crossing)"
                  class="inline-flex items-center px-3 py-1 bg-cenicana hover:bg-cenicana-700 text-white text-[11px] font-bold rounded-lg shadow-2xs transition-all cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                  Editar
                </button>
              </td>
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
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
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
          <BaseButton variant="secondary" size="xs" @click="firstPage()" :disabled="CrossingsListsStore.currentPage === 1"> Primera </BaseButton>
          <BaseButton variant="secondary" size="xs" @click="previousPage()" :disabled="CrossingsListsStore.currentPage === 1">
            <template #icon-left>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
            </template>
            Anterior
          </BaseButton>
        </div>

        <div class="text-xs font-bold text-slate-500 bg-white border border-slate-200/80 px-3 py-1 rounded-xl shadow-sm">
          Página <span class="text-emerald-700 font-extrabold">{{ CrossingsListsStore.currentPage }}</span> de
          <span class="text-slate-800 font-extrabold">{{ CrossingsListsStore.totalPages }}</span>
        </div>

        <div class="flex gap-2">
          <BaseButton variant="secondary" size="xs" @click="nextPage()" :disabled="CrossingsListsStore.currentPage === CrossingsListsStore.totalPages">
            Siguiente
            <template #icon-right>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </template>
          </BaseButton>
          <BaseButton variant="secondary" size="xs" @click="lastPage()" :disabled="CrossingsListsStore.currentPage === CrossingsListsStore.totalPages">
            Última
          </BaseButton>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Edición de Cruzamiento -->
  <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-100">
      <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
          <span class="p-1.5 bg-emerald-100 text-cenicana rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
          </span>
          Modificar Cruzamiento #{{ editingCrossing?.id_crzmnto }}
        </h3>
        <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 hover:bg-slate-200/60 transition-all cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-4">
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
          <p class="text-xs font-bold text-slate-800">
            Cruza: <span class="text-cenicana-700">{{ editingCrossing?.vrdad_mdre }} x {{ editingCrossing?.vrdad_pdre1 }}</span>
          </p>
          <p class="text-[11px] text-slate-500 font-mono">Pedigree: {{ editingCrossing?.pdgree || 'N/A' }}</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
            Plántulas Totales Germinadas: <span class="text-rose-500">*</span>
          </label>
          <input
            type="number"
            min="0"
            v-model="editForm.numero_plantas_sembradas"
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-white focus:ring-2 focus:ring-emerald-200 focus:border-cenicana transition-all font-mono font-bold"
            placeholder="Ingrese el número de plántulas germinadas..."
          />
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observaciones:</label>
          <textarea
            v-model="editForm.observaciones"
            rows="3"
            class="w-full p-3 border border-slate-200 rounded-xl text-xs bg-white focus:ring-2 focus:ring-emerald-200 focus:border-cenicana transition-all"
            placeholder="Comentarios adicionales..."
          ></textarea>
        </div>
      </div>

      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
        <button
          type="button"
          @click="isEditModalOpen = false"
          class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all cursor-pointer"
        >
          Cancelar
        </button>
        <button
          type="button"
          @click="saveEditCruzamiento"
          :disabled="isSaving"
          class="px-5 py-2 bg-cenicana hover:bg-cenicana-700 text-white text-xs font-bold rounded-xl shadow-md transition-all disabled:opacity-50 cursor-pointer inline-flex items-center"
        >
          <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Guardar Cambios
        </button>
      </div>
    </div>
  </div>

  <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
  <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />
</template>

<script setup lang="ts">
import { useCrossingsStore } from "@/stores/crossings";
import api from "@/services/api";
import urls from "@/services/urls";
import * as XLSX from "xlsx";
import { ref, computed, onMounted } from "vue";
import { useToast } from "vue-toastification";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import BackButton from "@/components/BackButton.vue";

const toast = useToast();
const CrossingsListsStore = useCrossingsStore();
const searchText = ref("");

// Estados para Modal de Edición
const isEditModalOpen = ref(false);
const isSaving = ref(false);
const editingCrossing = ref<any>(null);
const editForm = ref({
  id_cruzamiento: null,
  numero_plantas_sembradas: 0,
  numero_plantas_germinadas: 0,
  observaciones: ""
});

const openEditModal = (crossing: any) => {
  editingCrossing.value = crossing;
  editForm.value = {
    id_cruzamiento: crossing.id_crzmnto,
    numero_plantas_sembradas: crossing.plntlas_ttles || 0,
    numero_plantas_germinadas: crossing.plntlas_ttles || 0,
    observaciones: crossing.obsrvcnes || ""
  };
  isEditModalOpen.value = true;
};

const saveEditCruzamiento = async () => {
  if (!editForm.value.id_cruzamiento) return;
  isSaving.value = true;

  try {
    const res: any = await api.post(urls.API_URL + "crossing/modify", editForm.value, true);
    toast.success("Plántulas germinadas actualizadas con éxito");
    isEditModalOpen.value = false;
    await CrossingsListsStore.getCrossings();
  } catch (error: any) {
    console.error("Error al actualizar cruzamiento:", error);
    toast.error("Error al guardar los cambios en el servidor");
  } finally {
    isSaving.value = false;
  }
};

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

  let backendCol = columnKey;
  if (columnKey.startsWith("vrdad_pdre")) {
    backendCol = "padres";
  }

  columnTimeouts[columnKey] = setTimeout(async () => {
    await CrossingsListsStore.setColumnFilter(backendCol, value);
  }, 500);
};

// Función para obtener la clave válida para el jornal en el v-for
const getCrossingsKey = (crossing: any) => crossing.id_crzmnto.toString();
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
    key: "vivero_plot",
    text: "ID Plot Vivero"
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
    key: "plntlas_ttles",
    text: "Plántulas Totales"
  }
];
const columnsToShow = ref(["id_crzmnto", "vivero_plot", "pdgree", "vrdad_mdre", "vrdad_pdre1", "plntlas_ttles"]);
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
