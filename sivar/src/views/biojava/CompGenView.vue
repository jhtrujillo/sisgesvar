<template>
  <div class="space-y-6 w-full px-2 sm:px-4 pb-12">
    
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <router-link
          :to="{ name: 'biojava.show' }"
          class="flex items-center text-slate-500 hover:text-teal-600 transition-colors group"
        >
          <div class="p-2 bg-slate-50 group-hover:bg-teal-50 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
        </router-link>
        <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
          <div class="p-2 bg-teal-50 text-teal-600 rounded-xl mr-3">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          Genómica Comparativa (comp-gen)
        </h1>
      </div>
    </div>
    
    <p class="text-slate-500 text-sm ml-14 mb-8 max-w-3xl">
      Integra y visualiza bloques de sintenia (McScanX) y ortología con anotaciones funcionales para análisis evolutivos.
    </p>

    <div v-if="errorMsg" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 relative" role="alert">
      <span class="block sm:inline">{{ errorMsg }}</span>
      <button @click="errorMsg = ''" class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
      </button>
    </div>

    <!-- Si no hay resultado, mostramos el formulario -->
    <div v-if="!resultHtmlUrl" class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
      
      <!-- Panel de Autocompletado (Presets) -->
      <div class="mb-8 p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-700">Autocompletar Formulario (Presets)</h3>
          <p class="text-xs text-slate-500">Carga automáticamente todas las rutas de los pipelines de referencia predefinidos en el servidor.</p>
        </div>
        <button 
          @click="loadPreset1940vsR570" 
          type="button" 
          class="mt-3 sm:mt-0 px-4 py-2 bg-indigo-100 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 hover:border-indigo-600 rounded-lg text-sm font-semibold transition-colors flex items-center shadow-sm"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Cargar pipeline CC 1940 vs R570
        </button>
      </div>

      <form @submit.prevent="runAnalysis" class="space-y-6">
        
        <h3 class="text-lg font-bold text-slate-700 border-b pb-2">Parámetros Obligatorios</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Archivo de Colinealidad (.collinearity)</label>
            <ServerFilePicker v-model="form.collinearity" placeholder="/ruta/al/archivo.collinearity" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">GFF Genoma 1</label>
            <ServerFilePicker v-model="form.gff1" placeholder="/ruta/al/genoma1.gff" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">GFF Genoma 2</label>
            <ServerFilePicker v-model="form.gff2" placeholder="/ruta/al/genoma2.gff" />
          </div>
        </div>

        <h3 class="text-lg font-bold text-slate-700 border-b pb-2 mt-8">Parámetros Adicionales Obligatorios (Pipeline)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">FASTA CDS Genoma 1 (.cds.fna)</label>
            <ServerFilePicker v-model="form.cds1" placeholder="/ruta/al/genoma1.cds.fna" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">FASTA CDS Genoma 2 (.cds.fna)</label>
            <ServerFilePicker v-model="form.cds2" placeholder="/ruta/al/genoma2.cds.fna" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Mostrar Genoma 1 (Ej. R570)</label>
            <input v-model="form.name1" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="R570">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Mostrar Genoma 2 (Ej. CC 1940)</label>
            <input v-model="form.name2" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="CC 1940">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Organismo (Tasa de Sustitución)</label>
            <select v-model="form.organism" class="w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
              <option value="Saccharum">Saccharum (Predeterminado)</option>
              <option value="Arabidopsis">Arabidopsis</option>
              <option value="Human">Humano</option>
              <option value="Populus">Populus</option>
              <option value="Drosophila">Drosophila</option>
            </select>
          </div>
        </div>

        <h3 class="text-lg font-bold text-slate-700 border-b pb-2 mt-8">Parámetros Opcionales (Integración)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">FASTA Proteínas 1 (.protein.faa)</label>
            <ServerFilePicker v-model="form.prot1" placeholder="Opcional" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">FASTA Proteínas 2 (.protein.faa)</label>
            <ServerFilePicker v-model="form.prot2" placeholder="Opcional" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Archivo VCF (.vcf)</label>
            <ServerFilePicker v-model="form.vcf" placeholder="Opcional (para variantes)" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Archivo Ka/Ks (.tsv pre-calculado)</label>
            <ServerFilePicker v-model="form.kaks" placeholder="Opcional (saltar cálculo)" />
          </div>
        </div>

        <div class="pt-6 flex justify-end">
          <button 
            type="submit" 
            :disabled="isLoading"
            class="flex items-center px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isLoading ? 'Procesando (puede tardar minutos)...' : 'Ejecutar Análisis' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Resultados -->
    <div v-else class="space-y-4">
      <div class="flex justify-between items-center bg-teal-50 border border-teal-200 p-4 rounded-xl">
        <div>
          <h3 class="font-bold text-teal-800">Análisis Completado</h3>
          <p class="text-sm text-teal-600">El archivo HTML interactivo se ha generado exitosamente.</p>
        </div>
        <button @click="resetForm" class="px-4 py-2 bg-white text-teal-700 border border-teal-300 rounded-lg hover:bg-teal-50 text-sm font-medium transition-colors">
          Nuevo Análisis
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden" style="height: 80vh;">
        <iframe :src="resultHtmlUrl" class="w-full h-full border-none" title="BioJava Interactive Dashboard"></iframe>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import ServerFilePicker from '@/components/ServerFilePicker.vue';

const isLoading = ref(false);
const errorMsg = ref('');
const resultHtmlUrl = ref('');

// Configuración de rutas (Esta es la raíz donde Vite sirve los estáticos)
const PUBLIC_PATH = '/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/public/biojava_outputs/';

const form = ref({
  collinearity: '',
  gff1: '',
  gff2: '',
  annot1: '',
  annot2: '',
  cds1: '',
  cds2: '',
  prot1: '',
  prot2: '',
  vcf: '',
  kaks: '',
  name1: '',
  name2: '',
  organism: 'Saccharum',
  outputFile: '',
  outputHTML: ''
});

// Función de Autocompletado del Pipeline CC-1940 vs R570
const loadPreset1940vsR570 = () => {
  const baseDir = '/Users/estuvar4/Documents/2. software/17.biojava';
  
  form.value = {
    ...form.value,
    gff1: `${baseDir}/dataset_genomico/genomas/CC-01-1940/CC-01-1940.gff`,
    gff2: `${baseDir}/dataset_genomico/genomas/R570/R570.gff`,
    collinearity: `${baseDir}/dataset_genomico/comparativas/CC1940_vs_R570/CC1940_vs_R570.collinearity`,
    cds1: `${baseDir}/dataset_genomico/genomas/CC-01-1940/CC-01-1940.cds.fa`,
    cds2: `${baseDir}/dataset_genomico/genomas/R570/R570.cds.fa`,
    prot1: `${baseDir}/dataset_genomico/genomas/CC-01-1940/CC-01-1940.protein.faa`,
    prot2: `${baseDir}/dataset_genomico/genomas/R570/R570.protein.faa`,
    vcf: `${baseDir}/dataset_genomico/genomas/CC-01-1940/CC-01-1940_sim.vcf`,
    kaks: `${baseDir}/dataset_genomico/comparativas/CC1940_vs_R570/kaks_1940_vs_R570.tsv`,
    name1: 'CC 1940',
    name2: 'R570',
    organism: 'Saccharum'
  };
};

const runAnalysis = async () => {
  isLoading.value = true;
  errorMsg.value = '';
  
  // Generar la carpeta de salida dinámicamente basada en los nombres
  const safeName1 = form.value.name1.trim().replace(/\s+/g, '_');
  const safeName2 = form.value.name2.trim().replace(/\s+/g, '_');
  const subfolder = `comp_gen/${safeName1}_vs_${safeName2}`;
  
  form.value.outputHTML = `${PUBLIC_PATH}${subfolder}/visor_sintenia.html`;
  form.value.outputFile = `${PUBLIC_PATH}${subfolder}/reporte_comparativo.tsv`;

  try {
    const response = await fetch('http://localhost:3001/run-comp-gen', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value)
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data.error || 'Error desconocido al ejecutar comp-gen');
    }

    // El base url lo provee Vite automáticamente (Ej: /apps/sivar-nuevo/)
    // Reemplazamos la doble barra diagonal por precaución
    const viteBase = import.meta.env.BASE_URL;
    resultHtmlUrl.value = `${viteBase}biojava_outputs/${subfolder}/visor_sintenia.html`.replace(/\/\//g, '/');

  } catch (err: any) {
    errorMsg.value = err.message;
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const resetForm = () => {
  resultHtmlUrl.value = '';
};
</script>
