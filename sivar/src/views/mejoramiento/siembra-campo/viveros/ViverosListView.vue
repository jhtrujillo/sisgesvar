<template>
  <div class="container mx-auto p-6">
    <div class="mb-6">
      <div class="mb-4">
        <router-link
          :to="{ name: 'mejoramiento.show' }"
          class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 rounded-full shadow-sm transition-all duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Volver a Mejoramiento
        </router-link>
      </div>
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">Administración de Viveros</h1>
        <router-link
          :to="{ name: 'vivero_nuevo.show' }"
          class="flex items-center px-6 py-2.5 text-sm font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200"
        >
          Registrar Nuevo Vivero
        </router-link>
      </div>
    </div>

    <div class="bg-white shadow-md rounded my-6 p-4">
      <!-- Filtros y Paginación Superior -->
      <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <div class="flex items-center">
          <label class="text-sm text-gray-600 mr-2">Mostrar</label>
          <select v-model="itemsPerPage" class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-cenicana focus:border-transparent">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="1000000">Todos</option>
          </select>
          <span class="text-sm text-gray-600 ml-2">registros</span>
        </div>
        
        <div class="relative w-full md:w-64">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Buscar vivero..." 
            class="w-full border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cenicana focus:border-transparent"
          />
          <svg class="w-4 h-4 absolute right-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>

      <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="min-w-full bg-white">
        <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
          <tr>
            <th class="py-3 px-6 text-left">IDPlot</th>
            <th class="py-3 px-6 text-left">Nombre</th>
            <th class="py-3 px-6 text-left">Hda / Suerte</th>
            <th class="py-3 px-6 text-left">Proyecto</th>
            <th class="py-3 px-6 text-left">Tipo de floración</th>
            <th class="py-3 px-6 text-left">Fecha Siembra</th>
            <th class="py-3 px-6 text-left">Cortes</th>
            <th class="py-3 px-6 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-if="loading" class="border-b border-gray-200">
            <td colspan="5" class="py-3 px-6 text-center">Cargando viveros...</td>
          </tr>
          <tr v-else-if="filteredViveros.length === 0" class="border-b border-gray-200">
            <td colspan="5" class="py-3 px-6 text-center">No se encontraron resultados.</td>
          </tr>
          <tr
            v-else
            v-for="vivero in paginatedViveros"
            :key="vivero.id"
            class="border-b border-gray-200 hover:bg-gray-100"
          >
            <td class="py-3 px-6 text-left whitespace-nowrap">
              <span class="font-medium">{{ getBaseCode(vivero.identificador_unico) }}</span>
            </td>
            <td class="py-3 px-6 text-left" v-html="vivero.nombre"></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vivero.hacienda || 'N/A' }} / {{ vivero.suerte || 'N/A' }}</td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900" :title="vivero.proyecto?.nm_prycto || 'N/A'" v-html="vivero.proyecto?.nm_prycto || 'N/A'"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ vivero.condicion || 'N/A' }}</div>
            </td>
            <td class="py-3 px-6 text-left">{{ formatDate(vivero.fecha_siembra) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ vivero.numero_corte }}</div>
            </td>
            <td class="py-3 px-6 text-center">
              <div class="flex item-center justify-center">

                <router-link
                  :to="{ name: 'vivero_editar.show', params: { id: vivero.id } }"
                  class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110 cursor-pointer"
                  title="Editar"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </router-link>
                <button
                  @click="deleteVivero(vivero.id)"
                  class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>

      <!-- Paginación Inferior -->
      <div class="flex flex-col md:flex-row justify-between items-center mt-4 text-sm text-gray-600">
        <div>
          Mostrando {{ paginationStart }} a {{ paginationEnd }} de {{ filteredViveros.length }} registros
        </div>
        <div class="flex gap-2 mt-2 md:mt-0">
          <button 
            @click="prevPage" 
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Anterior
          </button>
          <div class="flex items-center px-2">
            Página {{ currentPage }} de {{ totalPages }}
          </div>
          <button 
            @click="nextPage" 
            :disabled="currentPage >= totalPages"
            class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Cosecha -->
    <div v-if="isCosechaModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
          <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Registrar Cosecha</h4>
          <button @click="closeCosechaModal" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl">&times;</button>
        </div>

        <!-- Body -->
        <form @submit.prevent="submitCosecha">
          <div class="p-6 space-y-4">
            <div class="p-3 bg-emerald-50/50 text-emerald-900 text-xs font-semibold rounded-lg border border-emerald-100/50">
              Registrando cosecha para el vivero <span class="font-bold">{{ viveroSeleccionado?.nombre }}</span> (Corte Actual: {{ viveroSeleccionado?.numero_corte }}).
            </div>

            <div>
              <label for="fecha_cosecha" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Fecha de Cosecha <span class="text-red-500">*</span></label>
              <input
                v-model="cosechaForm.fecha_cosecha"
                id="fecha_cosecha"
                type="date"
                required
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
              />
            </div>

            <div>
              <label for="nueva_fecha_siembra" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nueva Fecha de Siembra <span class="text-red-500">*</span></label>
              <input
                v-model="cosechaForm.nueva_fecha_siembra"
                id="nueva_fecha_siembra"
                type="date"
                required
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
              />
            </div>
          </div>

          <!-- Footer -->
          <div class="border-t border-slate-100 p-5 bg-slate-50 flex justify-end gap-3">
            <button
              type="button"
              @click="closeCosechaModal"
              class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSubmittingCosecha"
              class="flex items-center px-5 py-2.5 text-sm font-bold text-white bg-cenicana hover:bg-cenicana-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl transition-colors"
            >
              <svg v-if="isSubmittingCosecha" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Guardar Cosecha
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Historial de Cosechas -->
    <div v-if="isHistorialModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
          <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Historial de Cosechas</h4>
          <button @click="closeHistorialModal" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl">&times;</button>
        </div>

        <!-- Body -->
        <div class="p-6">
          <div class="mb-4 flex flex-col gap-2">
            <div class="text-sm text-slate-600">
              Historial de cosechas para el vivero <span class="font-bold text-slate-800" v-html="viveroSeleccionado?.nombre"></span> (<span class="font-mono text-xs">{{ getBaseCode(viveroSeleccionado?.identificador_unico) }}</span>)
            </div>
            <div class="text-xs text-slate-500 bg-slate-50 p-3 rounded-lg border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-2">
              <div><span class="font-bold block uppercase">Ingenio</span> <span v-html="getIngenioName(viveroSeleccionado?.ingenio)"></span></div>
              <div><span class="font-bold block uppercase">Hacienda</span> <span v-html="viveroSeleccionado?.hacienda || 'N/A'"></span></div>
              <div><span class="font-bold block uppercase">Suerte</span> {{ viveroSeleccionado?.suerte || 'N/A' }}</div>
              <div><span class="font-bold block uppercase">Corte Actual</span> {{ viveroSeleccionado?.numero_corte || 'N/A' }}</div>
              
              <div class="md:col-span-2"><span class="font-bold block uppercase">Proyecto</span> <span v-html="viveroSeleccionado?.nombre_proyecto || 'N/A'"></span></div>
              <div><span class="font-bold block uppercase">Ambiente</span> <span v-html="viveroSeleccionado?.nombre_ambiente || 'N/A'"></span></div>
              <div><span class="font-bold block uppercase">Responsable</span> <span v-html="viveroSeleccionado?.nombre_responsable || 'N/A'"></span></div>
            </div>
          </div>

          <div v-if="loadingHistorial" class="flex justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-cenicana" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          <div v-else-if="historial.length === 0" class="text-center py-8 text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200">
            No hay historial de cosechas para este vivero.
          </div>

          <div v-else class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-sm text-left">
              <thead class="text-xs text-slate-600 uppercase bg-slate-50 border-b border-slate-200">
                <tr>
                  <th scope="col" class="px-4 py-3">Código Histórico</th>
                  <th scope="col" class="px-4 py-3">Fecha de Cosecha</th>
                  <th scope="col" class="px-4 py-3">Nueva Siembra</th>
                  <th scope="col" class="px-4 py-3 text-center">Corte Anterior</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in historial" :key="item.id" :class="index !== historial.length - 1 ? 'border-b border-slate-100' : ''" class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3 font-medium text-slate-800">
                    <span class="font-mono text-xs">{{ getHistoricalCode(viveroSeleccionado?.identificador_unico, item.numero_corte_anterior) }}</span>
                  </td>
                  <td class="px-4 py-3 font-medium text-slate-800">{{ formatDate(item.fecha_cosecha) }}</td>
                  <td class="px-4 py-3 text-slate-600">{{ formatDate(item.nueva_fecha_siembra) }}</td>
                  <td class="px-4 py-3 text-center">
                    <span class="bg-indigo-50 text-indigo-700 font-bold py-1 px-3 rounded-full text-xs">
                      {{ item.numero_corte_anterior }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-100 p-5 bg-slate-50 flex justify-end">
          <button
            @click="closeHistorialModal"
            class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import viverosServices from '@/services/viveros.services';
import dayjs from 'dayjs';
import { useToast } from 'vue-toastification';

const toast = useToast();
const viveros = ref<any[]>([]);
const loading = ref(true);

const isCosechaModalOpen = ref(false);
const isSubmittingCosecha = ref(false);
const viveroSeleccionado = ref<any>(null);
const cosechaForm = ref({
  fecha_cosecha: '',
  nueva_fecha_siembra: ''
});

const isHistorialModalOpen = ref(false);
const historial = ref<any[]>([]);
const loadingHistorial = ref(false);

const ingenios = ref<any[]>([]);

// Paginación y Filtrado
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

const filteredViveros = computed(() => {
  if (!searchQuery.value) return viveros.value;
  const q = searchQuery.value.toLowerCase();
  return viveros.value.filter(v => 
    (v.nombre && v.nombre.toLowerCase().includes(q)) ||
    (v.identificador_unico && v.identificador_unico.toLowerCase().includes(q)) ||
    (v.hacienda && v.hacienda.toLowerCase().includes(q)) ||
    (v.suerte && v.suerte.toLowerCase().includes(q))
  );
});

const totalPages = computed(() => {
  return Math.ceil(filteredViveros.value.length / itemsPerPage.value) || 1;
});

const paginatedViveros = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredViveros.value.slice(start, end);
});

const paginationStart = computed(() => {
  if (filteredViveros.value.length === 0) return 0;
  return (currentPage.value - 1) * itemsPerPage.value + 1;
});

const paginationEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value;
  return end > filteredViveros.value.length ? filteredViveros.value.length : end;
});

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

// Reset página cuando se busca
watch([searchQuery, itemsPerPage], () => {
  currentPage.value = 1;
});

const loadIngenios = async () => {
  try {
    const res = await viverosServices.getIngenios();
    ingenios.value = res.data;
  } catch (error) {
    console.error('Error fetching ingenios:', error);
  }
};

const getIngenioName = (codigo: string) => {
  if (!codigo) return 'N/A';
  const ingenio = ingenios.value.find(i => i.cd_ingnio === codigo);
  return ingenio ? `${ingenio.nm_ingnio} (${codigo})` : codigo;
};

const loadViveros = async () => {
  loading.value = true;
  try {
    const response = await viverosServices.getViveros();
    viveros.value = response.data;
  } catch (error) {
    console.error('Error fetching viveros:', error);
    toast.error('Error al cargar la lista de viveros');
  } finally {
    loading.value = false;
  }
};

const deleteVivero = async (id: number) => {
  if (confirm('¿Está seguro de que desea eliminar este vivero?')) {
    try {
      await viverosServices.deleteVivero(id);
      toast.success('Vivero eliminado correctamente');
      loadViveros();
    } catch (error) {
      console.error('Error deleting vivero:', error);
      toast.error('Error al eliminar el vivero');
    }
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  return dayjs(dateString).format('YYYY-MM-DD');
};

const openCosechaModal = (vivero: any) => {
  viveroSeleccionado.value = vivero;
  cosechaForm.value = {
    fecha_cosecha: '',
    nueva_fecha_siembra: ''
  };
  isCosechaModalOpen.value = true;
};

const closeCosechaModal = () => {
  isCosechaModalOpen.value = false;
  viveroSeleccionado.value = null;
};

const getBaseCode = (currentCode: string) => {
  if (!currentCode) return '';
  return currentCode.replace(/-C?\d+$/, '');
};

const getHistoricalCode = (currentCode: string, historicalCorte: number) => {
  if (!currentCode) return '';
  const baseCode = getBaseCode(currentCode);
  return `${baseCode}-${historicalCorte}`;
};

const submitCosecha = async () => {
  if (!viveroSeleccionado.value) return;
  isSubmittingCosecha.value = true;
  try {
    await viverosServices.registrarCosecha(viveroSeleccionado.value.id, cosechaForm.value);
    toast.success('Cosecha registrada y vivero actualizado correctamente');
    closeCosechaModal();
    loadViveros(); // Reload table
  } catch (error) {
    console.error('Error registering cosecha:', error);
    toast.error('Error al registrar la cosecha');
  } finally {
    isSubmittingCosecha.value = false;
  }
};

const openHistorialModal = async (vivero: any) => {
  viveroSeleccionado.value = vivero;
  isHistorialModalOpen.value = true;
  loadingHistorial.value = true;
  historial.value = [];
  try {
    const response = await viverosServices.getHistorialCosechas(vivero.id);
    historial.value = response.data;
  } catch (error) {
    console.error('Error fetching historial:', error);
    toast.error('Error al cargar el historial de cosechas');
  } finally {
    loadingHistorial.value = false;
  }
};

const closeHistorialModal = () => {
  isHistorialModalOpen.value = false;
  viveroSeleccionado.value = null;
  historial.value = [];
};

onMounted(() => {
  loadIngenios();
  loadViveros();
});
</script>
