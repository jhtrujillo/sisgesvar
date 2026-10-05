<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-5 animate-fade-in">
    <div class="bg-white rounded-3xl shadow-2xl max-w-6xl w-full max-h-[92vh] flex flex-col overflow-hidden border border-slate-100">
      <!-- Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-slate-900 via-slate-800 to-cenicana-900 text-white flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-emerald-500/20 rounded-2xl border border-emerald-400/30 text-emerald-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-base font-extrabold tracking-tight flex items-center gap-2">
              Mapa Físico de Parcelas
              <span v-if="experimento" class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                {{ experimento.tpo_ensyo === "F" ? "Familias (F)" : "Individual (I)" }}
              </span>
            </h3>
            <p class="text-xs text-slate-300 font-medium truncate max-w-xl">
              {{ experimento?.nm_prycto || "Cargando experimento..." }} | Serie {{ experimento?.srie }} | {{ experimento?.estdo }}
            </p>
          </div>
        </div>

        <button @click="close" class="p-2 text-slate-300 hover:text-white rounded-xl hover:bg-white/10 transition-all cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Stats Bar & Toolbar -->
      <div class="px-6 py-3 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-1.5 font-bold text-slate-700">
            <span class="w-3 h-3 rounded-md bg-emerald-500 inline-block"></span>
            Total Parcelas: <span class="font-mono text-emerald-700">{{ stats.totalParcelas || parcelas.length }}</span>
          </div>
          <div class="flex items-center gap-1.5 font-bold text-slate-700">
            <span class="w-3 h-3 rounded-md bg-blue-500 inline-block"></span>
            Tratamientos: <span class="font-mono text-blue-700">{{ stats.totalTratamientos }}</span>
          </div>
          <div class="flex items-center gap-1.5 font-bold text-slate-700">
            <span class="w-3 h-3 rounded-md bg-purple-500 inline-block"></span>
            Testigos: <span class="font-mono text-purple-700">{{ stats.totalTestigos }}</span>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchTerm"
            placeholder="Buscar por tratamiento o entrada..."
            class="px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white w-48 focus:ring-2 focus:ring-emerald-100"
          />
          <select v-model="filterType" class="px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white">
            <option value="ALL">Todos los tipos</option>
            <option value="TRATAMIENTOS">Solo Tratamientos</option>
            <option value="TESTIGOS">Solo Testigos</option>
          </select>
        </div>
      </div>

      <!-- Main Body: Layout Matrix -->
      <div class="p-6 overflow-y-auto flex-1 space-y-6 bg-slate-50/50">
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-16 text-slate-400">
          <svg class="animate-spin h-8 w-8 text-cenicana mb-3" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <span class="text-xs font-semibold">Cargando distribución de parcelas...</span>
        </div>

        <div v-else-if="filteredParcelas.length === 0" class="text-center py-16 text-slate-400">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 mx-auto mb-2 text-slate-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
            />
          </svg>
          <p class="text-xs font-bold text-slate-600">No hay parcelas registradas en el diseño estadístico</p>
          <p class="text-[11px] text-slate-400 mt-1">Asegúrese de haber generado el diseño estadístico para este experimento.</p>
        </div>

        <!-- Render Repeticiones & Bloques Grid -->
        <div v-else class="space-y-6">
          <div v-for="(group, repKey) in parcelasByRep" :key="repKey" class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
              <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cenicana"></span>
                Repetición {{ repKey }}
              </h4>
              <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full"> {{ group.length }} parcelas </span>
            </div>

            <!-- Grid of Plots -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
              <div
                v-for="p in group"
                :key="p.id_dissalida_det || p.prcla"
                @click="selectedPlot = p"
                class="p-3 rounded-2xl border transition-all cursor-pointer hover:shadow-md hover:-translate-y-0.5 relative group"
                :class="getPlotClass(p)"
              >
                <!-- Badge Top -->
                <div class="flex items-center justify-between text-[10px] font-bold mb-1 opacity-80">
                  <span>P #{{ p.prcla || p.entrda }}</span>
                  <span v-if="p.block">B-{{ p.block }}</span>
                </div>

                <!-- Treatment Name -->
                <div class="text-xs font-extrabold truncate text-slate-900 font-mono" :title="p.trtmnto">
                  {{ p.trtmnto || "Entrada " + p.entrda }}
                </div>

                <!-- Footer Stats -->
                <div class="mt-2 pt-1 border-t border-black/5 flex items-center justify-between text-[9.5px] font-semibold opacity-75">
                  <span>Entrada {{ p.entrda }}</span>
                  <span v-if="p.tstgo !== 'No'" class="px-1.5 py-0.2 rounded bg-purple-200 text-purple-900 font-bold">TESTIGO</span>
                  <span v-else class="text-slate-500">TRAT.</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-100 border border-blue-300"></span> Tratamiento</span>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-100 border border-purple-300"></span> Testigo Fijo</span>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-100 border border-amber-300"></span> Testigo Móvil</span>
        </div>
        <button @click="close" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-all cursor-pointer">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import api from "@/services/api";
import urls from "@/services/urls";

const props = defineProps<{
  isOpen: boolean;
  idDsnoEnc?: number | string | null;
  idPr?: number | string | null;
  srie?: number | string | null;
  estdo?: string | null;
}>();

const emit = defineEmits(["close"]);

const isLoading = ref(false);
const experimento = ref<any>(null);
const parcelas = ref<any[]>([]);
const stats = ref<any>({ totalParcelas: 0, totalTratamientos: 0, totalTestigos: 0 });
const searchTerm = ref("");
const filterType = ref("ALL");
const selectedPlot = ref<any>(null);

const close = () => {
  emit("close");
};

const fetchData = async () => {
  if (!props.isOpen) return;
  isLoading.value = true;
  experimento.value = null;
  parcelas.value = [];

  try {
    let res: any = null;
    if (props.idDsnoEnc) {
      res = await api.get(`${urls.API_URL}getMapaParcelas/${props.idDsnoEnc}`, {}, true);
    } else if (props.idPr && props.srie && props.estdo) {
      res = await api.get(`${urls.API_URL}getMapaParcelasProject/${props.idPr}/${props.srie}/${encodeURIComponent(props.estdo)}`, {}, true);
    }

    const data = res?.data || res;
    if (data && data.success) {
      experimento.value = data.experimento || data.experimentos?.[0] || null;
      parcelas.value = data.parcelas || [];
      stats.value = data.stats || {
        totalParcelas: parcelas.value.length,
        totalTratamientos: parcelas.value.filter((p) => p.tstgo === "No").length,
        totalTestigos: parcelas.value.filter((p) => p.tstgo !== "No").length
      };
    }
  } catch (error) {
    console.error("Error al cargar mapa de parcelas:", error);
  } finally {
    isLoading.value = false;
  }
};

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) fetchData();
  }
);

const filteredParcelas = computed(() => {
  return parcelas.value.filter((p) => {
    const matchSearch =
      !searchTerm.value ||
      String(p.trtmnto || "")
        .toLowerCase()
        .includes(searchTerm.value.toLowerCase()) ||
      String(p.entrda || "").includes(searchTerm.value) ||
      String(p.prcla || "").includes(searchTerm.value);

    const matchType =
      filterType.value === "ALL" || (filterType.value === "TRATAMIENTOS" && p.tstgo === "No") || (filterType.value === "TESTIGOS" && p.tstgo !== "No");

    return matchSearch && matchType;
  });
});

const parcelasByRep = computed(() => {
  const groups: Record<string, any[]> = {};
  filteredParcelas.value.forEach((p) => {
    const rep = p.rptcion || 1;
    if (!groups[rep]) groups[rep] = [];
    groups[rep].push(p);
  });
  return groups;
});

const getPlotClass = (p: any) => {
  if (p.tstgo === "Si") {
    return "bg-purple-50/80 border-purple-200 text-purple-900 hover:border-purple-400";
  } else if (p.tstgo === "Movil") {
    return "bg-amber-50/80 border-amber-200 text-amber-900 hover:border-amber-400";
  } else {
    return "bg-blue-50/80 border-blue-200 text-blue-900 hover:border-blue-400";
  }
};
</script>
