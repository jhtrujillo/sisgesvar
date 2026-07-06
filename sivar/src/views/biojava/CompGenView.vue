<template>
  <div class="space-y-6 w-full px-2 sm:px-4 pb-12">
    
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-8">
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
        <div>
          <h1 class="text-3xl font-bold text-slate-800">Genómica Comparativa Integrada</h1>
          <p class="text-slate-500 mt-2">Visor interactivo de sintenia e integración funcional</p>
        </div>
      </div>
    </div>
    
    <p class="text-slate-500 text-sm ml-14 mb-8 max-w-3xl">
      Integra y visualiza bloques de sintenia (McScanX) y ortología con anotaciones funcionales para análisis evolutivos.
    </p>

    <!-- Barra de Resultados Pre-procesados -->
    <div class="p-6 md:p-8 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-slate-800">Resultados Procesados</h2>
        <p class="text-sm text-slate-500 mt-1">Selecciona un análisis pre-configurado para cargar automáticamente sus parámetros en el formulario inferior.</p>
      </div>
      <div class="w-full md:w-96">
        <select 
          v-model="selectedPrecomputed"
          @change="loadPrecomputed"
          class="w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm font-medium"
        >
          <option value="">-- Seleccionar Análisis --</option>
          <option value="CC1940_vs_R570">CC 1940 vs R570</option>
          <option value="R570_vs_Spont_sim">R570 vs Spontaneum (Simulación)</option>
        </select>
      </div>
    </div>

    <!-- Visor de Resultados -->
    <div v-if="resultHtmlUrl" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="p-3 bg-slate-50 border-b border-slate-100 flex justify-end items-center">
        <a :href="resultHtmlUrl" target="_blank" class="text-sm text-teal-600 hover:text-teal-700 font-medium flex items-center">
          Abrir en nueva pestaña 
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        </a>
      </div>
      <div class="w-full" style="height: 800px;">
        <iframe :src="resultHtmlUrl" class="w-full h-full border-0"></iframe>
      </div>
    </div>

    <!-- Ejecutar Análisis Manual -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="p-6 md:p-8 bg-white flex items-center justify-between font-bold text-slate-800 text-xl border-b border-slate-100">
        Ejecutar Análisis Manual
      </div>
      
      <div class="p-6 md:p-8 bg-white">
        <form @submit.prevent="runAnalysis" class="space-y-6">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <!-- Archivo Principal -->
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Archivo de Colinealidad (.collinearity) *</label>
              <ServerFilePicker v-model="form.collinearity" placeholder="/ruta/al/archivo.collinearity" />
            </div>

            <!-- Grupo Genoma 1 -->
            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-4">
              <h4 class="font-bold text-teal-800 text-sm border-b border-teal-100 pb-2">Datos Genoma 1</h4>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nombre (Ej. R570) *</label>
                <input v-model="form.name1" type="text" class="w-full border-slate-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-1.5" placeholder="R570">
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">GFF (.gff) *</label>
                <ServerFilePicker v-model="form.gff1" placeholder="/ruta/genoma1.gff" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">FASTA CDS (.cds.fna) *</label>
                <ServerFilePicker v-model="form.cds1" placeholder="/ruta/genoma1.cds.fna" />
              </div>
            </div>

            <!-- Grupo Genoma 2 -->
            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-4">
              <h4 class="font-bold text-teal-800 text-sm border-b border-teal-100 pb-2">Datos Genoma 2</h4>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nombre (Ej. CC 1940) *</label>
                <input v-model="form.name2" type="text" class="w-full border-slate-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-1.5" placeholder="CC 1940">
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">GFF (.gff) *</label>
                <ServerFilePicker v-model="form.gff2" placeholder="/ruta/genoma2.gff" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">FASTA CDS (.cds.fna) *</label>
                <ServerFilePicker v-model="form.cds2" placeholder="/ruta/genoma2.cds.fna" />
              </div>
            </div>

            <!-- Organismo -->
            <div class="md:col-span-2 mt-2">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Organismo (Tasa de Sustitución) *</label>
              <select v-model="form.organism" class="w-full border-slate-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-1.5">
                <option value="Saccharum">Saccharum (Predeterminado)</option>
                <option value="Arabidopsis">Arabidopsis</option>
                <option value="Human">Humano</option>
                <option value="Populus">Populus</option>
                <option value="Drosophila">Drosophila</option>
              </select>
            </div>
          </div>

          <!-- Opciones Avanzadas -->
          <details class="group bg-white rounded-xl border border-slate-200 mt-6">
            <summary class="p-4 cursor-pointer list-none flex items-center justify-between font-bold text-slate-600 text-sm bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
              Parámetros Opcionales de Integración (Proteínas, VCF, Ka/Ks)
              <span class="transition group-open:rotate-180">
                <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="20"><path d="M6 9l6 6 6-6"></path></svg>
              </span>
            </summary>
            <div class="p-4 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 bg-white rounded-b-xl">
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">FASTA Proteínas Genoma 1 (.protein.faa)</label>
                <ServerFilePicker v-model="form.prot1" placeholder="Opcional" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">FASTA Proteínas Genoma 2 (.protein.faa)</label>
                <ServerFilePicker v-model="form.prot2" placeholder="Opcional" />
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">Archivo VCF (.vcf) [Genoma 2]</label>
                <ServerFilePicker v-model="form.vcf" placeholder="Opcional (para variantes)" />
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">Archivo Ka/Ks (.tsv pre-calculado)</label>
                <ServerFilePicker v-model="form.kaks" placeholder="Opcional (saltar cálculo)" />
              </div>
            </div>
          </details>

          <!-- Botón de Ejecución -->
          <div class="pt-6 border-t flex justify-end">
            <button 
              type="submit" 
              :disabled="isLoading"
              class="flex items-center px-8 py-2.5 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700 transition-all focus:ring-4 focus:ring-teal-100 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed text-sm"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ isLoading ? 'Procesando...' : 'Ejecutar Análisis' }}
            </button>
          </div>

          <!-- Mensaje de Error -->
          <div v-if="errorMsg" class="mt-4 p-3 bg-red-50 text-red-600 rounded-lg border border-red-200 text-sm">
            {{ errorMsg }}
          </div>
        </form>
      </div>
    </div>

    <!-- Consola de Logs (Modal Flotante) -->
    <div v-if="showLogsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-75 backdrop-blur-sm p-4">
      <div class="bg-slate-900 border border-slate-700 w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col h-[70vh]">
        <div class="px-4 py-3 bg-slate-800 border-b border-slate-700 flex justify-between items-center">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="ml-4 text-xs font-mono text-slate-400 font-bold tracking-wider">BIOJAVA CONSOLE</span>
          </div>
          <button v-if="!isLoading" @click="closeLogs" class="text-slate-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="flex-1 p-4 overflow-y-auto bg-black font-mono text-xs sm:text-sm text-green-400 whitespace-pre-wrap break-all" ref="logsContainer">
          <div v-for="(log, idx) in consoleLogs" :key="idx" class="mb-1">{{ log }}</div>
          <div v-if="isLoading" class="mt-4 flex items-center text-slate-500">
            <span class="animate-pulse">Procesando...</span>
            <span class="ml-2 animate-bounce">_</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import ServerFilePicker from '@/components/ServerFilePicker.vue';

const isLoading = ref(false);
const errorMsg = ref('');
const resultHtmlUrl = ref('');
const selectedPrecomputed = ref('');

const showLogsModal = ref(false);
const consoleLogs = ref<string[]>([]);
const logsContainer = ref<HTMLElement | null>(null);

// Directorio base relativo a biojava-runner.js para guardar los outputs
const PUBLIC_PATH = 'public/biojava_outputs/';
// URL base dinámica: busca el backend en la misma IP/Host desde donde se abrió SIVAR
const BACKEND_URL = `http://${window.location.hostname}:3001`;

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

const closeLogs = () => {
  showLogsModal.value = false;
};

const scrollToBottom = async () => {
  await nextTick();
  if (logsContainer.value) {
    logsContainer.value.scrollTop = logsContainer.value.scrollHeight;
  }
};

const loadPrecomputed = () => {
  const baseDir = '/Users/estuvar4/Documents/2. software/17.biojava';

  if (selectedPrecomputed.value === 'CC1940_vs_R570') {
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
  } else if (selectedPrecomputed.value === 'R570_vs_Spont_sim') {
    form.value = {
      ...form.value,
      gff1: `${baseDir}/dataset_genomico/genomas/R570_sim/R570.gff`,
      gff2: `${baseDir}/dataset_genomico/genomas/Spont_sim/Spont.gff`,
      collinearity: `${baseDir}/dataset_genomico/comparativas/R570_vs_Spont_sim/R570_vs_Spont.collinearity`,
      cds1: `${baseDir}/dataset_genomico/genomas/R570_sim/R570.cds.fa`,
      cds2: `${baseDir}/dataset_genomico/genomas/Spont_sim/Spont.cds.fa`,
      prot1: ``,
      prot2: ``,
      vcf: ``,
      kaks: `${baseDir}/dataset_genomico/comparativas/R570_vs_Spont_sim/R570_vs_Spont.kaks.tsv`,
      name1: 'R570',
      name2: 'Spontaneum',
      organism: 'Saccharum'
    };
  }
};

const runAnalysis = async () => {
  isLoading.value = true;
  errorMsg.value = '';
  consoleLogs.value = ['Inicializando análisis BioJava...'];
  showLogsModal.value = true;
  resultHtmlUrl.value = '';
  
  // Generar la carpeta de salida dinámicamente basada en los nombres
  const safeName1 = form.value.name1.trim().replace(/\s+/g, '_');
  const safeName2 = form.value.name2.trim().replace(/\s+/g, '_');
  const subfolder = `comp_gen/${safeName1}_vs_${safeName2}`;
  
  form.value.outputHTML = `${PUBLIC_PATH}${subfolder}/visor_sintenia.html`;
  form.value.outputFile = `${PUBLIC_PATH}${subfolder}/reporte_comparativo.tsv`;

  try {
    const response = await fetch(`${BACKEND_URL}/run-comp-gen`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value)
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data.error || 'Error desconocido al iniciar comp-gen');
    }

    // Comenzar a escuchar SSE
    const eventSource = new EventSource(`${BACKEND_URL}/comp-gen-logs/${data.jobId}`);

    eventSource.addEventListener('log', (e) => {
      consoleLogs.value.push(JSON.parse(e.data));
      scrollToBottom();
    });

    eventSource.addEventListener('success', (e) => {
      consoleLogs.value.push('\n[OK] Análisis finalizado correctamente.');
      scrollToBottom();
      isLoading.value = false;
      eventSource.close();
      
      // Obtener el HTML desde el servidor Node (no de Vite)
      resultHtmlUrl.value = `${BACKEND_URL}/${PUBLIC_PATH}${subfolder}/visor_sintenia.html`.replace(/([^:]\/)\/+/g, "$1");
      
      // Cerrar modal automáticamente después de unos segundos si todo salió bien
      setTimeout(() => {
        if (!errorMsg.value) {
            closeLogs();
        }
      }, 2000);
    });

    eventSource.addEventListener('error', (e) => {
      const errData = JSON.parse(e.data);
      consoleLogs.value.push(`\n[ERROR] El proceso falló: ${errData.message}`);
      scrollToBottom();
      errorMsg.value = 'El proceso falló. Revisa la consola para más detalles.';
      isLoading.value = false;
      eventSource.close();
    });

  } catch (err: any) {
    errorMsg.value = err.message;
    console.error(err);
    consoleLogs.value.push(`[ERROR] ${err.message}`);
    isLoading.value = false;
  }
};

const resetForm = () => {
  resultHtmlUrl.value = '';
};
</script>
