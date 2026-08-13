<template>
  <div class="space-y-6 w-full px-2 sm:px-4 pb-12">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-8">
      <div class="flex items-center space-x-3">
        <router-link :to="{ name: 'bioinformatica.show' }" class="flex items-center text-slate-500 hover:text-teal-600 transition-colors group">
          <div class="p-2 bg-slate-50 group-hover:bg-teal-50 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
        </router-link>
        <div>
          <h1 class="text-3xl font-bold text-slate-800">Servidor BLAST Nativo</h1>
          <p class="text-slate-500 mt-2">Alineamiento de secuencias integrado en SIVAR</p>
        </div>
      </div>
    </div>

    <!-- Formulario BLAST -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="p-6 md:p-8 bg-white flex items-center justify-between font-bold text-slate-800 text-xl border-b border-slate-100">Configurar Análisis</div>

      <div class="p-6 md:p-8 bg-white">
        <form @submit.prevent="runBlast" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Secuencia de Consulta (FASTA) *</label>
              <textarea
                v-model="form.sequence"
                rows="6"
                class="w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm font-mono p-3"
                placeholder=">Mi_Secuencia&#10;ATGCGTACGTAGCTAGCTAGC..."
                required
              ></textarea>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Programa BLAST *</label>
              <select
                v-model="form.program"
                class="w-full border-slate-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-1.5"
                required
              >
                <option value="blastn">BLASTn (Nucleótidos vs Nucleótidos)</option>
                <option value="blastp">BLASTp (Proteínas vs Proteínas)</option>
                <option value="blastx">BLASTx (Nucleótidos traducidos vs Proteínas)</option>
                <option value="tblastn">tBLASTn (Proteínas vs Nucleótidos traducidos)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Base de Datos (Genoma) *</label>
              <select
                v-model="form.database"
                class="w-full border-slate-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-1.5"
                required
              >
                <option value="/biodata5/proyectos/genomica_comparativa/dataset_genomico/genomas/CC-01-1940/CC-01-1940.fasta">CC 01-1940</option>
                <option value="/biodata5/proyectos/genomica_comparativa/dataset_genomico/genomas/R570/R570.fasta">R570</option>
                <option value="/biodata5/proyectos/genomica_comparativa/dataset_genomico/genomas/Spont_sim/Spont.fasta">Spontaneum</option>
              </select>
            </div>

            <div class="md:col-span-2 mt-2">
              <label class="block text-xs font-medium text-slate-600 mb-1">E-value Threshold (Opcional)</label>
              <input
                type="number"
                step="0.00001"
                v-model="form.expect"
                class="w-1/2 border-slate-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-1.5"
                placeholder="1e-5"
              />
            </div>
          </div>

          <!-- Botón de Ejecución -->
          <div class="pt-6 border-t flex justify-end">
            <BaseButton type="submit" variant="success" size="md" :loading="isLoading" class="!bg-teal-600 hover:!bg-teal-700 !px-8">
              <template #icon-left>
                <svg v-if="isLoading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
              </template>
              {{ isLoading ? "Procesando..." : "Ejecutar BLAST" }}
            </BaseButton>
          </div>

          <div v-if="errorMsg" class="mt-4 p-3 bg-red-50 text-red-600 rounded-lg border border-red-200 text-sm">
            {{ errorMsg }}
          </div>
        </form>
      </div>
    </div>

    <!-- Resultados -->
    <div v-if="results" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="p-6 bg-teal-50 border-b border-teal-100">
        <h2 class="text-xl font-bold text-teal-800">Resultados de Alineamiento</h2>
      </div>
      <div class="p-6 overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
          <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
            <tr>
              <th class="py-3 px-4">Hit</th>
              <th class="py-3 px-4">Score</th>
              <th class="py-3 px-4">E-value</th>
              <th class="py-3 px-4">Identidad</th>
              <th class="py-3 px-4">Longitud</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(hit, idx) in results.BlastOutput2[0].report.results.search.hits" :key="idx">
              <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="py-3 px-4 font-mono text-xs text-teal-700 font-bold">{{ hit.description[0].id }}</td>
                <td class="py-3 px-4">{{ hit.hsps[0].bit_score }}</td>
                <td class="py-3 px-4 text-orange-600 font-mono">{{ hit.hsps[0].evalue }}</td>
                <td class="py-3 px-4">
                  {{ hit.hsps[0].identity }} / {{ hit.hsps[0].align_len }} ({{ Math.round((hit.hsps[0].identity / hit.hsps[0].align_len) * 100) }}%)
                </td>
                <td class="py-3 px-4">{{ hit.len }}</td>
              </tr>
              <!-- Opcional: Mostrar alineamiento expandido -->
              <tr class="bg-slate-50/50">
                <td colspan="5" class="p-4 border-b border-slate-200">
                  <div class="font-mono text-xs overflow-x-auto bg-slate-900 text-slate-300 p-4 rounded-lg">
                    <div class="mb-2 text-slate-400">Query: {{ hit.hsps[0].query_from }} al {{ hit.hsps[0].query_to }}</div>
                    <div class="whitespace-pre">{{ hit.hsps[0].qseq }}</div>
                    <div class="whitespace-pre text-teal-400">{{ hit.hsps[0].midline }}</div>
                    <div class="whitespace-pre">{{ hit.hsps[0].hseq }}</div>
                    <div class="mt-2 text-slate-400">Subject: {{ hit.hsps[0].hit_from }} al {{ hit.hsps[0].hit_to }}</div>
                  </div>
                </td>
              </tr>
            </template>
            <tr v-if="!results.BlastOutput2[0].report.results.search.hits || results.BlastOutput2[0].report.results.search.hits.length === 0">
              <td colspan="5" class="text-center py-8 text-slate-500">No se encontraron hits significativos.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Consola de Logs -->
    <div v-if="showLogsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-75 backdrop-blur-sm p-4">
      <div class="bg-slate-900 border border-slate-700 w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col h-[70vh]">
        <div class="px-4 py-3 bg-slate-800 border-b border-slate-700 flex justify-between items-center">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="ml-4 text-xs font-mono text-slate-400 font-bold tracking-wider">BLAST CONSOLE</span>
          </div>
          <BaseButton v-if="!isLoading" variant="ghost" size="xs" iconOnly @click="closeLogs" class="!text-slate-400 hover:!text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </BaseButton>
        </div>
        <div class="flex-1 p-4 overflow-y-auto bg-black font-mono text-xs sm:text-sm text-green-400 whitespace-pre-wrap break-all" ref="logsContainer">
          <div v-for="(log, idx) in consoleLogs" :key="idx" class="mb-1">{{ log }}</div>
          <div v-if="isLoading" class="mt-4 flex items-center text-slate-500">
            <span class="animate-pulse">Procesando alineamiento...</span>
            <span class="ml-2 animate-bounce">_</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from "vue";

const isLoading = ref(false);
const errorMsg = ref("");
const results = ref<any>(null);

const showLogsModal = ref(false);
const consoleLogs = ref<string[]>([]);
const logsContainer = ref<HTMLElement | null>(null);

const BACKEND_URL = import.meta.env.VITE_BLAST_API_URL || `http://${window.location.hostname}:3002`;

const form = ref({
  sequence: "",
  program: "blastn",
  database: "/biodata5/proyectos/genomica_comparativa/dataset_genomico/genomas/CC-01-1940/CC-01-1940.fasta",
  expect: 1e-5
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

const fetchResults = async (url: string) => {
  try {
    const res = await fetch(`${BACKEND_URL}${url}`);
    if (!res.ok) throw new Error("Error obteniendo JSON");
    results.value = await res.json();
  } catch (err) {
    console.error(err);
    errorMsg.value = "Error al parsear resultados de BLAST.";
  }
};

const runBlast = async () => {
  isLoading.value = true;
  errorMsg.value = "";
  results.value = null;
  consoleLogs.value = ["Inicializando motor nativo de BLAST..."];
  showLogsModal.value = true;

  // Si estamos en local (Mac) para desarrollo, sobreescribir base de datos a un dummy
  let finalDatabase = form.value.database;
  if (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1") {
    finalDatabase = "local_db";
  }

  try {
    const response = await fetch(`${BACKEND_URL}/run-blast`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ...form.value, database: finalDatabase })
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data.error || "Error desconocido al iniciar BLAST");
    }

    const eventSource = new EventSource(`${BACKEND_URL}/blast-logs/${data.jobId}`);

    eventSource.addEventListener("log", (e) => {
      consoleLogs.value.push(JSON.parse(e.data));
      scrollToBottom();
    });

    eventSource.addEventListener("success", async (e) => {
      const eData = JSON.parse(e.data);
      consoleLogs.value.push("\n[OK] Alineamiento finalizado correctamente.");
      scrollToBottom();
      isLoading.value = false;
      eventSource.close();

      if (eData.resultUrl) {
        await fetchResults(eData.resultUrl);
      }

      setTimeout(() => {
        if (!errorMsg.value) closeLogs();
      }, 1500);
    });

    eventSource.addEventListener("error", (e) => {
      const errData = JSON.parse(e.data);
      consoleLogs.value.push(`\n[ERROR] El proceso falló: ${errData.message}`);
      scrollToBottom();
      errorMsg.value = "El proceso falló. Revisa la consola para más detalles.";
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
</script>
