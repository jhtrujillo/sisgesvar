<template>
  <div class="p-6 max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
      <div>
        <nav class="text-xs text-slate-400 font-semibold mb-1 uppercase tracking-wider flex items-center gap-1.5">
          <router-link :to="{ name: 'siembra_campo_viveros.show' }" class="hover:text-cenicana transition-colors">Viveros</router-link>
          <span>/</span>
          <span class="text-slate-600">Administración de Lotes</span>
        </nav>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Administración de Lotes por Ingenio</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Configura la capacidad máxima de viveros permitidos para cada lote físico de los ingenios.</p>
      </div>

      <router-link
        :to="{ name: 'siembra_campo_viveros.show' }"
        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-all shadow-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver a Viveros
      </router-link>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
      <!-- Selector de Ingenio y Botón Agregar -->
      <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
        <div class="w-full sm:max-w-xs">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Seleccionar Ingenio</label>
          <select
            v-model="selectedIngenio"
            @change="loadLotes"
            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
          >
            <option value="" disabled>Seleccione un ingenio...</option>
            <option v-for="ing in ingenios" :key="ing.cd_ingnio" :value="ing.cd_ingnio">
              {{ decodeHTMLEntities(ing.nm_ingnio) }} ({{ ing.cd_ingnio }})
            </option>
          </select>
        </div>

        <button
          v-if="selectedIngenio"
          @click="openAddModal"
          class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-cenicana hover:bg-emerald-800 rounded-xl transition-all shadow-md shadow-emerald-950/10 cursor-pointer self-end"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nuevo Lote
        </button>
      </div>

      <!-- Listado de Lotes -->
      <div v-if="!selectedIngenio" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="p-4 bg-slate-50 rounded-full border border-slate-100 text-slate-400 mb-3 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Selecciona un ingenio</h3>
        <p class="text-xs text-slate-400 mt-1">Elige un ingenio de la lista para gestionar y visualizar sus lotes correspondientes.</p>
      </div>

      <div v-else-if="loading" class="flex justify-center py-16">
        <div class="animate-spin rounded-full h-8 w-8 border-2 border-cenicana border-t-transparent"></div>
      </div>

      <div v-else-if="lotes.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="p-4 bg-emerald-50 text-cenicana rounded-full border border-emerald-100 mb-3 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Sin lotes creados</h3>
        <p class="text-xs text-slate-400 mt-1">No se encontraron lotes registrados para este ingenio. ¡Crea el primero!</p>
      </div>

      <!-- Lotes Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="lote in lotes"
          :key="lote.id"
          class="bg-slate-50/50 border border-slate-100 hover:border-slate-200 rounded-2xl p-5 transition-all hover:shadow-md flex flex-col justify-between gap-4"
        >
          <div>
            <div class="flex justify-between items-start">
              <h3 class="text-base font-black text-slate-800 tracking-tight">{{ lote.nombre_lote }}</h3>
              <span
                class="px-2 py-0.5 rounded-full text-[9px] font-bold border"
                :class="[
                  lote.viveros_activos_count >= lote.capacidad_maxima
                    ? 'bg-red-50 text-red-700 border-red-100'
                    : 'bg-emerald-50 text-emerald-700 border-emerald-100'
                ]"
              >
                {{ lote.viveros_activos_count }} / {{ lote.capacidad_maxima }} Viveros
              </span>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Ingenio: {{ decodeHTMLEntities(selectedIngenioName) }}</p>

            <!-- ProgressBar -->
            <div class="w-full bg-slate-200 rounded-full h-1.5 mt-4 overflow-hidden">
              <div
                class="h-1.5 rounded-full transition-all duration-500"
                :class="[lote.viveros_activos_count >= lote.capacidad_maxima ? 'bg-red-500' : 'bg-cenicana']"
                :style="{ width: `${Math.min(100, (lote.viveros_activos_count / lote.capacidad_maxima) * 100)}%` }"
              ></div>
            </div>
          </div>

          <div class="flex justify-end gap-2 border-t border-slate-100 pt-3">
            <button
              @click="openEditModal(lote)"
              class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all"
              title="Editar lote"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </button>
            <button
              @click="deleteLote(lote)"
              class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
              title="Eliminar lote"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form (Crear / Editar) -->
    <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
          <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">
            {{ editingLoteId ? 'Editar Lote' : 'Agregar Lote' }}
          </h4>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl">&times;</button>
        </div>

        <!-- Body -->
        <form @submit.prevent="submitForm">
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nombre/Número del Lote</label>
              <input
                v-model="form.nombre_lote"
                type="text"
                placeholder="Ej: Lote A, Lote 1"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
              />
            </div>

            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Capacidad Máxima (Viveros)</label>
              <input
                v-model.number="form.capacidad_maxima"
                type="number"
                min="1"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
              />
            </div>
          </div>

          <!-- Footer -->
          <div class="border-t border-slate-100 p-5 bg-slate-50 flex justify-end gap-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2.5 text-xs font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 text-xs font-bold text-white bg-cenicana hover:bg-emerald-800 disabled:opacity-50 rounded-xl transition-all shadow-md shadow-emerald-950/10 cursor-pointer"
            >
              {{ saving ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import viverosServices from '@/services/viveros.services';
import { useToast } from 'vue-toastification';

const toast = useToast();

const ingenios = ref<any[]>([]);
const lotes = ref<any[]>([]);
const selectedIngenio = ref('');
const loading = ref(false);
const saving = ref(false);

const isModalOpen = ref(false);
const editingLoteId = ref<number | null>(null);

const form = ref({
  nombre_lote: '',
  capacidad_maxima: 5
});

const decodeHTMLEntities = (text: string) => {
  if (!text) return '';
  const textArea = document.createElement('textarea');
  textArea.innerHTML = text;
  return textArea.value;
};

const selectedIngenioName = computed(() => {
  const ing = ingenios.value.find(i => i.cd_ingnio === selectedIngenio.value);
  return ing ? ing.nm_ingnio : selectedIngenio.value;
});

const loadIngenios = async () => {
  try {
    const res = await viverosServices.getIngenios();
    ingenios.value = res.data;
  } catch (error) {
    console.error('Error fetching ingenios:', error);
    toast.error('Error al cargar la lista de ingenios');
  }
};

const loadLotes = async () => {
  if (!selectedIngenio.value) return;
  loading.value = true;
  try {
    const res = await viverosServices.getLotes({ ingenio_codigo: selectedIngenio.value });
    lotes.value = res.data;
  } catch (error) {
    console.error('Error fetching lotes:', error);
    toast.error('Error al cargar los lotes del ingenio');
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  editingLoteId.value = null;
  form.value = {
    nombre_lote: '',
    capacidad_maxima: 5
  };
  isModalOpen.value = true;
};

const openEditModal = (lote: any) => {
  editingLoteId.value = lote.id;
  form.value = {
    nombre_lote: lote.nombre_lote,
    capacidad_maxima: lote.capacidad_maxima
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const submitForm = async () => {
  saving.value = true;
  try {
    if (editingLoteId.value) {
      await viverosServices.updateLote(editingLoteId.value, form.value);
      toast.success('Lote actualizado correctamente');
    } else {
      await viverosServices.createLote({
        ...form.value,
        ingenio_codigo: selectedIngenio.value
      });
      toast.success('Lote creado correctamente');
    }
    isModalOpen.value = false;
    loadLotes();
  } catch (error: any) {
    console.error('Error saving lote:', error);
    const msg = error.response?.data?.message || 'Error al guardar el lote';
    toast.error(msg);
  } finally {
    saving.value = false;
  }
};

const deleteLote = async (lote: any) => {
  if (confirm(`¿Está seguro de que desea eliminar el lote "${lote.nombre_lote}"?`)) {
    try {
      await viverosServices.deleteLote(lote.id);
      toast.success('Lote eliminado correctamente');
      loadLotes();
    } catch (error: any) {
      console.error('Error deleting lote:', error);
      const msg = error.response?.data?.message || 'Error al eliminar el lote';
      toast.error(msg);
    }
  }
};

onMounted(() => {
  loadIngenios();
});
</script>
