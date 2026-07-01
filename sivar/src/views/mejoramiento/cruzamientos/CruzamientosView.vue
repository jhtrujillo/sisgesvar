<template>
  <div class="space-y-8 w-full max-w-6xl mx-auto px-4">
    <!-- Botón Volver -->
    <div class="flex justify-start">
      <router-link
        class="group flex items-center px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-emerald-50 hover:text-cenicana-800 hover:border-emerald-200 transition-all duration-200"
        :to="{ name: 'mejoramiento.show' }"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Volver a Mejoramiento
      </router-link>
    </div>

    <!-- Title Block -->
    <div class="border-b border-slate-100 pb-5">
      <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
        <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
          </svg>
        </div>
        Módulo de Cruzamientos
      </h1>
      <p class="text-slate-500 mt-2 ml-11 text-sm">Planifique, consulte y realice el seguimiento de cruzamientos genéticos para el desarrollo de nuevas variedades.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Ver Cruzamientos Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'crossing_list.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 640 512" class="transition-transform duration-500 group-hover:scale-110">
              <path
                fill="currentColor"
                d="M176 288a112 112 0 1 0 0-224a112 112 0 1 0 0 224zm176-112c0 86.3-62.1 158.1-144 173.1V384h32c17.7 0 32 14.3 32 32s-14.3 32-32 32h-32v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32h-32c-17.7 0-32-14.3-32-32s14.3-32 32-32h32v-34.9C62.1 334.1 0 262.3 0 176C0 78.8 78.8 0 176 0s176 78.8 176 176zm-80.1 184.6c19.3-10.1 36.9-23.1 52.1-38.4c20 18.5 46.7 29.8 76.1 29.8c61.9 0 112-50.1 112-112s-50.1-112-112-112c-7.2 0-14.3.7-21.1 2c-4.9-21.5-13-41.7-24-60.2c14.3-3.8 29.4-5.8 45-5.8c37 0 71.4 11.4 99.8 31l20.6-20.6L487 41c-6.9-6.9-8.9-17.2-5.2-26.2S494.3 0 504 0h112c13.3 0 24 10.7 24 24v112c0 9.7-5.8 18.5-14.8 22.2s-19.3 1.7-26.2-5.2l-33.4-33.4l-20.6 20.6c19.5 28.4 31 62.7 31 99.8c0 97.2-78.8 176-176 176c-50.5 0-96-21.3-128.1-55.4z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Ver Cruzamientos</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Consulte el catálogo histórico y el registro de cruzamientos exitosos programados.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Ver Registros
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Programación de Cruzamientos Card -->
      <div
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden cursor-pointer"
        @click="iniciarProgramacion"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:rotate-12">
              <path
                fill="currentColor"
                d="M7 3V1h2v2h6V1h2v2h4a1 1 0 0 1 1 1v5h-2V5h-3v2h-2V5H9v2H7V5H4v14h6v2H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm10 9a4 4 0 1 0 0 8a4 4 0 0 0 0-8m-6 4a6 6 0 1 1 12 0a6 6 0 0 1-12 0m5-3v3.414l2.293 2.293l1.414-1.414L18 15.586V13z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Programación</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Planificación científica y ponderación de cruzamientos de acuerdo con viabilidad y mérito genético.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Configurar
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </div>

      <!-- Consolidado de Cruzamientos Card (Oculto Temporalmente) -->
      <router-link
        v-if="false"
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'alignments.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 16 16" class="transition-transform duration-500 group-hover:scale-105">
              <path
                fill="currentColor"
                d="M2.5 1.75v11.5c0 .138.112.25.25.25h3.17a.75.75 0 0 1 0 1.5H2.75A1.75 1.75 0 0 1 1 13.25V1.75C1 .784 1.784 0 2.75 0h8.5C12.216 0 13 .784 13 1.75v7.736a.75.75 0 0 1-1.5 0V1.75a.25.25 0 0 0-.25-.25h-8.5a.25.25 0 0 0-.25.25m13.274 9.537l-4.557 4.45a.75.75 0 0 1-1.055-.008l-1.943-1.95a.75.75 0 0 1 1.062-1.058l1.419 1.425l4.026-3.932a.75.75 0 1 1 1.048 1.074M4.75 4h4.5a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1 0-1.5M4 7.75A.75.75 0 0 1 4.75 7h2a.75.75 0 0 1 0 1.5h-2A.75.75 0 0 1 4 7.75"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Consolidado</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Visualice alineamientos y resúmenes estadísticos consolidados de la campaña actual.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Ver Consolidado
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import { useLinksStore } from "@/stores/links";
import { useMatrixCrossingStore } from "@/stores/crossingmatrix";

const router = useRouter();
const linksStore = useLinksStore();
const MatrixCrossingStore = useMatrixCrossingStore();

onMounted(async () => {
  await linksStore.getLinks();
});

function iniciarProgramacion() {
  // 1. Borrar todas las variables temporales de localStorage asociadas al asistente
  localStorage.removeItem("selectedVariety");
  localStorage.removeItem("selectedMegaAmbiente");
  localStorage.removeItem("selectedCdCntble");
  localStorage.removeItem("selectedIdProject");
  localStorage.removeItem("cruzamientos");
  
  // Limpiar borradores guardados de sugerencias
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key && (key.startsWith("suggestion_draft_") || key.startsWith("cruzamientos_draft_"))) {
      localStorage.removeItem(key);
      i--; // Disminuir el índice para no saltarse elementos tras remover
    }
  }

  // 2. Reiniciar estados persistidos de Pinia para cruzamientos
  MatrixCrossingStore.cruzamientosSeleccionados = [];
  MatrixCrossingStore.matrixCrossingsFilter = [];

  // 3. Redirigir al primer paso
  router.push({ name: "crossing_initial_data.show" });
}
</script>
