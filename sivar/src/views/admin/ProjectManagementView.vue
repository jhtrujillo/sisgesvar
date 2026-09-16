<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-emerald-50/20 p-4 md:p-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Top Navigation & Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center space-x-4">
          <BackButton :to="{ name: 'mejoramiento.show' }" label="Volver a Mejoramiento" />
          <div>
            <div class="flex items-center space-x-2">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">
                Módulo de Gobierno & Control
              </span>
              <span v-if="isSuperAdmin" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                👑 Super-Administrador Global
              </span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight mt-1">
              Administración de Proyectos y Permisos
            </h1>
            <p class="text-xs text-slate-500 font-medium">
              Gestiona el inventario de proyectos de Cenicaña y otorga privilegios de administración o edición por usuario.
            </p>
          </div>
        </div>

        <!-- Super Admin Banner -->
        <div class="flex items-center bg-slate-900 text-white px-4 py-2.5 rounded-2xl space-x-3 shadow-md border border-slate-800">
          <div class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300 font-bold text-sm">
            JT
          </div>
          <div>
            <div class="text-[10px] uppercase font-bold text-slate-400">Super Admin Principal</div>
            <div class="text-xs font-extrabold text-white">jhtrujillo@cenicana.org</div>
          </div>
        </div>
      </div>

      <!-- KPI Dashboard Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Proyectos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-emerald-100 text-emerald-800 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Proyectos</div>
            <div class="text-2xl font-black text-slate-800">{{ proyectos.length }}</div>
          </div>
        </div>

        <!-- Proyectos con Admin -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-amber-100 text-amber-800 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Con Administradores</div>
            <div class="text-2xl font-black text-amber-700">{{ proyectosConAdmin }}</div>
          </div>
        </div>

        <!-- Proyectos Administrados por Mi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-sky-100 text-sky-800 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Mis Administraciones</div>
            <div class="text-2xl font-black text-sky-700">{{ misProyectosCount }}</div>
          </div>
        </div>

        <!-- Programas de Investigación -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-indigo-100 text-indigo-800 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 10V11" />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Programas / Áreas</div>
            <div class="text-2xl font-black text-indigo-700">{{ programasCount }}</div>
          </div>
        </div>
      </div>

      <!-- Toolbar Filtros & Acciones -->
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="flex flex-col md:flex-row gap-3 items-center w-full md:w-auto">
          <!-- Buscador por Texto -->
          <div class="relative w-full md:w-80">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar proyecto o código contable..."
              class="w-full pl-9 pr-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50/50"
            />
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-slate-400 absolute left-3 top-2.5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Filtro por Programa -->
          <select
            v-model="selectedProgram"
            class="w-full md:w-auto px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50/50"
          >
            <option value="all">Todos los Programas</option>
            <option v-for="p in programasList" :key="p" :value="p">{{ p }}</option>
          </select>

          <!-- Filtro por Estado Admin -->
          <select
            v-model="adminStatusFilter"
            class="w-full md:w-auto px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50/50"
          >
            <option value="all">Todos los Estados</option>
            <option value="with_admin">Con Administrador Asignado</option>
            <option value="without_admin">Sin Administrador</option>
          </select>
        </div>

        <!-- Botón Recargar -->
        <button
          @click="loadProyectos"
          :disabled="isLoading"
          class="w-full md:w-auto px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all flex items-center justify-center space-x-1.5 cursor-pointer disabled:opacity-50"
        >
          <svg v-if="isLoading" class="animate-spin h-3.5 w-3.5 text-slate-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Sincronizar Lista</span>
        </button>
      </div>

      <!-- Tabla Principal de Proyectos -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-20 space-y-3">
          <div class="relative w-10 h-10">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-100 animate-pulse"></div>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-600 border-t-transparent animate-spin"></div>
          </div>
          <span class="text-xs font-bold text-slate-600">Cargando inventario de proyectos y permisos...</span>
        </div>

        <div v-else-if="filteredProyectos.length === 0" class="flex flex-col items-center justify-center py-16 text-center text-slate-400 space-y-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <span class="text-sm font-semibold text-slate-600">No se encontraron proyectos con los criterios de búsqueda.</span>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-100/70 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
              <tr>
                <th class="px-5 py-3.5">Código / ID</th>
                <th class="px-5 py-3.5">Proyecto</th>
                <th class="px-5 py-3.5">Programa / Área</th>
                <th class="px-5 py-3.5">Administradores Asignados</th>
                <th class="px-5 py-3.5 text-center">Permisos Totales</th>
                <th class="px-5 py-3.5 text-center">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
              <tr v-for="p in filteredProyectos" :key="p.id_prycto" class="hover:bg-slate-50/70 transition-colors">
                <td class="px-5 py-3.5">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-800 font-mono text-[11px] font-bold border border-slate-200">
                    {{ p.cd_cntble || `PR-${p.id_prycto}` }}
                  </span>
                </td>
                <td class="px-5 py-3.5 max-w-xs">
                  <div class="font-extrabold text-slate-800 text-sm leading-snug">{{ p.nm_prycto }}</div>
                </td>
                <td class="px-5 py-3.5">
                  <div class="text-xs font-bold text-slate-700">{{ p.nombre_programa }}</div>
                  <div class="text-[10px] text-slate-400 font-semibold">{{ p.nombre_area_trbjo }}</div>
                </td>
                <td class="px-5 py-3.5">
                  <div v-if="p.administradores && p.administradores.length > 0" class="flex flex-wrap gap-1">
                    <span
                      v-for="admin in p.administradores"
                      :key="admin.usuario_id"
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200"
                    >
                      👑 {{ admin.nombre }}
                    </span>
                  </div>
                  <span v-else class="text-[11px] text-slate-400 italic">
                    Sin administrador directo
                  </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-black text-xs border border-slate-200">
                    {{ p.total_usuarios }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <div class="flex items-center justify-center space-x-1.5">
                    <button
                      @click="openDetailModal(p)"
                      class="px-2.5 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all flex items-center justify-center space-x-1 cursor-pointer border border-slate-200 shadow-sm"
                      title="Ver Ficha 360° del Proyecto"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <span>Ficha 360°</span>
                    </button>

                    <button
                      @click="openPermissionsModal(p)"
                      class="px-2.5 py-1.5 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-sm transition-all flex items-center justify-center space-x-1 cursor-pointer"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span>Permisos</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Modal de Gestión de Permisos -->
    <ProjectPermissionsModal
      :isOpen="isModalOpen"
      :proyecto="selectedProyecto"
      @close="isModalOpen = false"
      @updated="loadProyectos"
    />

    <!-- Modal Ficha 360° del Proyecto -->
    <ProjectDetailModal
      :isOpen="isDetailModalOpen"
      :proyectoId="selectedProyectoId"
      @close="isDetailModalOpen = false"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import BackButton from "@/components/BackButton.vue";
import ProjectPermissionsModal from "@/components/admin/ProjectPermissionsModal.vue";
import ProjectDetailModal from "@/components/admin/ProjectDetailModal.vue";
import projectManagementService from "@/services/projectManagement.services";
import { useToast } from "vue-toastification";

const toast = useToast();

const proyectos = ref<any[]>([]);
const isSuperAdmin = ref(false);
const isLoading = ref(false);

const searchQuery = ref("");
const selectedProgram = ref("all");
const adminStatusFilter = ref("all");

const isModalOpen = ref(false);
const isDetailModalOpen = ref(false);
const selectedProyecto = ref<any>(null);
const selectedProyectoId = ref<number | null>(null);

const openDetailModal = (proyecto: any) => {
  selectedProyectoId.value = proyecto.id_prycto;
  isDetailModalOpen.value = true;
};

const loadProyectos = async () => {
  isLoading.value = true;
  try {
    const res = await projectManagementService.getProyectosAdmin();
    proyectos.value = res.data.proyectos || [];
    isSuperAdmin.value = res.data.is_super_admin || false;
  } catch (err) {
    console.error("Error al cargar proyectos:", err);
    toast.error("Error al consultar la lista de proyectos y permisos.");
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadProyectos();
});

const openPermissionsModal = (proyecto: any) => {
  selectedProyecto.value = proyecto;
  isModalOpen.value = true;
};

const programasList = computed(() => {
  const set = new Set<string>();
  proyectos.value.forEach((p) => {
    if (p.nombre_programa) set.add(p.nombre_programa);
  });
  return Array.from(set).sort();
});

const proyectosConAdmin = computed(() => {
  return proyectos.value.filter((p) => p.administradores && p.administradores.length > 0).length;
});

const misProyectosCount = computed(() => {
  return proyectos.value.filter((p) => p.can_manage).length;
});

const programasCount = computed(() => {
  return programasList.value.length;
});

const filteredProyectos = computed(() => {
  return proyectos.value.filter((p) => {
    const q = searchQuery.value.toLowerCase().trim();
    const matchText =
      !q ||
      (p.nm_prycto && p.nm_prycto.toLowerCase().includes(q)) ||
      (p.cd_cntble && p.cd_cntble.toLowerCase().includes(q));

    let matchProgram = true;
    if (selectedProgram.value !== "all") {
      matchProgram = p.nombre_programa === selectedProgram.value;
    }

    let matchStatus = true;
    if (adminStatusFilter.value === "with_admin") {
      matchStatus = p.administradores && p.administradores.length > 0;
    } else if (adminStatusFilter.value === "without_admin") {
      matchStatus = !p.administradores || p.administradores.length === 0;
    }

    return matchText && matchProgram && matchStatus;
  });
});
</script>
