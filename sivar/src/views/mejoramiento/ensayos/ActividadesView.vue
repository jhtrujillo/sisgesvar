<script setup>
import { ref, watch, onMounted } from "vue";
import debounce from "lodash/debounce";
import { EnsayosService } from "@/services/ensayos.services";
import EnsayosNavComponent from "@/components/EnsayosNavComponent.vue";

// Local State
const actividades = ref({ data: [], links: [] });
const users = ref([]);
const kpis = ref({
  total_historico: 0,
  operaciones_hoy: 0,
  estandarizaciones: 0,
  ediciones_celda: 0
});
const search = ref("");
const filterAccion = ref("");
const filterUserId = ref("");
const currentPage = ref(1);
const isLoading = ref(true);

const loadActividades = async () => {
  isLoading.value = true;
  try {
    const response = await EnsayosService.getActividades({
      search: search.value,
      accion: filterAccion.value,
      user_id: filterUserId.value,
      page: currentPage.value
    });
    actividades.value = response.data.actividades;
    users.value = response.data.users;
    kpis.value = response.data.kpis;
  } catch (error) {
    console.error("Error loading activity log:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadActividades();
});

const updateFilters = debounce(() => {
  currentPage.value = 1;
  loadActividades();
}, 300);

watch([search, filterAccion, filterUserId], () => updateFilters());

const getActionStyle = (accion) => {
  if (accion === "IMPORTACION") return "bg-emerald-100 text-emerald-700 border-emerald-200";
  if (accion === "EDICION_CELDA") return "bg-amber-100 text-amber-700 border-amber-200";
  if (accion === "ESTANDARIZACION_MASIVA") return "bg-orange-100 text-orange-700 border-orange-200";
  if (accion === "EDICION_CATALOGO") return "bg-indigo-100 text-indigo-700 border-indigo-200";
  if (accion === "FUSION_CATALOGO") return "bg-rose-100 text-rose-700 border-rose-200";
  if (accion === "SUBIDA_ADJUNTO") return "bg-emerald-50 text-emerald-700 border-emerald-200";
  if (accion === "ELIMINACION_ADJUNTO") return "bg-rose-50 text-rose-700 border-rose-200";
  return "bg-slate-100 text-slate-700 border-slate-200";
};

const getActionIcon = (accion) => {
  if (accion === "IMPORTACION") return "📥";
  if (accion === "EDICION_CELDA") return "✏️";
  if (accion === "ESTANDARIZACION_MASIVA") return "🧹";
  if (accion === "EDICION_CATALOGO") return "📁";
  if (accion === "FUSION_CATALOGO") return "🔗";
  if (accion === "SUBIDA_ADJUNTO") return "📎";
  if (accion === "ELIMINACION_ADJUNTO") return "🗑️";
  return "⚡";
};

const getActionTitle = (accion) => {
  if (accion === "IMPORTACION") return "📥 Importación Masiva";
  if (accion === "EDICION_CELDA") return "✏️ Edición en Excel";
  if (accion === "ESTANDARIZACION_MASIVA") return "🧹 Estandarización Inteligente";
  if (accion === "EDICION_CATALOGO") return "📁 Edición Catálogo";
  if (accion === "FUSION_CATALOGO") return "🔗 Fusión de Términos";
  if (accion === "SUBIDA_ADJUNTO") return "📎 Subida Adjunto";
  if (accion === "ELIMINACION_ADJUNTO") return "🗑️ Eliminó Adjunto";
  return accion;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat("es-ES", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit"
  }).format(date);
};

// Expanded state for detail modals or in-card details
const expandedIds = ref([]);
const toggleExpand = (id) => {
  if (expandedIds.value.includes(id)) {
    expandedIds.value = expandedIds.value.filter((i) => i !== id);
  } else {
    expandedIds.value.push(id);
  }
};

const changePage = (url) => {
  if (url) {
    try {
      const urlObj = new URL(url);
      const pageParam = urlObj.searchParams.get("page");
      currentPage.value = pageParam ? parseInt(pageParam) : 1;
      loadActividades();
    } catch (e) {
      // Fallback for relative paths
      const match = url.match(/page=(\d+)/);
      if (match && match[1]) {
        currentPage.value = parseInt(match[1]);
        loadActividades();
      }
    }
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-50/50 p-4 sm:p-8 font-sans w-full max-w-full min-w-0 overflow-x-hidden">
    <!-- Overlay Loading State -->
    <div v-if="isLoading" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300">
      <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center gap-3 border border-slate-100">
        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-slate-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <span class="text-slate-800 font-bold tracking-wide animate-pulse">Cargando bitácora de actividades...</span>
      </div>
    </div>

    <div class="max-w-5xl w-full mx-auto space-y-6 min-w-0">
      <!-- Shared Navigation tabs -->
      <EnsayosNavComponent />

      <div class="flex items-center space-x-3 bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm">
        <div class="p-2.5 bg-slate-800 text-white rounded-xl shadow-inner">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-2xl font-black leading-tight text-slate-800">Historial & Auditoría</h2>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Seguimiento cronológico de operaciones, modificaciones y cargas del sistema.</p>
        </div>
      </div>

      <!-- 📊 PANEL DE MÉTRICAS Y RESUMEN EN TIEMPO REAL -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total General -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-200">
          <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">📊</div>
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total General</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ kpis.total_historico }}</p>
          </div>
        </div>

        <!-- Card 2: Operaciones Hoy -->
        <div
          class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-200 border-l-4 border-l-sky-500"
        >
          <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">📅</div>
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hechos Hoy</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ kpis.operaciones_hoy }}</p>
          </div>
        </div>

        <!-- Card 3: Cambios en Celdas -->
        <div
          class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-200 border-l-4 border-l-amber-500"
        >
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">✏️</div>
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ediciones</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ kpis.ediciones_celda }}</p>
          </div>
        </div>

        <!-- Card 4: Limpiezas Masivas -->
        <div
          class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-200 border-l-4 border-l-orange-500"
        >
          <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">🧹</div>
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estandarizaciones</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ kpis.estandarizaciones }}</p>
          </div>
        </div>
      </div>

      <!-- Advanced Multi-Filter Bar -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
        <!-- General Search -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <input
            type="text"
            v-model="search"
            placeholder="Filtrar descripción..."
            class="block w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition placeholder-slate-400 font-medium"
          />
        </div>

        <!-- Action Filter -->
        <div>
          <select
            v-model="filterAccion"
            class="block w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition font-bold text-slate-700 cursor-pointer"
          >
            <option value="">⚡ Todas las Acciones</option>
            <option value="IMPORTACION">📥 Importaciones Masivas</option>
            <option value="EDICION_CELDA">✏️ Ediciones Manuales</option>
            <option value="ESTANDARIZACION_MASIVA">🧹 Estandarizaciones Inteligentes</option>
            <option value="EDICION_CATALOGO">📁 Ediciones de Catálogos</option>
            <option value="FUSION_CATALOGO">🔗 Fusiones de Términos</option>
            <option value="SUBIDA_ADJUNTO">📎 Subidas de Adjuntos</option>
            <option value="ELIMINACION_ADJUNTO">🗑️ Eliminaciones de Adjuntos</option>
          </select>
        </div>

        <!-- User Filter -->
        <div>
          <select
            v-model="filterUserId"
            class="block w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition font-bold text-slate-700 cursor-pointer"
          >
            <option value="">👤 Todos los Usuarios</option>
            <option v-for="usr in users" :key="usr.id" :value="usr.id">
              {{ usr.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Main Chronological Timeline Container -->
      <div class="relative">
        <!-- Vertical Timeline Anchor Bar -->
        <div class="absolute top-4 left-8 md:left-10 bottom-4 w-0.5 bg-slate-200"></div>

        <div class="space-y-6">
          <div v-for="act in actividades.data" :key="act.id" class="relative pl-16 md:pl-20 flex flex-col transition duration-200 hover:-translate-x-0.5 group">
            <!-- Activity Icon Circle Wrapper -->
            <div
              class="absolute left-4 md:left-6 top-1 w-10 h-10 rounded-xl shadow-md flex items-center justify-center text-xl border-2 z-10 bg-white group-hover:scale-110 transition-transform duration-200"
              :class="getActionStyle(act.accion)"
            >
              {{ getActionIcon(act.accion) }}
            </div>

            <!-- Activity Card Content -->
            <div
              class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col space-y-3 group-hover:shadow-md group-hover:border-slate-300 transition"
            >
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <!-- Badge & Actor -->
                <div class="flex flex-wrap items-center gap-2.5">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black border shadow-inner tracking-wider"
                    :class="getActionStyle(act.accion)"
                  >
                    {{ getActionTitle(act.accion) }}
                  </span>
                  <span class="text-xs font-extrabold text-slate-700 flex items-center gap-1">
                    <span class="text-base opacity-60">👤</span>
                    {{ act.user ? act.user.name : "Sistema Autónomo" }}
                    <span class="text-[9px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded">
                      {{ act.user ? act.user.role : "SYS" }}
                    </span>
                  </span>
                </div>
                <!-- Formatted Datetime -->
                <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest">
                  {{ formatDate(act.created_at) }}
                </div>
              </div>

              <!-- Primary Description text -->
              <p class="text-sm font-bold text-slate-800 leading-snug">
                {{ act.descripcion }}
              </p>

              <!-- Technical payload details inspector -->
              <div v-if="act.detalles && Object.keys(act.detalles).length > 0" class="pt-1">
                <button
                  @click="toggleExpand(act.id)"
                  class="inline-flex items-center text-xs font-black text-slate-500 hover:text-slate-800 transition gap-1 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"
                >
                  <svg
                    class="w-3 h-3 transform transition-transform duration-200"
                    :class="expandedIds.includes(act.id) ? 'rotate-90' : ''"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                  </svg>
                  {{ expandedIds.includes(act.id) ? "Ocultar Metadatos" : "Inspeccionar Detalles Técnicos" }}
                </button>

                <!-- Payload Expandable Block -->
                <div
                  v-if="expandedIds.includes(act.id)"
                  class="mt-3 bg-slate-900 text-slate-300 p-4 rounded-xl overflow-x-auto text-xs font-mono border border-slate-800 shadow-inner animate-fade-in"
                >
                  <div v-for="(val, key) in act.detalles" :key="key" class="flex gap-2 mb-1 last:mb-0">
                    <span class="text-indigo-400 font-black">{{ key }}:</span>
                    <span
                      :class="{
                        'text-amber-400 font-bold': key === 'antes' || key === 'eliminado',
                        'text-emerald-400 font-bold': key === 'ahora' || key === 'conservado'
                      }"
                    >
                      {{ typeof val === "object" ? JSON.stringify(val) : `"${val}"` }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State Check -->
          <div v-if="actividades.data.length === 0" class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm max-w-xl mx-auto">
            <div class="text-5xl mb-4">🕵️‍♂️</div>
            <h3 class="text-base font-black text-slate-800">Sin Actividades Registradas</h3>
            <p class="text-sm text-slate-500 mt-1">No se encontraron rastros de auditoría con los filtros aplicados.</p>
          </div>
        </div>
      </div>

      <!-- Professional Pagination controls -->
      <div v-if="actividades.links && actividades.data.length > 0" class="flex flex-wrap justify-center gap-1.5 py-4">
        <button
          v-for="link in actividades.links"
          :key="link.label"
          @click="changePage(link.url)"
          :disabled="!link.url || link.active"
          class="px-4 py-2 rounded-xl border text-xs font-black transition duration-200 shadow-sm"
          :class="{
            'bg-slate-800 text-white border-slate-800': link.active,
            'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 cursor-pointer': link.url && !link.active,
            'opacity-40 bg-slate-100 text-slate-400 border-slate-100 cursor-not-allowed shadow-none': !link.url
          }"
        >
          {{ link.label }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
