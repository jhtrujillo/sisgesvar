<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 transform transition-all flex flex-col">
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white flex items-center justify-between rounded-t-3xl">
          <div>
            <h2 class="text-lg font-black text-white tracking-tight">Vincular Proyecto y Caracteres</h2>
            <p class="text-[11px] text-slate-300 mt-1">
              Selecciona el proyecto y los ambientes (caracteres) correspondientes a asignar a este vivero.
            </p>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-slate-400 hover:text-white rounded-full hover:bg-white/10 transition-colors"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 bg-slate-50 flex-1 overflow-visible space-y-6 min-h-[350px]">
          <!-- Seleccionar Proyecto -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">1. Seleccionar Proyecto</label>
            <div class="relative">
              <input
                type="text"
                v-model="searchProyecto"
                @focus="showProyectos = true"
                @blur="hideProyectosDelay"
                placeholder="Escribe para buscar un proyecto..."
                class="w-full bg-white border border-slate-200 text-slate-800 text-sm font-medium rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none shadow-sm"
                :disabled="isLoadingCaracteres"
              />
              <button
                v-if="selectedProyecto"
                @click="clearProyecto"
                type="button"
                class="absolute right-3 top-3.5 text-slate-400 hover:text-red-500 transition-colors"
              >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </button>
              
              <div
                v-if="showProyectos"
                class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
              >
                <div v-if="filteredProyectos.length === 0" class="cursor-default select-none py-3 px-4 text-slate-400 font-medium">
                  No se encontraron proyectos
                </div>
                <div
                  v-for="pry in filteredProyectos"
                  :key="pry.id_prycto"
                  @mousedown.prevent="selectProyecto(pry)"
                  class="cursor-pointer select-none py-3 px-4 hover:bg-slate-50 text-slate-700 font-medium transition-colors border-b border-slate-50 last:border-0"
                  :class="selectedProyecto?.id_prycto === pry.id_prycto ? 'bg-emerald-50 text-emerald-800 font-bold' : ''"
                >
                  {{ formatProjectName(pry) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Seleccionar Caracteres -->
          <div v-if="selectedProyecto" class="animate-fade-in">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">2. Seleccionar Caracteres (Ambientes)</label>
            
            <div v-if="isLoadingCaracteres" class="flex justify-center p-4">
              <div class="animate-spin rounded-full h-6 w-6 border-2 border-emerald-500 border-t-transparent"></div>
            </div>
            
            <div v-else class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
              <div v-if="caracteres.length === 0" class="text-sm text-slate-500 mb-3 text-center p-2 bg-slate-50 rounded-lg">
                No hay caracteres registrados para este proyecto.
              </div>
              
              <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 max-h-48 overflow-y-auto p-1">
                <label v-for="car in caracteres" :key="car.id" class="flex items-center p-3 border border-slate-100 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors" :class="selectedCaracteres.includes(car.id) ? 'bg-emerald-50/50 border-emerald-200' : ''">
                  <input type="checkbox" :value="car.id" v-model="selectedCaracteres" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                  <span class="ml-3 text-sm font-medium text-slate-700">{{ car.nombre }}</span>
                </label>
              </div>

              <!-- Crear nuevo -->
              <div class="border-t border-slate-100 pt-4 mt-2">
                <p class="text-xs text-slate-500 mb-2 font-medium">¿Falta alguno? Crear nuevo carácter:</p>
                <div class="flex gap-2">
                  <input
                    type="text"
                    v-model="nuevoCaracter"
                    @keyup.enter="crearCaracter"
                    placeholder="Nombre del nuevo carácter..."
                    class="flex-1 bg-slate-50 border border-slate-200 text-slate-800 text-sm font-medium rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                  />
                  <button
                    type="button"
                    @click="crearCaracter"
                    :disabled="!nuevoCaracter.trim() || isCreating"
                    class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <span v-if="isCreating">...</span>
                    <span v-else>Crear</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-5 bg-white border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
          <button
            @click="closeModal"
            class="px-5 py-2 text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors"
          >
            Cancelar
          </button>
          <button
            @click="confirmar"
            :disabled="!selectedProyecto"
            class="px-5 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm shadow-emerald-600/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
          >
            Agregar Proyecto
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useToast } from "vue-toastification";
import viverosServices from "@/services/viveros.services";

const props = defineProps<{
  isOpen: boolean;
  proyectos: any[];
}>();

const emit = defineEmits(["close", "confirm"]);
const toast = useToast();

const searchProyecto = ref("");
const showProyectos = ref(false);
const selectedProyecto = ref<any>(null);

const caracteres = ref<any[]>([]);
const selectedCaracteres = ref<number[]>([]);
const isLoadingCaracteres = ref(false);

const nuevoCaracter = ref("");
const isCreating = ref(false);

const hideProyectosDelay = () => {
  setTimeout(() => { showProyectos.value = false; }, 200);
};

const formatProjectName = (pry: any) => {
  let code = pry.cd_cntble;
  if (code && code.length === 6) {
    code = `${code.substring(0, 2)}.${code.substring(2, 4)}.${code.substring(4, 6)}`;
  }
  return code ? `${code} - ${pry.nm_prycto}` : pry.nm_prycto;
};

const filteredProyectos = computed(() => {
  if (searchProyecto.value === "") return props.proyectos;
  return props.proyectos.filter((pry) => {
    return formatProjectName(pry).toLowerCase().includes(searchProyecto.value.toLowerCase());
  });
});

const selectProyecto = async (pry: any) => {
  selectedProyecto.value = pry;
  searchProyecto.value = formatProjectName(pry);
  showProyectos.value = false;
  selectedCaracteres.value = [];
  
  await loadCaracteres(pry.id_prycto);
};

const clearProyecto = () => {
  selectedProyecto.value = null;
  searchProyecto.value = "";
  caracteres.value = [];
  selectedCaracteres.value = [];
  showProyectos.value = true;
};

const loadCaracteres = async (proyecto_id: number) => {
  isLoadingCaracteres.value = true;
  try {
    const res = await viverosServices.getCaracteresPorProyecto(proyecto_id);
    caracteres.value = res.data;
  } catch (error) {
    console.error("Error fetching caracteres:", error);
    toast.error("Error al cargar los caracteres del proyecto.");
  } finally {
    isLoadingCaracteres.value = false;
  }
};

const crearCaracter = async () => {
  if (!nuevoCaracter.value.trim() || !selectedProyecto.value) return;
  isCreating.value = true;
  try {
    const res = await viverosServices.createCaracter(selectedProyecto.value.id_prycto, {
      nombre: nuevoCaracter.value.trim()
    });
    const newCar = res.data;
    caracteres.value.push(newCar);
    selectedCaracteres.value.push(newCar.id);
    nuevoCaracter.value = "";
    toast.success("Carácter creado correctamente");
  } catch (error) {
    console.error("Error creating caracter:", error);
    toast.error("No se pudo crear el carácter.");
  } finally {
    isCreating.value = false;
  }
};

const closeModal = () => {
  emit("close");
};

const confirmar = () => {
  if (!selectedProyecto.value) return;
  emit("confirm", {
    proyecto: selectedProyecto.value,
    caracteres_ids: selectedCaracteres.value
  });
};

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    clearProyecto();
    showProyectos.value = false;
  }
});
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
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
