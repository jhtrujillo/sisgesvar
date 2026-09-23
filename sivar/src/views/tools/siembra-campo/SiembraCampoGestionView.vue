<template>
  <div class="container mx-auto p-6">
    <div class="mb-6">
      <div class="mb-4">
        <BaseButton variant="secondary" size="sm" rounded="full" :to="{ name: 'main.show' }">
          <template #icon-left>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </template>
          Volver a Inicio
        </BaseButton>
      </div>
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-cenicana" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            Gestión Siembra Campo
          </h1>
          <p class="text-sm text-slate-500 mt-1">Módulo de Herramientas (Tools)</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
        <div class="p-6">
          <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2">Importar Excel a Siembra Campo</h3>
          <p class="text-sm text-slate-500 mb-6 line-clamp-3">
            Carga masiva de datos desde archivos Excel (.xlsx, .xls) a la tabla siembra_campo. Permite seleccionar hoja, realizar un mapeo inteligente de
            columnas y validar campos obligatorios antes de insertar.
          </p>
          <BaseButton variant="primary" block @click="showWizard = true"> Iniciar Importación </BaseButton>
        </div>
      </div>
    </div>

    <SiembraCampoImportWizard :show="showWizard" @close="showWizard = false" @imported="onImported" />
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import SiembraCampoImportWizard from "@/components/tools/siembra-campo/SiembraCampoImportWizard.vue";

const showWizard = ref(false);

const onImported = () => {
  showWizard.value = false;
};
</script>
