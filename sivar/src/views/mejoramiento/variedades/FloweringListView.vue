<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="min-h-screen bg-slate-50/50 flex flex-col p-4 sm:p-8 font-sans">
    <!-- Premium Header Area -->
    <div class="w-full mx-auto mb-6 bg-white/80 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200/60 flex flex-col sm:flex-row items-center justify-between transition-all duration-300">
      <div class="flex items-center gap-5">
        <div class="p-3 bg-gradient-to-br from-violet-600 to-fuchsia-600 rounded-xl shadow-lg shadow-violet-500/30">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-900 to-fuchsia-700 tracking-tight">
            Tabla de Floración
          </h1>
          <p class="text-sm text-slate-500 font-medium">Registros detallados de campo</p>
        </div>
      </div>
      
      <div class="flex items-center gap-6 mt-4 sm:mt-0">
        <!-- Switch Histórico -->
        <label class="relative inline-flex items-center cursor-pointer group" title="Visualizar todos los registros de floración de los últimos 10 años">
          <input type="checkbox" v-model="verHistorico" class="sr-only peer">
          <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
          <span class="ml-3 text-sm font-bold text-slate-600 group-hover:text-violet-700 transition-colors">Modo Histórico</span>
        </label>

        <router-link
          class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-300 bg-slate-900 border border-transparent rounded-full hover:bg-violet-700 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 focus:outline-none"
          :to="{ name: 'mejoramiento.show' }"
        >
          <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Volver
        </router-link>
      </div>
    </div>

    <!-- English Presentation Summary Card -->
    <div class="w-full mx-auto mb-6 bg-gradient-to-r from-violet-50/50 via-white to-fuchsia-50/30 border border-violet-100/70 rounded-2xl p-5 shadow-sm flex items-start gap-4">
      <div class="p-2 bg-violet-100 text-violet-700 rounded-xl mt-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <div class="flex-1">
        <h3 class="text-xs font-bold text-violet-900 uppercase tracking-wider mb-2">Module Presentation Overview (English)</h3>
        <p class="text-xs text-slate-600 leading-relaxed">
          This module manages the <strong>Flowering Registry (Floración)</strong>. It dynamically filters and displays only the active, available flowers associated with each research project registered within the last 24 hours. It serves as the primary interface to log, store, and quantify pollen viability metrics. During this crucial stage, breeders evaluate parent specimens to define which varieties will act as the <strong>maternal (female)</strong> and <strong>paternal (male)</strong> parents for the upcoming hybridization and crossing processes.
        </p>
      </div>
    </div>

    <!-- Table Container -->
    <div class="w-full bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 flex-1 relative z-10 p-2 sm:p-6 overflow-hidden min-h-[400px]">
      
      <!-- Overlay Loading State -->
      <div v-if="isLoading" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300">
        <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center gap-3 border border-violet-100">
          <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-violet-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useFloweringStore } from "@/stores/flowering";
import TableComponent from "../../../components/app-table/TableComponent.vue";
import type { Column } from "../../../components/app-table/models";

const floweringListsStore = useFloweringStore();
const verHistorico = ref(false);
const isLoading = ref(false);

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
  // COLUMNAS VISIBLES POR DEFECTO (AGRUPADAS)
  // ==========================================

  // Identificador único de la floración en la base de datos
  { keyName: "id_flrcion", text: "Id" },

  // Nombre de la variedad de la planta o especie botánica recolectada
  { keyName: "vrdad", text: "Variedad" },

  // Columna combinada que muestra el día y la hora exacta en que se registró la floración
  { 
    keyName: "fecha_hora", 
    text: "Fecha y Hora", 
    formatFromRow: (row) => `${row.fcha || ''} ${row.hra || ''}`.trim() 
  },

  // Columna combinada que resume la localización geográfica y parcelaria (Hacienda, Lote, Parcela, Surco)
  { 
    keyName: "ubicacion", 
    text: "Ubicación", 
    formatFromRow: (row) => `${row.hcnda || ''} (L${row.lte || '-'} P${row.prcla || '-'} S${row.srco || '-'})` 
  },

  // Columna combinada que muestra el sexo original de la planta y, si hubo una mutación/cambio inducido, lo indica con una flecha
  { 
    keyName: "sexo_comp", 
    text: "Sexo", 
    formatFromRow: (row) => row.cmbio_sxo ? `${row.sxo} ➝ ${row.cmbio_sxo}` : (row.sxo || 'N/A') 
  },

  // Porcentaje de viabilidad o cantidad del polen disponible en la flor
  { 
    keyName: "polen_comp", 
    text: "Polen", 
    formatFromRow: (row) => row.polen ? `${row.polen}%` : 'N/A' 
  },

  // Nombre del proyecto de investigación genética al cual está asociada esta flor
  { keyName: "nm_prycto", text: "Proyecto" },

  // ==========================================
  // COLUMNAS OCULTAS POR DEFECTO (DETALLADAS)
  // Disponibles en el botón 'Columnas' y en Exportación Excel
  // ==========================================

  // Fecha bruta sin hora
  { keyName: "fcha", text: "Fecha (Original)", hiddenByDefault: true },
  
  // Hora bruta sin fecha
  { keyName: "hra", text: "Hora (Original)", hiddenByDefault: true },
  
  // Nombre de la hacienda o estación experimental
  { keyName: "hcnda", text: "Hacienda (Original)", hiddenByDefault: true },
  
  // Número o identificador del lote en el campo
  { keyName: "lte", text: "Lote (Original)", hiddenByDefault: true },
  
  // Número o identificador de la parcela específica
  { keyName: "prcla", text: "Parcela (Original)", hiddenByDefault: true },
  
  // Número de surco donde está sembrada la planta
  { keyName: "srco", text: "Surco (Original)", hiddenByDefault: true },
  
  // Sexo biológico base de la planta (Ej. Macho, Hembra, etc.)
  { keyName: "sxo", text: "Sexo (Original)", hiddenByDefault: true },
  
  // Sexo secundario o modificado por inducción/químicos
  { keyName: "cmbio_sxo", text: "Cambio de Sexo (Original)", hiddenByDefault: true },
  
  // Valor numérico crudo del polen
  { keyName: "polen", text: "Polen (Original)", hiddenByDefault: true },
  
  // Estado fenológico o tipo de floración observada
  { keyName: "flrcion", text: "Floración", hiddenByDefault: true },
  
  // Grupo de cruzamiento o familia al que pertenece
  { keyName: "grpo", text: "Grupo", hiddenByDefault: true },
  
  // Conteos sucesivos de granos de polen viables vs totales en diferentes mediciones o muestras (1 a 5)
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
  
  // Booleano o indicador si la flor fue seleccionada explícitamente para un cruce
  { keyName: "slcciondo", text: "Seleccionado", hiddenByDefault: true },
  
  // Características morfológicas relevantes de la planta
  { keyName: "nmbre_crcter", text: "Caracter", hiddenByDefault: true },
  
  // Nombre o id del vivero de aclimatación, si aplica
  { keyName: "vivero", text: "Vivero", hiddenByDefault: true },
  
  // Notas textuales adicionales hechas por el investigador en el campo
  { keyName: "obsrvcn", text: "Observación", hiddenByDefault: true },
  
  // Nombre o ID del usuario del sistema que ingresó o modificó este registro
  { keyName: "usuario", text: "Usuario que Editó", hiddenByDefault: true },
  
  // Ingenio azucarero o institución primaria responsable
  { keyName: "ingnio", text: "Ingenio", hiddenByDefault: true },
  
  // Identificador referencial a la tabla de siembras de campo
  { keyName: "id_smbra_cmpo", text: "Id Siembra de Campo", hiddenByDefault: true }
];
</script>
