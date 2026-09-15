<template>
  <Teleport to="body">
    <transition name="modal-fade">
      <div v-if="isOpen" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <!-- Backdrop Blur de Fondo -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" @click="closeModal"></div>

        <!-- Ventana del Modal -->
        <div
          class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] overflow-hidden transform transition-all duration-300"
        >
          <!-- Cabecera del Modal -->
          <div class="bg-gradient-to-r from-emerald-900 via-slate-800 to-emerald-900 text-white px-6 py-4 flex items-center justify-between border-b border-emerald-800/40 shrink-0">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-black tracking-tight flex items-center gap-2">
                  Flores Disponibles de Otros Proyectos y Bolsa Común
                </h3>
                <p class="text-xs text-emerald-200/90 font-medium mt-0.5">
                  Consulte y transfiera flores activas de otros proyectos o Bolsa Común a este proyecto.
                </p>
              </div>
            </div>

            <!-- Botón de Cerrar -->
            <button
              @click="closeModal"
              class="p-2 rounded-xl text-slate-300 hover:text-white bg-slate-800/60 hover:bg-slate-700/60 transition-colors cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>

          <!-- Filtros de Búsqueda -->
          <div class="p-4 bg-slate-50 border-b border-slate-150 shrink-0 space-y-3">
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
              <!-- Buscador por Texto -->
              <div class="relative w-full sm:w-72">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Buscar variedad o proyecto..."
                  class="w-full pl-9 pr-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
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

              <!-- Filtro de Origen (Tabs) -->
              <div class="flex items-center space-x-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-semibold w-full sm:w-auto">
                <button
                  @click="originFilter = 'all'"
                  :class="originFilter === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                >
                  Todas ({{ flowersList.length }})
                </button>
                <button
                  @click="originFilter = 'bolsa'"
                  :class="originFilter === 'bolsa' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 cursor-pointer"
                >
                  💼 Bolsa Común ({{ countBolsa }})
                </button>
                <button
                  @click="originFilter = 'proyectos'"
                  :class="originFilter === 'proyectos' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 cursor-pointer"
                >
                  📁 Otros Proyectos ({{ countOtrosProyectos }})
                </button>
              </div>

              <!-- Filtro por Sexo -->
              <select
                v-model="sexFilter"
                class="px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
              >
                <option value="all">Todos los Sexos</option>
                <option value="Femenino">Femenino (Hembra)</option>
                <option value="Masculino">Masculino (Macho)</option>
              </select>
            </div>
          </div>

          <!-- Spinner de Carga -->
          <div v-if="isLoading" class="flex-1 flex flex-col items-center justify-center py-16 space-y-3">
            <div class="relative w-10 h-10">
              <div class="absolute inset-0 rounded-full border-4 border-emerald-100 animate-pulse"></div>
              <div class="absolute inset-0 rounded-full border-4 border-emerald-600 border-t-transparent animate-spin"></div>
            </div>
            <span class="text-xs font-bold text-slate-600">Buscando flores disponibles en el catálogo...</span>
          </div>

          <!-- Contenido Principal - Tabla de Flores -->
          <div v-else class="flex-1 overflow-y-auto p-4 scrollbar-custom bg-slate-50/50">
            <div v-if="filteredFlowers.length === 0" class="flex flex-col items-center justify-center py-12 text-center text-slate-400 space-y-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <span class="text-xs font-semibold text-slate-500">No se encontraron flores disponibles con los criterios seleccionados.</span>
            </div>

            <div v-else class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
              <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/70 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                  <tr>
                    <th class="px-4 py-3">Variedad</th>
                    <th class="px-4 py-3">Sexo / Polen</th>
                    <th class="px-4 py-3">Origen</th>
                    <th class="px-4 py-3 text-center">Disponible</th>
                    <th class="px-4 py-3 text-center">Acción</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                  <tr v-for="item in filteredFlowers" :key="item.variedad_key" class="hover:bg-emerald-50/40 transition-colors">
                    <td class="px-4 py-3 font-extrabold text-slate-800 text-sm">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-100">
                        {{ item.vrdad }}
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-2">
                        <span
                          :class="item.sxo === 'Femenino' ? 'bg-pink-100 text-pink-700 border-pink-200' : 'bg-blue-100 text-blue-700 border-blue-200'"
                          class="px-2 py-0.5 rounded text-[10px] font-bold border"
                        >
                          {{ item.sxo === 'Femenino' ? '♀ Hembra' : '♂ Macho' }}
                        </span>
                        <span v-if="item.polen" class="text-[10px] text-slate-400">Polen: {{ item.polen }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <span
                        v-if="item.bolsa_comun === 1"
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200"
                      >
                        💼 Bolsa Común
                      </span>
                      <span
                        v-else
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200"
                      >
                        📁 {{ item.nombre_proyecto }}
                        <span v-if="item.codigo_proyecto" class="text-[9px] opacity-75">({{ item.codigo_proyecto }})</span>
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-extrabold text-xs">
                        {{ item.cantidad }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <button
                        @click="assignFlower(item)"
                        :disabled="assigningKey === item.variedad_key"
                        class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-sm transition-all duration-150 disabled:opacity-50 flex items-center justify-center space-x-1 mx-auto cursor-pointer"
                      >
                        <svg v-if="assigningKey === item.variedad_key" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ assigningKey === item.variedad_key ? 'Asignando...' : 'Asignar a este Proyecto' }}</span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Pie del Modal -->
          <div class="px-6 py-4 bg-white border-t border-slate-150 flex items-center justify-between shrink-0">
            <span class="text-xs text-slate-500 font-medium">
              Mostrando {{ filteredFlowers.length }} registro(s) disponible(s).
            </span>
            <button
              @click="closeModal"
              class="px-5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all cursor-pointer"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import CrossingsService from "@/services/crossings.services";
import { useToast } from "vue-toastification";

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  currentProject: {
    type: String,
    required: true
  }
});

const emit = defineEmits(["update:isOpen", "flowerAssigned"]);

const toast = useToast();
const isLoading = ref(false);
const flowersList = ref<any[]>([]);
const searchQuery = ref("");
const originFilter = ref<"all" | "bolsa" | "proyectos">("all");
const sexFilter = ref("all");
const assigningKey = ref<string | null>(null);

const countBolsa = computed(() => flowersList.value.filter((f) => f.bolsa_comun === 1).length);
const countOtrosProyectos = computed(() => flowersList.value.filter((f) => f.bolsa_comun === 0).length);

const loadExternalFlowers = async () => {
  if (!props.currentProject) return;
  isLoading.value = true;
  try {
    const res = await CrossingsService.getFloresOtrosProyectos(props.currentProject);
    flowersList.value = res.data || [];
  } catch (err) {
    console.error("Error al cargar flores de otros proyectos:", err);
    toast.error("Error al cargar el catálogo de flores disponibles.");
  } finally {
    isLoading.value = false;
  }
};

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      loadExternalFlowers();
    }
  }
);

const filteredFlowers = computed(() => {
  return flowersList.value.filter((f) => {
    // Texto
    const text = searchQuery.value.toLowerCase().trim();
    const matchText =
      !text ||
      (f.vrdad && f.vrdad.toLowerCase().includes(text)) ||
      (f.nombre_proyecto && f.nombre_proyecto.toLowerCase().includes(text)) ||
      (f.codigo_proyecto && f.codigo_proyecto.toLowerCase().includes(text));

    // Origen
    let matchOrigin = true;
    if (originFilter.value === "bolsa") matchOrigin = f.bolsa_comun === 1;
    if (originFilter.value === "proyectos") matchOrigin = f.bolsa_comun === 0;

    // Sexo
    let matchSex = true;
    if (sexFilter.value !== "all") matchSex = f.sxo === sexFilter.value;

    return matchText && matchOrigin && matchSex;
  });
});

const assignFlower = async (item: any) => {
  assigningKey.value = item.variedad_key;
  try {
    await CrossingsService.enviarFlorAProyecto(item.variedad_key, props.currentProject, item.bolsa_comun);
    toast.success(`Flor ${item.vrdad} asignada exitosamente a este proyecto.`);
    emit("flowerAssigned", item);
    await loadExternalFlowers();
  } catch (err) {
    console.error("Error al asignar flor:", err);
    toast.error("No se pudo asignar la flor al proyecto.");
  } finally {
    assigningKey.value = null;
  }
};

const closeModal = () => {
  emit("update:isOpen", false);
};
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
