<!-- Vista para mostrar en una lista los procesos que han sido completados y el módulo de la Bolsa Común de Flores -->
<template>
  <div class="min-h-screen bg-slate-50/50 flex flex-col p-4 sm:p-8 font-sans">
    <!-- Header Area -->
    <div class="space-y-6 w-full mx-auto px-4 mb-4">
      <!-- Botón Volver -->
      <BackButton :to="{ name: 'mejoramiento.show' }" label="Volver a Mejoramiento" />

      <!-- Title Block con switch e información -->
      <div class="border-b border-slate-100 pb-5 flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
            <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                ></path>
              </svg>
            </div>
            Módulo de Floración
          </h1>
          <p class="text-slate-500 mt-2 ml-11 text-sm">
            Gestión de registros de floración, viabilidad de polen y catálogo de flores libres en Bolsa Común para cruzamientos.
          </p>
        </div>

        <!-- Acciones generales -->
        <div v-if="activeTab === 'tabla'" class="flex flex-col items-end gap-3 shrink-0 mt-1">
          <label class="relative inline-flex items-center cursor-pointer group" title="Visualizar todos los registros de floración de los últimos 10 años">
            <input type="checkbox" v-model="verHistorico" class="sr-only peer" />
            <div
              class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"
            ></div>
            <span class="ml-3 text-sm font-bold text-slate-600 group-hover:text-emerald-700 transition-colors">Modo Histórico</span>
          </label>
          <button
            @click="isImportWizardOpen = true"
            class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Importar Excel
          </button>
        </div>
      </div>
    </div>

    <!-- Pestañas de Submódulos -->
    <div class="flex items-center space-x-2 border-b border-slate-200 mb-6 px-4">
      <button
        @click="activeTab = 'tabla'"
        :class="
          activeTab === 'tabla'
            ? 'border-emerald-600 text-emerald-800 bg-white font-black shadow-sm'
            : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 font-bold'
        "
        class="flex items-center space-x-2 py-3 px-5 text-xs border-b-2 transition-all rounded-t-xl cursor-pointer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        <span>🌸 Registro General de Floración</span>
      </button>

      <button
        @click="activeTab = 'bolsa'"
        :class="
          activeTab === 'bolsa'
            ? 'border-emerald-600 text-emerald-800 bg-white font-black shadow-sm'
            : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 font-bold'
        "
        class="flex items-center space-x-2 py-3 px-5 text-xs border-b-2 transition-all rounded-t-xl cursor-pointer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <span>💼 Flores Disponibles en Bolsa Común</span>
        <span v-if="bolsaFlores.length > 0" class="ml-1.5 px-2 py-0.5 text-[10px] font-black bg-amber-500 text-white rounded-full">
          {{ totalFloresBolsa }}
        </span>
      </button>
    </div>

    <!-- CONTENIDO TAB 1: Registro General de Floraciones -->
    <div
      v-if="activeTab === 'tabla'"
      class="w-full bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 flex-1 relative z-10 p-2 sm:p-6 overflow-hidden min-h-[400px]"
    >
      <!-- Overlay Loading State -->
      <div v-if="isLoading" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300">
        <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center gap-3 border border-violet-100">
          <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-violet-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <span class="text-violet-800 font-bold tracking-wide animate-pulse">Sincronizando registros...</span>
        </div>
      </div>

      <TableComponent
        :rows="floweringListsStore.FloweringList"
        :have-search="true"
        :have-column-filters="true"
        :have-button-excel="true"
        :allow-hide-columns="true"
        name-excel="flowering"
        :columns="conlumnsInfo"
        :empty-message="verHistorico ? 'No se encontraron registros de floración en el historial' : 'No hay registros de floración para las últimas 24 horas'"
        :empty-subtext="
          verHistorico
            ? 'Intente ajustar los términos de búsqueda.'
            : 'No se encontraron flores registradas entre ayer y hoy. Active el Modo Histórico en la parte superior derecha para consultar registros anteriores.'
        "
      ></TableComponent>
    </div>

    <!-- CONTENIDO TAB 2: Módulo de Bolsa Común -->
    <div v-else-if="activeTab === 'bolsa'" class="space-y-6 flex-1">
      <!-- Tarjetas KPIs Bolsa Común -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-emerald-100 text-emerald-700 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Flores Libres</div>
            <div class="text-2xl font-black text-slate-800">{{ totalFloresBolsa }}</div>
          </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-indigo-100 text-indigo-700 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.594 15.12a2 2 0 00-1.42.368l-1.42 1.065"
              />
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Variedades Únicas</div>
            <div class="text-2xl font-black text-slate-800">{{ bolsaFlores.length }}</div>
          </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-pink-100 text-pink-700 rounded-xl">
            <span class="text-xl font-bold">♀</span>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-pink-500">Hembras (Femenino)</div>
            <div class="text-2xl font-black text-pink-700">{{ countHembrasBolsa }}</div>
          </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center space-x-4">
          <div class="p-3 bg-sky-100 text-sky-700 rounded-xl">
            <span class="text-xl font-bold">♂</span>
          </div>
          <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-sky-500">Machos (Masculino)</div>
            <div class="text-2xl font-black text-sky-700">{{ countMachosBolsa }}</div>
          </div>
        </div>
      </div>

      <!-- Barra de Filtro y Acciones -->
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="flex flex-col sm:flex-row gap-3 items-center w-full sm:w-auto">
          <!-- Buscador por Texto -->
          <div class="relative w-full sm:w-80">
            <input
              v-model="searchBolsa"
              type="text"
              placeholder="Buscar por variedad o carácter..."
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

          <!-- Filtro por Sexo -->
          <select
            v-model="sexBolsaFilter"
            class="w-full sm:w-auto px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50/50"
          >
            <option value="all">Todos los Sexos</option>
            <option value="Hembra">Hembra ♀</option>
            <option value="Macho">Macho ♂</option>
          </select>
        </div>

        <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
          <button
            @click="loadBolsaComunFlores"
            :disabled="isLoadingBolsa"
            class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all flex items-center space-x-1.5 cursor-pointer disabled:opacity-50"
          >
            <svg v-if="isLoadingBolsa" class="animate-spin h-3.5 w-3.5 text-slate-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-3.5 w-3.5 text-slate-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
            <span>Sincronizar Bolsa Común</span>
          </button>
        </div>
      </div>

      <!-- Tabla de Flores Disponibles en Bolsa Común -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div v-if="isLoadingBolsa" class="flex flex-col items-center justify-center py-20 space-y-3">
          <div class="relative w-10 h-10">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-100 animate-pulse"></div>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-600 border-t-transparent animate-spin"></div>
          </div>
          <span class="text-xs font-bold text-slate-600">Cargando inventario de flores en la Bolsa Común...</span>
        </div>

        <div v-else-if="filteredBolsaFlores.length === 0" class="flex flex-col items-center justify-center py-16 text-center text-slate-400 space-y-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
            />
          </svg>
          <span class="text-sm font-semibold text-slate-600">No hay flores disponibles en la Bolsa Común con los criterios seleccionados.</span>
          <p class="text-xs text-slate-400 max-w-sm">Las flores libres de proyectos finalizados o ingresadas sin proyecto aparecerán automáticamente aquí.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-100/70 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
              <tr>
                <th class="px-5 py-3.5">Variedad</th>
                <th class="px-5 py-3.5">Sexo / Polen</th>
                <th class="px-5 py-3.5">Carácter / Criterio</th>
                <th class="px-5 py-3.5 text-center">Flores Libres</th>
                <th class="px-5 py-3.5 text-center">Estado</th>
                <th class="px-5 py-3.5 text-center">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
              <tr v-for="item in filteredBolsaFlores" :key="item.variedad_key" class="hover:bg-emerald-50/40 transition-colors">
                <td class="px-5 py-3.5 font-extrabold text-slate-800 text-sm">
                  <span
                    @click="openVarietyProfile(item.vrdad)"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-100 hover:underline cursor-pointer"
                  >
                    {{ item.vrdad }}
                  </span>
                </td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-2">
                    <span
                      :class="isHembra(item.sxo) ? 'bg-pink-100 text-pink-700 border-pink-200' : 'bg-sky-100 text-sky-700 border-sky-200'"
                      class="px-2.5 py-0.5 rounded text-[10px] font-bold border"
                    >
                      {{ isHembra(item.sxo) ? "♀ Hembra" : "♂ Macho" }}
                    </span>
                    <span v-if="item.polen" class="text-[10px] text-slate-400 font-semibold">Polen: {{ item.polen }}%</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-slate-600">
                  {{ item.nombre_caracter || "Caracterización Estándar" }}
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="px-3 py-1 rounded-xl bg-amber-100 text-amber-900 font-black text-sm border border-amber-200">
                    {{ item.cantidad }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                  >
                    🟢 Libre en Bolsa
                  </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <router-link :to="{ name: 'crossing_initial_data.show' }">
                    <button
                      class="px-3 py-1.5 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-sm transition-all duration-150 flex items-center justify-center space-x-1 mx-auto cursor-pointer"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                      <span>Programar Cruzamiento</span>
                    </button>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <FloracionImportWizard :is-open="isImportWizardOpen" @close="isImportWizardOpen = false" @imported="onImportSuccess" />

    <!-- Drawer de Hoja de Vida de la Variedad -->
    <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, computed } from "vue";
import { useFloweringStore } from "@/stores/flowering";
import TableComponent from "../../../components/app-table/TableComponent.vue";
import type { Column } from "../../../components/app-table/models";
import BackButton from "@/components/BackButton.vue";
import FloracionImportWizard from "@/components/floracion/FloracionImportWizard.vue";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import floweringService from "@/services/flowering.services";
import { useToast } from "vue-toastification";

const toast = useToast();
const floweringListsStore = useFloweringStore();

const activeTab = ref<"tabla" | "bolsa">("tabla");
const verHistorico = ref(false);
const isLoading = ref(false);
const isImportWizardOpen = ref(false);

// State Módulo Bolsa Común
const isLoadingBolsa = ref(false);
const bolsaFlores = ref<any[]>([]);
const searchBolsa = ref("");
const sexBolsaFilter = ref("all");

// State Variety Profile Drawer
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const openVarietyProfile = (name: string) => {
  if (name && name !== "null" && name !== "?") {
    selectedVarietyForDrawer.value = name;
    isDrawerOpen.value = true;
  }
};

const isHembra = (sxo?: string) => {
  if (!sxo) return false;
  const s = sxo.toLowerCase().trim();
  return s.includes("hembra") || s.includes("femenino") || s === "hd" || s === "hf" || s.startsWith("h");
};

const isMacho = (sxo?: string) => {
  if (!sxo) return false;
  const s = sxo.toLowerCase().trim();
  return s.includes("macho") || s.includes("masculino") || s === "md" || s === "mf" || s.startsWith("m");
};

const loadBolsaComunFlores = async () => {
  isLoadingBolsa.value = true;
  try {
    const res = await floweringService.getBolsaComunFlores();
    bolsaFlores.value = res.data || [];
  } catch (err) {
    console.error("Error al cargar la Bolsa Común de Flores:", err);
    toast.error("Error al consultar las flores disponibles en la Bolsa Común.");
  } finally {
    isLoadingBolsa.value = false;
  }
};

watch(activeTab, (newTab) => {
  if (newTab === "bolsa") {
    loadBolsaComunFlores();
  }
});

const filteredBolsaFlores = computed(() => {
  return bolsaFlores.value.filter((item) => {
    const query = searchBolsa.value.toLowerCase().trim();
    const matchText =
      !query || (item.vrdad && item.vrdad.toLowerCase().includes(query)) || (item.nombre_caracter && item.nombre_caracter.toLowerCase().includes(query));

    let matchSex = true;
    if (sexBolsaFilter.value === "Hembra") {
      matchSex = isHembra(item.sxo);
    } else if (sexBolsaFilter.value === "Macho") {
      matchSex = isMacho(item.sxo);
    }

    return matchText && matchSex;
  });
});

const totalFloresBolsa = computed(() => {
  return bolsaFlores.value.reduce((acc, f) => acc + (parseInt(f.cantidad) || 0), 0);
});

const countHembrasBolsa = computed(() => {
  return bolsaFlores.value.filter((f) => isHembra(f.sxo)).reduce((acc, f) => acc + (parseInt(f.cantidad) || 0), 0);
});

const countMachosBolsa = computed(() => {
  return bolsaFlores.value.filter((f) => isMacho(f.sxo)).reduce((acc, f) => acc + (parseInt(f.cantidad) || 0), 0);
});

const onImportSuccess = async () => {
  try {
    isLoading.value = true;
    await floweringListsStore.getFlowering(verHistorico.value);
  } catch (e) {
    console.error("Error reloading flowering list:", e);
  } finally {
    isLoading.value = false;
  }
};

onMounted(async () => {
  isLoading.value = true;
  await floweringListsStore.getFlowering(verHistorico.value);
  isLoading.value = false;

  // Cargar contador inicial de la Bolsa Común
  loadBolsaComunFlores();
});

watch(verHistorico, async (nuevoValor) => {
  isLoading.value = true;
  await floweringListsStore.getFlowering(nuevoValor);
  isLoading.value = false;
});

const conlumnsInfo: Array<Column> = [
  { keyName: "grpo", text: "Origen" },
  { keyName: "vrdad", text: "Variedad" },
  { keyName: "nmbre_crcter", text: "Caracter" },
  { keyName: "vivero", text: "Vivero" },
  { keyName: "ingnio", text: "Ingenio" },
  { keyName: "hcnda", text: "Hacienda" },
  { keyName: "lte", text: "Lote" },
  { keyName: "prcla", text: "Parcela" },
  { keyName: "flores", text: "Flores", formatFromRow: () => "1" },
  { keyName: "polen", text: "Polen", formatFromRow: (row) => (row.polen ? `${row.polen}%` : "") },
  { keyName: "sxo", text: "Sexo", formatFromRow: (row) => (row.cmbio_sxo ? `${row.sxo} -> ${row.cmbio_sxo}` : row.sxo || "") },
  { keyName: "flrcion", text: "Floracion" },
  { keyName: "fcha", text: "Fecha" },
  { keyName: "anio", text: "Año", formatFromRow: (row) => (row.fcha ? row.fcha.substring(0, 4) : "") },
  { keyName: "nm_prycto", text: "Proyecto" },
  { keyName: "usuario", text: "Usuario", formatFromRow: (row) => `${row.prmer_nmbre || ""} ${row.aplldo || ""}`.trim() },
  { keyName: "obsrvcn", text: "Observaciones" }
];
</script>
