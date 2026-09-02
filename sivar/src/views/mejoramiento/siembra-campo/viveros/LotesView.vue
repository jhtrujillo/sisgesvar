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

      <BaseButton variant="secondary" size="sm" :to="{ name: 'siembra_campo_viveros.show' }">
        <template #icon-left>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </template>
        Volver a Viveros
      </BaseButton>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
      <!-- Selector de Ingenio, Hacienda y Botón Agregar -->
      <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full md:max-w-2xl">
          <div class="w-full sm:max-w-xs">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Seleccionar Ingenio</label>
            <select
              v-model="selectedIngenio"
              @change="loadHaciendas"
              class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
            >
              <option value="">Seleccione un ingenio...</option>
              <option v-for="ing in ingenios" :key="ing.cd_ingnio" :value="ing.cd_ingnio">{{ decodeHTMLEntities(ing.nm_ingnio) }} ({{ ing.cd_ingnio }})</option>
            </select>
          </div>

          <div class="w-full sm:max-w-xs" v-if="selectedIngenio">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Seleccionar Hacienda</label>
            <select
              v-model="selectedHacienda"
              @change="loadLotes"
              class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
            >
              <option value="">Seleccione una hacienda...</option>
              <option v-for="hda in haciendas" :key="hda.cd_hcnda" :value="hda.cd_hcnda">
                {{ decodeHTMLEntities(hda.nm_hcnda) }}
              </option>
            </select>
          </div>
        </div>

        <BaseButton v-if="selectedIngenio && selectedHacienda" variant="primary" size="sm" @click="openAddModal" class="self-end md:self-auto">
          <template #icon-left>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </template>
          Nuevo Lote
        </BaseButton>
      </div>

      <!-- Listado de Lotes -->
      <div v-if="!selectedIngenio" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="p-4 bg-slate-50 rounded-full border border-slate-100 text-slate-400 mb-3 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
            />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Selecciona un ingenio</h3>
        <p class="text-xs text-slate-400 mt-1">Elige un ingenio de la lista para gestionar y visualizar sus lotes correspondientes.</p>
      </div>

      <div v-else-if="!selectedHacienda" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="p-4 bg-slate-50 rounded-full border border-slate-100 text-slate-400 mb-3 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
            />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Selecciona una hacienda</h3>
        <p class="text-xs text-slate-400 mt-1">Elige una hacienda para cargar y administrar sus lotes físicos.</p>
      </div>

      <div v-else-if="loading" class="flex justify-center py-16">
        <div class="animate-spin rounded-full h-8 w-8 border-2 border-cenicana border-t-transparent"></div>
      </div>

      <div v-else-if="lotes.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="p-4 bg-emerald-50 text-cenicana rounded-full border border-emerald-100 mb-3 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
            />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Sin lotes creados</h3>
        <p class="text-xs text-slate-400 mt-1">No se encontraron lotes registrados para esta hacienda. ¡Crea el primero!</p>
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
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">
              Ingenio: {{ decodeHTMLEntities(selectedIngenioName) }} <br />
              Hacienda: {{ decodeHTMLEntities(getHaciendaName(lote.hacienda_codigo)) }} <br />
              Parcelas/Vivero: {{ lote.total_parcelas_vivero ?? 10 }}
            </p>

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
            <BaseButton variant="ghost" size="xs" iconOnly @click="openEditModal(lote)" title="Editar lote" class="hover:text-blue-600 hover:bg-blue-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                />
              </svg>
            </BaseButton>
            <BaseButton variant="ghost" size="xs" iconOnly @click="deleteLote(lote)" title="Eliminar lote" class="hover:text-red-600 hover:bg-red-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
              </svg>
            </BaseButton>
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
            {{ editingLoteId ? "Editar Lote" : "Agregar Lote" }}
          </h4>
          <BaseButton variant="ghost" size="xs" iconOnly @click="closeModal" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</BaseButton>
        </div>

        <!-- Body -->
        <form @submit.prevent="submitForm">
          <div class="p-6 space-y-4">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs font-semibold text-slate-600">
              <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1 text-[9px]">Ubicación Física</span>
              Ingenio: {{ decodeHTMLEntities(selectedIngenioName) }} <br />
              Hacienda: {{ decodeHTMLEntities(selectedHaciendaName) }}
            </div>

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

            <!-- Individual Vivero Parcel capacity list -->
            <div class="space-y-3">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Configuración de Viveros</label>
              <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                <div
                  v-for="i in form.capacidad_maxima"
                  :key="'vivero_p_' + i"
                  class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 flex items-center gap-3"
                >
                  <span class="text-[10px] font-black text-slate-400 uppercase w-12 shrink-0">Vivero {{ i }}</span>
                  <input
                    v-model="form.nombres_por_vivero[i]"
                    type="text"
                    :placeholder="`Nombre (ej. V-${i})`"
                    class="flex-1 bg-white border border-slate-200 text-slate-800 text-xs font-semibold rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
                  />
                  <input
                    v-model.number="form.parcelas_por_vivero[i]"
                    type="number"
                    min="1"
                    required
                    title="Parcelas"
                    class="w-16 shrink-0 bg-white border border-slate-200 text-slate-800 text-xs font-black text-center rounded-lg py-1.5 focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none"
                  />
                </div>
              </div>
              <p class="text-[10px] text-slate-400">
                <strong>Nombre:</strong> identificador o consecutivo del vivero (opcional). <strong>Número:</strong> total de parcelas.
              </p>
            </div>
          </div>

          <!-- Footer -->
          <div class="border-t border-slate-100 p-5 bg-slate-50 flex justify-end gap-2">
            <BaseButton variant="secondary" size="sm" @click="closeModal"> Cancelar </BaseButton>
            <BaseButton type="submit" variant="primary" size="sm" :loading="saving"> Guardar </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import viverosServices from "@/services/viveros.services";
import { useToast } from "vue-toastification";

const toast = useToast();

const ingenios = ref<any[]>([]);
const haciendas = ref<any[]>([]);
const lotes = ref<any[]>([]);
const selectedIngenio = ref("");
const selectedHacienda = ref("");
const loading = ref(false);
const saving = ref(false);

const isModalOpen = ref(false);
const editingLoteId = ref<number | null>(null);

const form = ref({
  nombre_lote: "",
  capacidad_maxima: 5,
  total_parcelas_vivero: 10,
  hacienda_codigo: "",
  parcelas_por_vivero: {} as Record<number, number>,
  nombres_por_vivero: {} as Record<number, string>
});

const syncParcelasPorVivero = () => {
  const val = parseInt(form.value.capacidad_maxima as any);
  if (isNaN(val) || val < 1) return;
  if (!form.value.parcelas_por_vivero) {
    form.value.parcelas_por_vivero = {};
  }
  if (!form.value.nombres_por_vivero) {
    form.value.nombres_por_vivero = {};
  }
  for (let i = 1; i <= val; i++) {
    if (form.value.parcelas_por_vivero[i] === undefined || form.value.parcelas_por_vivero[i] === null) {
      form.value.parcelas_por_vivero[i] = form.value.total_parcelas_vivero || 10;
    }
    // Keep existing name if already set, otherwise leave empty (user fills it)
    if (form.value.nombres_por_vivero[i] === undefined) {
      form.value.nombres_por_vivero[i] = "";
    }
  }
  Object.keys(form.value.parcelas_por_vivero).forEach((key) => {
    const k = parseInt(key);
    if (k > val) {
      delete form.value.parcelas_por_vivero[k];
      delete form.value.nombres_por_vivero[k];
    }
  });
};

watch(
  () => form.value.capacidad_maxima,
  () => {
    syncParcelasPorVivero();
  }
);

const decodeHTMLEntities = (text: string) => {
  if (!text) return "";
  const textArea = document.createElement("textarea");
  textArea.innerHTML = text;
  return textArea.value;
};

const selectedIngenioName = computed(() => {
  const ing = ingenios.value.find((i) => i.cd_ingnio === selectedIngenio.value);
  return ing ? ing.nm_ingnio : selectedIngenio.value;
});

const selectedHaciendaName = computed(() => {
  const hda = haciendas.value.find((h) => h.cd_hcnda === selectedHacienda.value);
  return hda ? hda.nm_hcnda : selectedHacienda.value;
});

const getHaciendaName = (code: string) => {
  const hda = haciendas.value.find((h) => h.cd_hcnda === code);
  return hda ? hda.nm_hcnda : code;
};

const loadIngenios = async () => {
  try {
    const res = await viverosServices.getIngenios();
    ingenios.value = res.data;
  } catch (error) {
    console.error("Error fetching ingenios:", error);
    toast.error("Error al cargar la lista de ingenios");
  }
};

const loadHaciendas = async () => {
  haciendas.value = [];
  selectedHacienda.value = "";
  lotes.value = [];
  if (!selectedIngenio.value) return;

  try {
    const res = await viverosServices.getHaciendas(selectedIngenio.value);
    haciendas.value = res.data;
  } catch (error) {
    console.error("Error fetching haciendas:", error);
    toast.error("Error al cargar las haciendas");
  }
};

const loadLotes = async () => {
  if (!selectedIngenio.value || !selectedHacienda.value) {
    lotes.value = [];
    return;
  }
  loading.value = true;
  try {
    const res = await viverosServices.getLotes({
      ingenio_codigo: selectedIngenio.value,
      hacienda_codigo: selectedHacienda.value
    });
    lotes.value = res.data;
  } catch (error) {
    console.error("Error fetching lotes:", error);
    toast.error("Error al cargar los lotes");
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  editingLoteId.value = null;
  form.value = {
    nombre_lote: "",
    capacidad_maxima: 5,
    total_parcelas_vivero: 10,
    hacienda_codigo: selectedHacienda.value,
    parcelas_por_vivero: {},
    nombres_por_vivero: {}
  };
  syncParcelasPorVivero();
  isModalOpen.value = true;
};

const openEditModal = (lote: any) => {
  editingLoteId.value = lote.id;
  const pMap: Record<number, number> = {};
  const nMap: Record<number, string> = {};
  if (lote.viveros && lote.viveros.length > 0) {
    lote.viveros.forEach((v: any) => {
      const pos = v.consecutivo_vivero_ingenio;
      pMap[pos] = v.total_parcelas || 10;
      // Load existing custom name, but skip default auto-generated names
      const defaultName = `Vivero ${pos}`;
      nMap[pos] = v.nombre && v.nombre !== defaultName && v.nombre !== v.identificador_unico ? v.nombre : "";
    });
  }
  form.value = {
    nombre_lote: lote.nombre_lote,
    capacidad_maxima: lote.capacidad_maxima,
    total_parcelas_vivero: lote.total_parcelas_vivero || 10,
    hacienda_codigo: lote.hacienda_codigo || selectedHacienda.value,
    parcelas_por_vivero: pMap,
    nombres_por_vivero: nMap
  };
  syncParcelasPorVivero();
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
      toast.success("Lote actualizado correctamente");
    } else {
      await viverosServices.createLote({
        ...form.value,
        ingenio_codigo: selectedIngenio.value
      });
      toast.success("Lote creado correctamente");
    }
    isModalOpen.value = false;
    loadLotes();
  } catch (error: any) {
    console.error("Error saving lote:", error);
    let msg = error.response?.data?.message || "Error al guardar el lote";
    if (typeof msg === "string" && (msg.includes("SQLSTATE") || msg.includes("Unique violation") || msg.includes("duplicate key"))) {
      msg = "Ya existe un lote o vivero registrado con ese nombre o identificador en la hacienda seleccionada.";
    }
    toast.error(msg);
  } finally {
    saving.value = false;
  }
};

const deleteLote = async (lote: any) => {
  if (confirm(`¿Está seguro de que desea eliminar el lote "${lote.nombre_lote}"?`)) {
    try {
      await viverosServices.deleteLote(lote.id);
      toast.success("Lote eliminado correctamente");
      loadLotes();
    } catch (error: any) {
      console.error("Error deleting lote:", error);
      const msg = error.response?.data?.message || "Error al eliminar el lote";
      toast.error(msg);
    }
  }
};

onMounted(() => {
  loadIngenios();
});
</script>
