<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="min-h-screen bg-slate-50/50 flex flex-col p-4 sm:p-8 font-sans">
    <!-- Header Area -->
    <div class="space-y-6 w-full mx-auto px-4 mb-6">
      <!-- Botón Volver -->
      <BackButton :to="{ name: 'mejoramiento.show' }" label="Volver a Mejoramiento" />

      <!-- Title Block con switch integrado -->
      <div class="border-b border-slate-100 pb-5 flex items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
            <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                ></path>
              </svg>
            </div>
            Tabla de Floración
          </h1>
          <p class="text-slate-500 mt-2 ml-11 text-sm">
            Consulte y filtre los registros detallados de floración para evaluar la viabilidad del polen y programar los cruzamientos genéticos.
          </p>
        </div>

        <!-- Switch Histórico alineado con el título -->
        <div class="flex flex-col items-end gap-3 shrink-0 mt-1">
          <label
            class="relative inline-flex items-center cursor-pointer group"
            title="Visualizar todos los registros de floración de los últimos 10 años"
          >
            <input type="checkbox" v-model="verHistorico" class="sr-only peer" />
            <div
              class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"
            ></div>
            <span class="ml-3 text-sm font-bold text-slate-600 group-hover:text-emerald-700 transition-colors">Modo Histórico</span>
          </label>
          <button
            @click="isImportWizardOpen = true"
            class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Importar Excel
          </button>
        </div>
      </div>
    </div>

    <!-- English Presentation Summary Card -->
    <div
      class="w-full mx-auto mb-6 bg-gradient-to-r from-violet-50/50 via-white to-fuchsia-50/30 border border-violet-100/70 rounded-2xl p-5 shadow-sm flex items-start gap-4"
    >
      <div class="p-2 bg-violet-100 text-violet-700 rounded-xl mt-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <div class="flex-1">
        <h3 class="text-xs font-bold text-violet-900 uppercase tracking-wider mb-2">Module Presentation Overview (English)</h3>
        <p class="text-xs text-slate-600 leading-relaxed">
          This module manages the <strong>Flowering Registry (Floración)</strong>. It dynamically filters and displays only the active, available flowers
          associated with each research project registered within the last 24 hours. It serves as the primary interface to log, store, and quantify pollen
          viability metrics. During this crucial stage, breeders evaluate parent specimens to define which varieties will act as the
          <strong>maternal (female)</strong> and <strong>paternal (male)</strong> parents for the upcoming hybridization and crossing processes.
        </p>
      </div>
    </div>

    <!-- Table Container -->
    <div
      class="w-full bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 flex-1 relative z-10 p-2 sm:p-6 overflow-hidden min-h-[400px]"
    >
      <!-- Overlay Loading State -->
      <div v-if="isLoading" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300">
        <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center gap-3 border border-violet-100">
          <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-violet-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <span class="text-violet-800 font-bold tracking-wide animate-pulse">Sincronizando registros...</span>
        </div>
      </div>

      <TableComponent
        :rows="floweringListsStore.FloweringList"
        :have-search="true"
        :have-button-excel="true"
        :allow-hide-columns="true"
        name-excel="flowering"
        :columns="conlumnsInfo"
      ></TableComponent>
    </div>
    
    <FloracionImportWizard
      :is-open="isImportWizardOpen"
      @close="isImportWizardOpen = false"
      @imported="onImportSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useFloweringStore } from "@/stores/flowering";
import TableComponent from "../../../components/app-table/TableComponent.vue";
import type { Column } from "../../../components/app-table/models";
import BackButton from "@/components/BackButton.vue";
import FloracionImportWizard from "@/components/floracion/FloracionImportWizard.vue";

const floweringListsStore = useFloweringStore();
const verHistorico = ref(false);
const isLoading = ref(false);
const isImportWizardOpen = ref(false);

const onImportSuccess = async () => {
  try {
    isLoading.value = true;
    await floweringListsStore.getFlowering(verHistorico.value);
  } catch (e) {
    console.error('Error reloading flowering list:', e);
  } finally {
    isLoading.value = false;
  }
};

onMounted(async () => {
  isLoading.value = true;
  await floweringListsStore.getFlowering(verHistorico.value);
  isLoading.value = false;
});

// Recargar los datos cuando el usuario active o desactive el modo histórico
watch(verHistorico, async (nuevoValor) => {
  isLoading.value = true;
  await floweringListsStore.getFlowering(nuevoValor);
  isLoading.value = false;
});

/**
 * Modificación realizada por: Jhon Henry Trujillo PhD
 * Fecha: 2026-05-16
 * Propósito: Refactorizar y agrupar las columnas de la tabla usando formatFromRow y la
 * nueva propiedad hiddenByDefault. Esto resuelve el problema de la tabla excesivamente ancha,
 * mostrando solo las columnas más críticas de inicio y ocultando el resto para visualización posterior.
 */
const conlumnsInfo: Array<Column> = [
  // ==========================================
  // COLUMNAS VISIBLES POR DEFECTO
  // ==========================================
  { keyName: "vivero", text: "Vivero" },
  { keyName: "vrdad", text: "Variedad" },
  { keyName: "nmbre_crcter", text: "Caracter" },
  {
    keyName: "ubicacion",
    text: "Ubicación",
    formatFromRow: (row) => `${row.hcnda || ""} (L${row.lte || "-"} P${row.prcla || "-"} S${row.srco || "-"})`
  },
  {
    keyName: "sexo_comp",
    text: "Sexo",
    formatFromRow: (row) => (row.cmbio_sxo ? `${row.sxo} ➝ ${row.cmbio_sxo}` : row.sxo || "N/A")
  },
  {
    keyName: "polen_comp",
    text: "Polen",
    formatFromRow: (row) => (row.polen ? `${row.polen}%` : "N/A")
  },

  // ==========================================
  // COLUMNAS OCULTAS POR DEFECTO
  // ==========================================
  { keyName: "id_flrcion", text: "Id", hiddenByDefault: true },
  {
    keyName: "fecha_hora",
    text: "Fecha y Hora",
    hiddenByDefault: true,
    formatFromRow: (row) => `${row.fcha || ""} ${row.hra || ""}`.trim()
  },
  { keyName: "nm_prycto", text: "Proyecto", hiddenByDefault: true },
  { keyName: "fcha", text: "Fecha (Original)", hiddenByDefault: true },
  { keyName: "hra", text: "Hora (Original)", hiddenByDefault: true },
  { keyName: "hcnda", text: "Hacienda (Original)", hiddenByDefault: true },
  { keyName: "lte", text: "Lote (Original)", hiddenByDefault: true },
  { keyName: "prcla", text: "Parcela (Original)", hiddenByDefault: true },
  { keyName: "srco", text: "Surco (Original)", hiddenByDefault: true },
  { keyName: "sxo", text: "Sexo (Original)", hiddenByDefault: true },
  { keyName: "cmbio_sxo", text: "Cambio de Sexo (Original)", hiddenByDefault: true },
  { keyName: "polen", text: "Polen (Original)", hiddenByDefault: true },
  { keyName: "flrcion", text: "Floración", hiddenByDefault: true },
  { keyName: "grpo", text: "Grupo", hiddenByDefault: true },
  { keyName: "grnos_vbles1", text: "Granos Viables 1", hiddenByDefault: true },
  { keyName: "ttal_grnos1", text: "Total Granos 1", hiddenByDefault: true },
  { keyName: "grnos_vbles2", text: "Granos Viables 2", hiddenByDefault: true },
  { keyName: "ttal_grnos2", text: "Total Granos 2", hiddenByDefault: true },
  { keyName: "grnos_vbles3", text: "Granos Viables 3", hiddenByDefault: true },
  { keyName: "ttal_grnos3", text: "Total Granos 3", hiddenByDefault: true },
  { keyName: "grnos_vbles4", text: "Granos Viables 4", hiddenByDefault: true },
  { keyName: "ttal_grnos4", text: "Total Granos 4", hiddenByDefault: true },
  { keyName: "grnos_vbles5", text: "Granos Viables 5", hiddenByDefault: true },
  { keyName: "ttal_grnos5", text: "Total Granos 5", hiddenByDefault: true },
  { keyName: "slcciondo", text: "Seleccionado", hiddenByDefault: true },
  { keyName: "obsrvcn", text: "Observación", hiddenByDefault: true },
  {
    keyName: "usrio_edto_comp",
    text: "Editado Por",
    hiddenByDefault: true,
    formatFromRow: (row) => `${row.prmer_nmbre || ""} ${row.aplldo || ""}`.trim()
  },
  { keyName: "fcha_edto", text: "Fecha de Edición", hiddenByDefault: true }
];
</script>
