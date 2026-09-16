<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="bg-white rounded-3xl max-w-6xl w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all flex flex-col max-h-[92vh]">
        <!-- Header -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white flex items-center justify-between">
          <div class="space-y-1">
            <div class="flex items-center space-x-2">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                Ficha 360° • Código: {{ detalle?.proyecto?.cd_cntble || 'S/N' }}
              </span>
              <span class="text-xs text-slate-300 font-medium">ID: {{ detalle?.proyecto?.id_prycto }}</span>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight line-clamp-1">
              {{ detalle?.proyecto?.nm_prycto || 'Cargando Proyecto...' }}
            </h2>
            <p class="text-xs text-slate-300">
              Programa: {{ detalle?.proyecto?.nombre_programa }} | Área de Trabajo: {{ detalle?.proyecto?.nombre_area_trbjo }}
            </p>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-slate-400 hover:text-white rounded-full hover:bg-white/10 transition-colors cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Tab Navigation Bar -->
        <div class="px-6 pt-3 bg-slate-50 border-b border-slate-200/80 flex items-center space-x-2">
          <button
            @click="activeTab = 'summary'"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 cursor-pointer flex items-center gap-1.5"
            :class="activeTab === 'summary' ? 'border-emerald-600 text-emerald-800 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'"
          >
            📋 Resumen 360° & Inventario
          </button>
          <button
            @click="activeTab = 'isoproductivity'"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 cursor-pointer flex items-center gap-1.5"
            :class="activeTab === 'isoproductivity' ? 'border-emerald-600 text-emerald-800 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'"
          >
            📈 Isoproductividad & Rendimiento
          </button>
          <button
            @click="activeTab = 'stability'"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 cursor-pointer flex items-center gap-1.5"
            :class="activeTab === 'stability' ? 'border-emerald-600 text-emerald-800 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'"
          >
            🎯 Estabilidad Agronómica (AMMI & GGE)
          </button>
        </div>

        <!-- Body Content -->
        <div v-if="isLoading" class="p-16 flex flex-col items-center justify-center space-y-3 text-slate-400">
          <div class="relative w-10 h-10">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-100 animate-pulse"></div>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-600 border-t-transparent animate-spin"></div>
          </div>
          <span class="text-xs font-bold text-slate-600">Cargando consolidado 360° del proyecto...</span>
        </div>

        <div v-else class="p-6 space-y-6 overflow-y-auto flex-1">
          <!-- Tab 1: Resumen 360° -->
          <template v-if="activeTab === 'summary'">
            <!-- 1. Tarjetas KPI de Resumen del Proyecto -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              <!-- Total Flores -->
              <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
                <div class="text-[10px] font-extrabold uppercase text-emerald-800">Floraciones</div>
                <div class="text-xl font-black text-emerald-950 mt-1">
                  {{ detalle?.floracion_stats?.total_flores || 0 }} <span class="text-xs font-semibold text-emerald-700">flores</span>
                </div>
                <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">
                  🌸 {{ detalle?.floracion_stats?.flores_libres || 0 }} libres • 💼 {{ detalle?.floracion_stats?.flores_bolsa_comun || 0 }} en bolsa
                </div>
              </div>

              <!-- Viveros -->
              <div class="bg-sky-50/60 p-4 rounded-2xl border border-sky-100">
                <div class="text-[10px] font-extrabold uppercase text-sky-800">Viveros Registrados</div>
                <div class="text-xl font-black text-sky-950 mt-1">
                  {{ detalle?.viveros?.length || 0 }} <span class="text-xs font-semibold text-sky-700">viveros</span>
                </div>
                <div class="text-[10px] text-sky-700 font-semibold mt-0.5">
                  🏡 Siembras y parcelas activas
                </div>
              </div>

              <!-- Cruzamientos -->
              <div class="bg-purple-50/60 p-4 rounded-2xl border border-purple-100">
                <div class="text-[10px] font-extrabold uppercase text-purple-800">Cruzamientos</div>
                <div class="text-xl font-black text-purple-950 mt-1">
                  {{ detalle?.cruzamientos_count || 0 }} <span class="text-xs font-semibold text-purple-700">combinaciones</span>
                </div>
                <div class="text-[10px] text-purple-700 font-semibold mt-0.5">
                  🧬 Familias y progenitores
                </div>
              </div>

              <!-- Ensayos -->
              <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-100">
                <div class="text-[10px] font-extrabold uppercase text-amber-800">Ensayos de Campo</div>
                <div class="text-xl font-black text-amber-950 mt-1">
                  {{ detalle?.ensayos_count || 0 }} <span class="text-xs font-semibold text-amber-700">ensayos</span>
                </div>
                <div class="text-[10px] text-amber-700 font-semibold mt-0.5">
                  📊 Pruebas agronómicas
                </div>
              </div>
            </div>

            <!-- 2. Equipo de Trabajo y Permisos -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
              <div class="flex items-center justify-between">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  Equipo de Trabajo del Proyecto ({{ detalle?.permisos?.length || 0 }})
                </h3>
              </div>

              <div v-if="!detalle?.permisos || detalle.permisos.length === 0" class="text-xs text-slate-400 italic py-2">
                Sin administradores ni editores asignados explícitamente a este proyecto.
              </div>

              <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="u in detalle.permisos"
                  :key="u.usuario_id"
                  class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 flex items-center justify-between"
                >
                  <div>
                    <div class="font-bold text-xs text-slate-800">{{ u.nombre }}</div>
                    <div class="text-[10px] text-slate-500 truncate max-w-[180px]">{{ u.email || u.cargo || 'Investigador' }}</div>
                  </div>
                  <span
                    class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase border"
                    :class="{
                      'bg-amber-100 text-amber-900 border-amber-200': u.rol === 'ADMIN',
                      'bg-blue-100 text-blue-900 border-blue-200': u.rol === 'EDITOR',
                      'bg-slate-200 text-slate-800 border-slate-300': u.rol === 'VIEWER'
                    }"
                  >
                    {{ u.rol === 'ADMIN' ? '👑 Admin' : u.rol === 'EDITOR' ? '✏️ Editor' : '👁️ Lector' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- 3. Criterios de Selección / Caracteres Target -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Objetivos / Caracteres Objetivo de Selección
              </h3>

              <div v-if="!detalle?.caracteres || detalle.caracteres.length === 0" class="text-xs text-slate-400 italic py-2">
                No hay características ni criterios específicos parametrizados aún para este proyecto.
              </div>

              <div v-else class="flex flex-wrap gap-2">
                <span
                  v-for="c in detalle.caracteres"
                  :key="c"
                  class="px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-200/80"
                >
                  🎯 {{ c }}
                </span>
              </div>
            </div>

            <!-- 4. Viveros Vinculados -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Viveros Vinculados al Proyecto ({{ detalle?.viveros?.length || 0 }})
              </h3>

              <div v-if="!detalle?.viveros || detalle.viveros.length === 0" class="text-xs text-slate-400 italic py-2">
                No hay viveros registrados asociados a este proyecto en la base de datos.
              </div>

              <div v-else class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs border-collapse">
                  <thead class="bg-slate-100/70 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                      <th class="px-4 py-2.5">Identificador Único</th>
                      <th class="px-4 py-2.5">Nombre / Vivero</th>
                      <th class="px-4 py-2.5">Ambiente</th>
                      <th class="px-4 py-2.5">Responsable</th>
                      <th class="px-4 py-2.5">Fecha Siembra</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    <tr v-for="v in detalle.viveros" :key="v.id" class="hover:bg-slate-50 transition-colors">
                      <td class="px-4 py-2.5 font-bold text-slate-800 font-mono">{{ v.identificador_unico }}</td>
                      <td class="px-4 py-2.5 text-slate-700">{{ v.nombre || 'Sin nombre' }}</td>
                      <td class="px-4 py-2.5 text-slate-600">{{ v.ambiente || 'N/A' }}</td>
                      <td class="px-4 py-2.5 text-slate-800 font-bold">
                        {{ v.resp_nombre ? `${v.resp_nombre} ${v.resp_apellido}` : 'Sin responsable' }}
                      </td>
                      <td class="px-4 py-2.5 text-slate-500">{{ v.fecha_siembra || 'N/A' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </template>

          <!-- Tab 2: Isoproductividad & Rendimiento -->
          <template v-else-if="activeTab === 'isoproductivity'">
            <ProjectIsoproductivityChart :data="detalle?.isoproductividad || null" />
          </template>

          <!-- Tab 3: Estabilidad Agronómica (AMMI & GGE Biplot) -->
          <template v-else-if="activeTab === 'stability'">
            <ProjectStabilityAnalysis v-if="proyectoId" :project-id="proyectoId" />
          </template>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-end">
          <button
            @click="closeModal"
            class="px-5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 rounded-xl border border-slate-200 transition-all cursor-pointer shadow-sm"
          >
            Cerrar Ficha
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import projectManagementService from "@/services/projectManagement.services";
import ProjectIsoproductivityChart from "@/components/admin/ProjectIsoproductivityChart.vue";
import ProjectStabilityAnalysis from "@/components/admin/ProjectStabilityAnalysis.vue";
import { useToast } from "vue-toastification";

const props = defineProps<{
  isOpen: boolean;
  proyectoId: number | null;
}>();

const emit = defineEmits(["close"]);
const toast = useToast();

const activeTab = ref<"summary" | "isoproductivity" | "stability">("summary");
const detalle = ref<any>(null);
const isLoading = ref(false);

const closeModal = () => {
  emit("close");
};

const loadDetalleProyecto = async () => {
  if (!props.proyectoId) return;
  isLoading.value = true;
  try {
    const res = await projectManagementService.getDetalleProyecto(props.proyectoId);
    detalle.value = res.data || null;
  } catch (err) {
    console.error("Error al cargar detalle del proyecto:", err);
    toast.error("Error al consultar la Ficha 360° del proyecto.");
  } finally {
    isLoading.value = false;
  }
};

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal && props.proyectoId) {
      activeTab.value = "summary";
      detalle.value = null;
      loadDetalleProyecto();
    }
  }
);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
